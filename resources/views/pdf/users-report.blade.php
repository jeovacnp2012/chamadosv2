<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @php
        use Illuminate\Support\Str;
    @endphp
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4A90E2;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            color: #4A90E2;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 14px;
            color: #666;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .info-item {
            flex: 1;
        }

        .info-label {
            font-weight: bold;
            color: #4A90E2;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .users-table th {
            background-color: #4A90E2;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        .users-table td {
            padding: 10px 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .users-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .users-table tr:hover {
            background-color: #e3f2fd;
        }

        .role-badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #4A90E2;
            color: white;
            border-radius: 12px;
            font-size: 10px;
            margin: 2px;
        }

        .status-active {
            color: #28a745;
            font-weight: bold;
        }

        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }

        /* ✅ ESTILOS PARA AS ESTATÍSTICAS */
        .statistics-section {
            border: 2px solid #4A90E2;
            padding: 20px;
            margin-top: 30px;
            background-color: #f8f9fa;
            page-break-before: always;
        }

        .statistics-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            color: #4A90E2;
            text-transform: uppercase;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-item {
            text-align: center;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #4A90E2;
        }

        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .departments-section, .sectors-section {
            margin-top: 20px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #4A90E2;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .stats-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .stat-line {
            padding: 8px;
            background-color: white;
            border-radius: 3px;
            border-left: 3px solid #4A90E2;
            font-size: 11px;
        }

        .stat-name {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .stat-numbers {
            color: #666;
        }

        .user-type-info {
            text-align: right;
            font-size: 10px;
            color: #666;
            margin-top: 15px;
            font-style: italic;
        }

        /* Para quebras de página específicas */
        @media print {
            .users-table {
                font-size: 11px;
            }

            .users-table th,
            .users-table td {
                padding: 8px 6px;
            }

            .statistics-section {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
{{-- Cabeçalho --}}
<div class="header">
    <h1>{{ $title }}</h1>
    <div class="subtitle">{{ $subtitle ?? 'Sistema de Gerenciamento de Usuários' }}</div>
    @if(!empty($search_term))
        <div style="margin-top: 5px; font-size: 12px; color: #666;">
            Filtrado por busca: "{{ $search_term }}"
        </div>
    @endif
    @if(!empty($filter_info))
        <div style="margin-top: 5px; font-size: 12px; color: #666;">
            Filtros aplicados: {{ $filter_info }}
        </div>
    @endif
</div>

{{-- Informações do Relatório --}}
<div class="info-section">
    <div class="info-item">
        <div class="info-label">Total de Usuários:</div>
        <div>{{ $total_users }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">Gerado em:</div>
        <div>{{ $generated_at }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">Gerado por:</div>
        <div>{{ $generated_by }}</div>
    </div>
</div>

{{-- Tabela de Usuários --}}
@if($users->count() > 0)
    <table class="users-table">
        <thead>
        <tr>
            <th style="width: 22%">Nome</th>
            <th style="width: 25%">E-mail</th>
            <th style="width: 20%">Departamento Principal</th>
            <th style="width: 20%">Setor Principal</th>
            <th style="width: 13%">Perfil</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>
                    <strong>{{ $user->name }}</strong>
                    @if(!$user->is_active)
                        <br><small class="status-inactive">(Inativo)</small>
                    @endif
                </td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->departaments->isNotEmpty())
                        {{ $user->departaments->first()->name }}
                    @else
                        <span style="color: #999; font-style: italic;">Não informado</span>
                    @endif
                </td>
                <td>
                    @if($user->sectors->isNotEmpty())
                        {{ $user->sectors->first()->name }}
                    @else
                        <span style="color: #999; font-style: italic;">Não informado</span>
                    @endif
                </td>
                <td>
                    @if($user->roles->isNotEmpty())
                        @foreach($user->roles as $role)
                            <span class="role-badge">{{ $role->name }}</span>
                        @endforeach
                    @else
                        <span style="color: #999; font-style: italic;">Sem perfil</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="no-data">
        <h3>Nenhum usuário encontrado</h3>
        <p>Não há usuários que correspondam aos filtros aplicados.</p>
    </div>
@endif

{{-- ✅ SEÇÃO DE ESTATÍSTICAS DE CHAMADOS --}}
{{-- DEBUG: Verificar se as estatísticas existem --}}
@php
    \Log::info('Template - Estatísticas recebidas:', ['isset' => isset($statistics), 'data' => isset($statistics) ? $statistics : 'Não definidas']);
@endphp

@if(isset($statistics) && !empty($statistics))
    <div class="statistics-section">
        <div class="statistics-title">
            Estatísticas de Chamados
        </div>

        {{-- Totais Gerais --}}
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $statistics['total_open'] + $statistics['total_closed'] }}</div>
                <div class="stat-label">Total de Chamados</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $statistics['total_open'] }}</div>
                <div class="stat-label">Chamados Abertos</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $statistics['today_open'] }}</div>
                <div class="stat-label">Abertos Hoje</div>
            </div>
        </div>

        {{-- Por Departamento --}}
        @if(!empty($statistics['departments']))
            <div class="departments-section">
                <div class="section-title">Chamados por Departamento</div>
                <div class="stats-list">
                    @foreach($statistics['departments'] as $dept)
                        <div class="stat-line">
                            <div class="stat-name">{{ strtoupper($dept['name']) }}</div>
                            <div class="stat-numbers">
                                Total: {{ $dept['total'] }}
                                ({{ $dept['open'] }} Abertos / {{ $dept['closed'] }} Fechados)
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Por Setor --}}
        @if(!empty($statistics['sectors']))
            <div class="sectors-section">
                <div class="section-title">
                    Chamados por Setor
                    @if($statistics['user_type'] === 'Super Admin')
                        (Top 10)
                    @endif
                </div>
                <div class="stats-list">
                    @foreach($statistics['sectors'] as $sector)
                        <div class="stat-line">
                            <div class="stat-name">{{ strtoupper($sector['name']) }}</div>
                            <div class="stat-numbers">
                                Total: {{ $sector['total'] }}
                                ({{ $sector['open'] }} Abertos / {{ $sector['closed'] }} Fechados)
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Informações do usuário --}}
        <div class="user-type-info">
            Relatório gerado para: {{ $statistics['user_type'] }}
            @if(isset($statistics['message']))
                <br>{{ $statistics['message'] }}
            @endif
        </div>
    </div>
@else
    {{-- DEBUG: Mostrar se não há estatísticas --}}
    <div class="statistics-section">
        <div class="statistics-title">
            Estatísticas de Chamados
        </div>
        <div style="text-align: center; padding: 40px; color: #666;">
            <h3>Nenhuma estatística disponível</h3>
            <p>DEBUG: Variável $statistics não foi definida ou está vazia</p>
        </div>
    </div>
@endif

{{-- Rodapé --}}
<div class="footer">
    <p>Relatório gerado automaticamente pelo sistema em {{ $generated_at }}</p>
    <p>Este documento contém {{ $total_users }} usuário(s) listado(s)</p>
</div>
</body>
</html>
