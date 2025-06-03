<div class="mb-4 flex justify-end">
    <button
        onclick="
            const url = new URL(window.location.href);
            url.pathname = '/relatorios/supertabela';
            window.open(url.toString(), '_blank');
        "
        type="button"
        class="fi-btn inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:text-primary-600 focus:outline-none"
    >
        📊 Super Tabela
    </button>
</div>
