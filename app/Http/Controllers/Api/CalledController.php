<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Called;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CalledController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas',
            ], 401);
        }

        // Revoga tokens anteriores se quiser
        $user->tokens()->delete();

        // Cria novo token
        $token = $user->createToken('app-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(), // Spatie: retorna array de roles
            ],
        ]);
    }
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Called::query();

        // SUPER ADMIN vê tudo
        if ($user->hasRole('Super Admin')) {
            // sem filtros
        }
        // EXECUTOR vê apenas os chamados que ele executa
        elseif ($user->hasRole('Executor') && $user->supplier_id) {
            $query->where('supplier_id', $user->supplier_id);
        }
        // Demais perfis → setores dos seus departamentos
        else {
            $query->where('company_id', $user->company_id)
                ->whereIn('sector_id', collect($user->departaments)->flatMap->sectors->pluck('id'));
        }

        // Filtros opcionais via query string
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('protocol', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        return $query->with([
            'user:id,name',
            'sector:id,name',
            'supplier:id,trade_name',
            'patrimony:id,tag',
            'interactions',
        ])->latest()->paginate(10);
    }
    public function show(Request $request, Called $called)
    {
        // 🔐 Verifica se o usuário tem acesso ao setor do chamado
        if (
            !$request->user()->hasRole('Super Admin') &&
            !$request->user()->sectors->pluck('id')->contains($called->sector_id)
        ) {
            return response()->json([
                'error' => 'Você não tem permissão para visualizar este chamado.'
            ], 403);
        }

        // 🔄 Carrega relacionamentos
        $called->load([
            'user',
            'sector',
            'supplier',
            'patrimony',
            'calledType',
            'interactions.user' // inclui quem enviou a interação
        ]);

        return response()->json([
            'success' => true,
            'data' => $called,
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'protocol'         => 'required|string|max:8|unique:calleds,protocol',
                'problem'          => 'required|string',
                'sector_id'        => 'required|exists:sectors,id',
                'user_id'          => 'required|exists:users,id',
                'called_type_id'   => 'required|exists:called_types,id',
                'supplier_id'      => 'required|exists:suppliers,id',
                'patrimony_id'     => 'required|exists:patrimonies,id',
                'type_maintenance' => 'required|string|size:1|in:C,P',
            ],
            [
                'protocol.required' => 'O campo protocolo é obrigatório.',
                'protocol.unique'   => 'Este protocolo já está em uso.',
                'protocol.max'      => 'O protocolo deve ter no máximo 8 caracteres.',
                'problem.required' => 'A descrição do problema é obrigatória.',
                'sector_id.required' => 'O setor responsável é obrigatório.',
                'sector_id.exists'   => 'O setor informado não existe.',
                'user_id.required' => 'O usuário responsável é obrigatório.',
                'user_id.exists'   => 'O usuário informado não existe.',
                'called_type_id.required' => 'O tipo do chamado é obrigatório.',
                'called_type_id.exists'   => 'O tipo do chamado informado não existe.',
                'supplier_id.required' => 'O executor (fornecedor) é obrigatório.',
                'supplier_id.exists'   => 'O fornecedor informado não existe.',
                'patrimony_id.required' => 'O patrimônio vinculado é obrigatório.',
                'patrimony_id.exists'   => 'O patrimônio informado não existe.',
                'type_maintenance.required' => 'O tipo de manutenção é obrigatório.',
                'type_maintenance.string'   => 'O tipo de manutenção deve ser uma letra.',
                'type_maintenance.size'     => 'O tipo de manutenção deve conter apenas 1 caractere.',
                'type_maintenance.in'       => 'O tipo de manutenção deve ser "C" (Corretiva) ou "P" (Preventiva).',
            ]
        );

        $called = \App\Models\Called::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Chamado criado com sucesso.',
            'data' => $called->load(['user', 'sector', 'supplier', 'calledType', 'patrimony']),
        ], 201);
    }
    public function update(Request $request, \App\Models\Called $called)
    {
        if (
            !$request->user()->hasRoleApi('Super Admin') &&
            !$request->user()->sectors->pluck('id')->contains($called->sector_id)
        ) {
            return response()->json([
                'error' => 'Você não tem permissão para modificar este chamado.'
            ], 403);
        }
        $validated = $request->validate(
            [
                'protocol'         => 'sometimes|required|string|max:255|unique:calleds,protocol,' . $called->id,
                'problem'          => 'sometimes|required|string',
                'sector_id'        => 'sometimes|required|exists:sectors,id',
                'user_id'          => 'sometimes|required|exists:users,id',
                'called_type_id'   => 'sometimes|required|exists:called_types,id',
                'supplier_id'      => 'sometimes|required|exists:suppliers,id',
                'patrimony_id'     => 'sometimes|required|exists:patrimonies,id',
                'type_maintenance' => 'sometimes|required|string|size:1|in:C,P',
            ],
            [
                'protocol.required' => 'O campo protocolo é obrigatório.',
                'protocol.unique'   => 'Este protocolo já está em uso por outro chamado.',
                'protocol.max'      => 'O protocolo deve ter no máximo 255 caracteres.',

                'problem.required' => 'A descrição do problema é obrigatória.',

                'sector_id.required' => 'O setor responsável é obrigatório.',
                'sector_id.exists'   => 'O setor informado não existe.',

                'user_id.required' => 'O usuário responsável é obrigatório.',
                'user_id.exists'   => 'O usuário informado não existe.',

                'called_type_id.required' => 'O tipo do chamado é obrigatório.',
                'called_type_id.exists'   => 'O tipo do chamado informado não existe.',

                'supplier_id.required' => 'O executor (fornecedor) é obrigatório.',
                'supplier_id.exists'   => 'O fornecedor informado não existe.',

                'patrimony_id.required' => 'O patrimônio vinculado é obrigatório.',
                'patrimony_id.exists'   => 'O patrimônio informado não existe.',

                'type_maintenance.required' => 'O tipo de manutenção é obrigatório.',
                'type_maintenance.string'   => 'O tipo de manutenção deve ser uma letra.',
                'type_maintenance.size'     => 'O tipo de manutenção deve conter apenas 1 caractere.',
                'type_maintenance.in'       => 'O tipo de manutenção deve ser "C" (Corretiva) ou "P" (Preventiva).',
            ]
        );

        $called->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Chamado atualizado com sucesso.',
            'data'    => $called->load(['user', 'sector', 'supplier', 'patrimony']),
        ]);
    }
    public function destroy(Request $request, Called $called)
    {
        if (
            !$request->user()->hasRoleApi('Super Admin') &&
            !$request->user()->sectors->pluck('id')->contains($called->sector_id)
        ) {
            return response()->json([
                'error' => 'Você não tem permissão para modificar este chamado.'
            ], 403);
        }
        // Proteção: só exclui se não tiver interações
        if ($called->interactions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Este chamado possui interações vinculadas e não pode ser excluído.',
            ], 403);
        }
        $called->delete();
        return response()->json([
            'success' => true,
            'message' => 'Chamado excluído com sucesso.',
        ]);
    }
    public function storeInteraction(Request $request, Called $called)
    {
        if (
            !$request->user()->hasRoleApi('Super Admin') &&
            !$request->user()->sectors->pluck('id')->contains($called->sector_id)
        ) {
            return response()->json(['error' => 'Você não tem permissão para interagir com este chamado.'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'attachment_path' => 'nullable|file|max:10240',
        ], [
            'message.required' => 'A mensagem é obrigatória.',
            'attachment_path.file' => 'O anexo deve ser um arquivo válido.',
            'attachment_path.max' => 'O anexo não pode ser maior que 10MB.',
        ]);

        $interaction = new \App\Models\Interaction([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        if ($request->hasFile('attachment_path')) {
            $path = $request->file('attachment_path')->store('attachments', 'public');
            $interaction->attachment_path = $path;
        }

        $called->interactions()->save($interaction);

        return response()->json([
            'success' => true,
            'interaction' => $interaction->load('user')
        ], 201);
    }
}
