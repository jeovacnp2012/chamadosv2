<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Called;
use App\Models\Interaction;

Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['error' => 'Credenciais inválidas'], 401);
    }

    return response()->json([
        'token' => $user->createToken('app-token')->plainTextToken,
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/calleds', function (Request $request) {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 2);
        $query = Called::with(['user', 'sector', 'interactions']);
        $filters = $request->input('tableFilters', []);

        // 🔍 Status aberto/fechado
        if (!empty($filters['status_aberto']['value'])) {
            if ($filters['status_aberto']['value'] === 'abertos') {
                $query->whereNull('closing_date');
            } elseif ($filters['status_aberto']['value'] === 'fechados') {
                $query->whereNotNull('closing_date');
            }
        }

        // 🔍 Múltiplos setores
        if (!empty($filters['sector']['sector_ids'])) {
            $query->whereIn('sector_id', $filters['sector']['sector_ids']);
        }

        // 🔍 Datas de criação
        if (!empty($filters['created_at']['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_at']['created_from']);
        }

        if (!empty($filters['created_at']['created_until'])) {
            $query->whereDate('created_at', '<=', $filters['created_at']['created_until']);
        }

        // 🔍 Datas de fechamento
        if (!empty($filters['closing_date']['closing_from'])) {
            $query->whereDate('closing_date', '>=', $filters['closing_date']['closing_from']);
        }

        if (!empty($filters['closing_date']['closing_until'])) {
            $query->whereDate('closing_date', '<=', $filters['closing_date']['closing_until']);
        }

        // 🔍 Protocolo (LIKE)
        if (!empty($filters['protocolo']['value'])) {
            $query->where('protocolo', 'like', '%' . $filters['protocolo']['value'] . '%');
        }

        // 🔍 Texto do problema (LIKE)
        if (!empty($filters['problem']['value'])) {
            $query->where('problem', 'like', '%' . $filters['problem']['value'] . '%');
        }

        return response()->json($query->paginate($perPage, ['*'], 'page', $page));
    });

    // 💬 Envio de nova interação
    Route::post('/calleds/{called}/interactions', function (Request $request, Called $called) {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $interaction = new Interaction([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        $called->interactions()->save($interaction);

        return response()->json(['success' => true, 'interaction' => $interaction], 201);
    });
    Route::get('/calleds/{called}', function (Called $called) {
        return response()->json(
            $called->load(['user', 'sector', 'interactions'])
        );
    });
});
