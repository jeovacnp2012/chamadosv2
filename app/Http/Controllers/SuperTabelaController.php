<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;
use App\Models\Called;

class SuperTabelaController extends Controller
{
    public function show(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $query = Called::with(['user', 'sector', 'interactions']);
        $filters = $request->input('tableFilters', []);

        $titulo = 'Todos os chamados';

        // Início da construção do título
        $tituloPartes = [];

        // Status
        if (isset($filters['status_aberto']['value'])) {
            $valor = $filters['status_aberto']['value'];
            if ($valor === 'A' || $valor === 'abertos') {
                $query->whereNull('closing_date');
                $tituloPartes[] = 'Abertos';
            } elseif ($valor === 'F' || $valor === 'fechados') {
                $query->whereNotNull('closing_date');
                $tituloPartes[] = 'Fechados';
            }
        }

        // Setor(es)
        if (!empty($filters['sector']['sector_ids'])) {
            $query->whereIn('sector_id', $filters['sector']['sector_ids']);

            $nomes = \App\Models\Sector::whereIn('id', $filters['sector']['sector_ids'])->pluck('name')->toArray();
            $tituloPartes[] = 'dos setores ' . implode(', ', $nomes);
        }

        // Data de fechamento
        if (isset($filters['closing_date']['closing_from'])) {
            $query->whereDate('closing_date', '>=', $filters['closing_date']['closing_from']);
            $tituloPartes[] = 'fechados de ' . date('d/m/Y', strtotime($filters['closing_date']['closing_from']));
        }
        if (isset($filters['closing_date']['closing_until'])) {
            $query->whereDate('closing_date', '<=', $filters['closing_date']['closing_until']);
            $tituloPartes[] = 'até ' . date('d/m/Y', strtotime($filters['closing_date']['closing_until']));
        }

        // Data de criação
        if (isset($filters['created_at']['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_at']['created_from']);
            $tituloPartes[] = 'criados de ' . date('d/m/Y', strtotime($filters['created_at']['created_from']));
        }
        if (isset($filters['created_at']['created_until'])) {
            $query->whereDate('created_at', '<=', $filters['created_at']['created_until']);
            $tituloPartes[] = 'até ' . date('d/m/Y', strtotime($filters['created_at']['created_until']));
        }

        // Monta título final
        if (count($tituloPartes)) {
            $titulo = 'Chamados ' . implode(' ', $tituloPartes);
        }

        $calleds = $query->get();

        return view('supertabela', [
            'calleds' => $calleds,
            'titulo' => $titulo,
        ]);
    }


    public function chamadosAbertos(Request $request)
    {
        // Verificar se o usuário está autenticado
        if (!auth()->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        // Filtrar chamados abertos: closing_date null E status aberto
        $calleds = Called::with(['user', 'sector', 'interactions'])
            ->whereNull('closing_date')
            ->where('status', 'A') // ou 'open' dependendo do seu banco
            ->get();

        $pageTitle = 'Chamados Abertos';
        $filterType = 'abertos';

        return view('supertabela', compact('calleds', 'pageTitle', 'filterType'));
    }

    public function chamadosFechados(Request $request)
    {
        // Verificar se o usuário está autenticado
        if (!auth()->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        // Filtrar chamados fechados: closing_date NOT null E status fechado
        $calleds = Called::with(['user', 'sector', 'interactions'])
            ->whereNotNull('closing_date')
            ->where('status', 'F') // ou 'closed' dependendo do seu banco
            ->get();

        $pageTitle = 'Chamados Fechados';
        $filterType = 'fechados';

        return view('supertabela', compact('calleds', 'pageTitle', 'filterType'));
    }
}
