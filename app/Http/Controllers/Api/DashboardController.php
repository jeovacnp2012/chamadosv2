<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Called;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function contagem(Request $request)
    {
        $user = Auth::user();
        $hoje = Carbon::today();

        // Base das queries
        $queryTotais = Called::query();
        $queryHoje   = Called::query()->whereDate('created_at', $hoje);

        // Chama a API do Web para buscar os dados dinâmicos do dashboard
        $response = Http::withToken(session('api_token'))
            ->get(config('services.api.base_url') . '/api/v1/chamados/contagem');
        \Log::info('Resposta bruta do dashboard:', $response->json());

        // Filtragem por perfil
        if ($user->hasRole('Super Admin')) {
            // Super Admin vê tudo
            // Sem filtro extra
        } elseif ($user->hasRole('Gerente')) {
            $departamentIds = $user->departaments->pluck('id')->toArray();
            $queryTotais->whereIn('departament_id', $departamentIds);
            $queryHoje->whereIn('departament_id', $departamentIds);
        } elseif ($user->hasRole('Operador')) {
            $sectorIds = $user->sectors->pluck('id')->toArray();
            $queryTotais->whereIn('sector_id', $sectorIds);
            $queryHoje->whereIn('sector_id', $sectorIds);
        }

        // Códigos dos status: 'A' = Aberto, 'F' = Fechado, 'P' = Em Pendente
        $totais = [
            'abertos'   => (clone $queryTotais)->where('status', 'A')->count(),
            'fechados'  => (clone $queryTotais)->where('status', 'F')->count(),
            'total'     => (clone $queryTotais)->count(),
        ];
        $hoje = [
            'abertos'   => (clone $queryHoje)->where('status', 'A')->count(),
            'fechados'  => (clone $queryHoje)->where('status', 'F')->count(),
            'total'     => (clone $queryHoje)->count(),
        ];
        \Log::info('API Response:', $response->json());
        return response()->json([
            'totais' => $totais,
            'hoje'   => $hoje,
        ]);
    }
}
