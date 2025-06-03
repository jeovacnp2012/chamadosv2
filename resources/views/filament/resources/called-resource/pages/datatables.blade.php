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
