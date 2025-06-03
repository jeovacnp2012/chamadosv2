<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Super Tabela' }} - Relatórios</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        .table-container {
            max-height: 80vh;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-blue-600 text-white px-6 py-4">
            <h1 class="text-2xl font-bold">{{ $pageTitle ?? 'Super Tabela' }}</h1>
            <div class="flex items-center justify-between">
                <p class="text-blue-100">Total de registros: <span id="total-count">{{ count($calleds) }}</span></p>
                @if(isset($filterType))
                    <span class="px-3 py-1 bg-blue-500 rounded-full text-sm">
                            Filtro: {{ ucfirst($filterType) }}
                        </span>
                @endif
            </div>
        </div>

        <!-- Filtros com jQuery -->
        <div class="bg-gray-50 px-6 py-4 border-b">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar por nome/ID</label>
                    <input type="text" id="search-input" placeholder="Digite para buscar..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por usuário</label>
                    <select id="user-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos os usuários</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por setor</label>
                    <select id="sector-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos os setores</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="clear-filters" class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Limpar Filtros
                    </button>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="min-w-full divide-y divide-gray-200" id="chamados-table">
                <thead class="bg-gray-50 sticky top-0">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Setor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interações</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</th>
                    @if(isset($filterType) && $filterType === 'fechados')
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechado em</th>
                    @endif
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($calleds as $called)
                    <tr class="hover:bg-gray-50 table-row"
                        data-user="{{ strtolower($called->user->name ?? '') }}"
                        data-sector="{{ strtolower($called->sector->name ?? '') }}"
                        data-search="{{ strtolower($called->id . ' ' . ($called->user->name ?? '') . ' ' . ($called->sector->name ?? '')) }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $called->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $called->user->name ?? 'Não informado' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $called->sector->name ?? 'Não informado' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if(isset($filterType) && $filterType === 'abertos')
                                        bg-green-100 text-green-800
                                    @elseif(isset($filterType) && $filterType === 'fechados')
                                        bg-red-100 text-red-800
                                    @else
                                        {{ $called->status === 'aberto' || $called->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}
                                    @endif">
                                    {{ ucfirst($called->status ?? 'Indefinido') }}
                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                    {{ $called->interactions->count() }} interações
                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $called->created_at ? $called->created_at->format('d/m/Y H:i') : 'Não informado' }}
                        </td>
                        @if(isset($filterType) && $filterType === 'fechados')
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $called->closing_date ? \Carbon\Carbon::parse($called->closing_date)->format('d/m/Y H:i') : 'Não informado' }}
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr id="no-results">
                        <td colspan="{{ isset($filterType) && $filterType === 'fechados' ? '7' : '6' }}" class="px-6 py-4 text-center text-gray-500">
                            Nenhum chamado encontrado
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t">
            <div class="flex items-center justify-between">
                <a href="{{ route('filament.admin.pages.dashboard') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Voltar ao Filament
                </a>
                <button onclick="window.print()"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Preencher filtros dinâmicos
        const users = new Set();
        const sectors = new Set();

        $('.table-row').each(function() {
            const user = $(this).data('user');
            const sector = $(this).data('sector');
            if (user) users.add(user);
            if (sector) sectors.add(sector);
        });

        // Adicionar opções aos selects
        users.forEach(user => {
            $('#user-filter').append(`<option value="${user}">${user}</option>`);
        });

        sectors.forEach(sector => {
            $('#sector-filter').append(`<option value="${sector}">${sector}</option>`);
        });

        // Função de filtro
        function applyFilters() {
            const searchTerm = $('#search-input').val().toLowerCase();
            const selectedUser = $('#user-filter').val().toLowerCase();
            const selectedSector = $('#sector-filter').val().toLowerCase();

            let visibleCount = 0;

            $('.table-row').each(function() {
                const $row = $(this);
                const searchData = $row.data('search');
                const userData = $row.data('user');
                const sectorData = $row.data('sector');

                let showRow = true;

                // Filtro de busca
                if (searchTerm && !searchData.includes(searchTerm)) {
                    showRow = false;
                }

                // Filtro de usuário
                if (selectedUser && userData !== selectedUser) {
                    showRow = false;
                }

                // Filtro de setor
                if (selectedSector && sectorData !== selectedSector) {
                    showRow = false;
                }

                if (showRow) {
                    $row.show();
                    visibleCount++;
                } else {
                    $row.hide();
                }
            });

            // Atualizar contador
            $('#total-count').text(visibleCount);

            // Mostrar/ocultar mensagem de "nenhum resultado"
            if (visibleCount === 0) {
                $('#no-results').show();
            } else {
                $('#no-results').hide();
            }
        }

        // Eventos dos filtros
        $('#search-input, #user-filter, #sector-filter').on('input change', applyFilters);

        // Limpar filtros
        $('#clear-filters').click(function() {
            $('#search-input').val('');
            $('#user-filter').val('');
            $('#sector-filter').val('');
            applyFilters();
        });
    });
</script>
</body>
</html>
