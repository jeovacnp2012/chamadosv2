<?php

namespace App\Exports;

use App\Models\Called;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CalledsExport implements FromQuery, WithHeadings, WithMapping
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Called::query()->with(['sector', 'supplier']);

        $filters = $this->request->input('tableFilters', []);

        if (isset($filters['status_aberto']['value'])) {
            if ($filters['status_aberto']['value'] === 'abertos') {
                $query->where('status', 'A');
            } elseif ($filters['status_aberto']['value'] === 'fechados') {
                $query->where('status', 'F');
            }
        }

        if (!empty($filters['sector']['sector_id'])) {
            $query->where('sector_id', $filters['sector']['sector_id']);
        }

        return $query;
    }

    public function map($called): array
    {
        return [
            $called->protocolo,
            match ($called->status) {
                'A' => 'Aberto',
                'E' => 'Em andamento',
                'F' => 'Finalizado',
                default => 'Desconhecido',
            },
            $called->sector?->name ?? '---',
            $called->supplier?->trade_name ?? '---',
            $called->created_at->format('d/m/Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'Protocolo',
            'Status',
            'Setor',
            'Executor',
            'Data de Criação',
        ];
    }
}
