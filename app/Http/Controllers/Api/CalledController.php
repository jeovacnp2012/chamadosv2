<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Called;
use Illuminate\Http\Request;

class CalledController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Somente se tiver papel autorizado
        if (! $user->hasAnyRole(['Executor', 'Solicitante'])) {
            return response()->json(['error' => 'Não autorizado.'], 403);
        }

        $query = Called::query()->with(['sector', 'user', 'interactions']);

        // Filtros opcionais
        if ($request->filled('status')) {
            if ($request->status === 'A') {
                $query->whereNull('closing_date');
            } elseif ($request->status === 'F') {
                $query->whereNotNull('closing_date');
            }
        }

        if ($request->filled('sector_id')) {
            $query->where('sector_id', $request->sector_id);
        }

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }

        if ($request->filled('created_until')) {
            $query->whereDate('created_at', '<=', $request->created_until);
        }

        return response()->json($query->get());
    }
}
