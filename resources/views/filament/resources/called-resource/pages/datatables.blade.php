<x-filament::page>
    <div class="mb-4 flex justify-end">
        <button
            type="button"
            class="fi-btn inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:text-primary-600 focus:outline-none"
            onclick="
                let href = window.location.href;
                let filtro = href.includes('?') ? href.substring(href.indexOf('?')) : '';
                let novaUrl = '/relatorios/datatables' + filtro;
                window.open(novaUrl, '_blank');
            "
        >
            📊 Relatório com filtros
        </button>
    </div>

    {{ $this->table }}
</x-filament::page>
<script>

    $('#sua-tabela').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/api/v1/calleds',
            data: function (d) {
                // Envia os filtros no formato esperado pela API
                d.tableFilters = {
                    status_aberto: {
                        value: $('#filtro-status').val() // ou qualquer outro valor aplicado
                    },
                    sector: {
                        sector_ids: $('#filtro-setores').val() // se for select múltiplo
                    },
                    created_at: {
                        created_from: $('#filtro-data-inicio').val(),
                        created_until: $('#filtro-data-fim').val()
                    }
                    // adicione outros filtros conforme desejar
                };

                // O DataTables já envia automaticamente `start` e `length`,
                // então vamos converter isso para `page` e `per_page`
                d.page = Math.floor(d.start / d.length) + 1;
                d.per_page = d.length;
            },
            headers: {
                'Authorization': 'Bearer SEU_TOKEN_AQUI'
            }
        },
        columns: [
            { data: 'protocolo', name: 'protocolo' },
            { data: 'status', name: 'status' },
            { data: 'sector.name', name: 'sector.name' },
            { data: 'user.name', name: 'user.name' },
            { data: 'created_at', name: 'created_at' },
            // ...
        ]
    });


</script>
