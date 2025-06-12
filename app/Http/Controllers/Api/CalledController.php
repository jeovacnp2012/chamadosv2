<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Called;
use App\Models\Interaction;
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

        \Log::info('=== INICIO DEBUG API ===');
        \Log::info('API - Usuário e parâmetros recebidos:', [
            'user_id' => $user->id,
            'user_roles' => $user->getRoleNames(),
            'todos_parametros' => $request->all(),
            'status_recebido' => $request->get('status'),
            'status_filled' => $request->filled('status'),
            'query_string' => $request->getQueryString()
        ]);

        $query = Called::query();

        // SUPER ADMIN vê tudo
        if ($user->hasRole('Super Admin')) {
            \Log::info('API - Super Admin: sem filtros de permissão');
        }
        // EXECUTOR vê apenas os chamados que ele executa
        elseif ($user->hasRole('Executor') && $user->supplier_id) {
            \Log::info('API - Executor: filtrando por supplier_id', ['supplier_id' => $user->supplier_id]);
            $query->where('supplier_id', $user->supplier_id);
        }
        // Demais perfis → setores dos seus departamentos
        else {
            $userSectors = collect($user->departaments)->flatMap->sectors->pluck('id');
            \Log::info('API - Outros perfis: filtrando por setores', [
                'company_id' => $user->company_id,
                'sector_ids' => $userSectors->toArray()
            ]);
            $query->where('company_id', $user->company_id)
                ->whereIn('sector_id', $userSectors);
        }

        // *** FILTRO PADRÃO: ABERTOS ***
        $statusFilter = $request->get('status', 'A'); // Padrão é 'A' (Abertos)

        \Log::info('API - Aplicando filtro de status', [
            'status_solicitado' => $request->get('status'),
            'status_aplicado' => $statusFilter
        ]);

        $query->where('status', $statusFilter);

        // Outros filtros opcionais
        if ($request->filled('search')) {
            \Log::info('API - Aplicando filtro de busca', ['search' => $request->search]);
            $query->where('protocol', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('from') && $request->filled('to')) {
            \Log::info('API - Aplicando filtro de data', [
                'from' => $request->from,
                'to' => $request->to
            ]);
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        // Log da query SQL antes de executar
        \Log::info('API - Query SQL que será executada:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        $results = $query->with([
            'user:id,name',
            'sector:id,name',
            'supplier:id,trade_name',
            'patrimony:id,tag',
            'interactions',
        ])->latest()->paginate(10);

        \Log::info('API - Resultado da consulta:', [
            'total_encontrado' => $results->total(),
            'count_pagina_atual' => $results->count(),
            'status_distribution' => $results->items() ? array_count_values(array_column($results->items(), 'status')) : [],
            'primeiros_3_status' => array_slice(array_column($results->items(), 'status'), 0, 3),
            'estrutura_primeiro_item' => $results->items() ? array_keys($results->items()[0]->toArray()) : []
        ]);
        \Log::info('=== FIM DEBUG API ===');

        return $results;
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
}
