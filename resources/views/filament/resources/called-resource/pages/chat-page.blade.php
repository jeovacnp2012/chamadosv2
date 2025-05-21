<x-filament::page>
    <h2 class="text-lg font-bold mb-6 flex items-center gap-2">
        <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 text-primary" />
        Chat do Chamado
    </h2>

    {{-- Painel do chamado --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow p-4 space-y-2 border text-xs">
            <h3 class="text-sm font-bold text-gray-700 mb-2">📌 Informações Gerais</h3>
            <p><strong>Protocolo:</strong> {{ $record->protocol }}</p>
            <p><strong>Status:</strong> {{ $record->status }}</p>
            <p><strong>Descrição:</strong> {{ $record->description }}</p>
            <p><strong>Data de Abertura:</strong> {{ $record->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Data de Encerramento:</strong> {{ $record->closing_date ? $record->closing_date->format('d/m/Y H:i') : '—' }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 space-y-2 border text-xs">
            <h3 class="text-sm font-bold text-gray-700 mb-2">👤 Envolvidos e Local</h3>
            <p><strong>Solicitante:</strong> {{ $record->user->name ?? '—' }}</p>
            <p><strong>Executor:</strong> {{ $record->executor->name ?? '—' }}</p>
            <p><strong>Setor:</strong> {{ $record->sector->name ?? '—' }}</p>
            <p><strong>Departamento:</strong> {{ $record->sector->departament->name ?? '—' }}</p>
            <p><strong>Plaqueta Patrimônio:</strong> {{ $record->patrimony_tag ?? '—' }}</p>
        </div>
    </div>

    {{-- Botão abrir modal --}}
    <div class="mb-4">
        <x-filament::button color="primary" x-data @click="$dispatch('open-send-message')">
            Nova Mensagem
        </x-filament::button>
    </div>

    {{-- Componente de mensagens Livewire --}}
    <livewire:called-messages :called="$record" />

    {{-- Componentes obrigatórios e montados --}}
    <livewire:send-message-form :called="$record" />
    <livewire:edit-message-modal wire:key="edit-message-modal" />

    {{-- Notificações Livewire --}}
    <script>
        window.addEventListener('notify', event => {
            window.Filament?.notifications?.notify({
                type: event.detail.type,
                title: event.detail.message,
            });
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('open-edit-modal', (data) => {
                const component = Livewire.find(
                    document.querySelector('[wire\\:key="edit-message-modal"]').getAttribute('wire:id')
                );
                component.call('handleEditRequest', data.id);
            });
        });
    </script>
</x-filament::page>
