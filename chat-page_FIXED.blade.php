<x-filament-panels::page>
    <x-filament-panels::header
        :actions="$this->getHeaderActions()"
        :breadcrumbs="[
            [
                'label' => __('Chamados'),
                'url' => static::$resource::getUrl('index'),
            ],
            [
                'label' => $this->record->protocol,
                'url' => static::$resource::getUrl('edit', ['record' => $this->record]),
            ],
            [
                'label' => __('Chat'),
            ],
        ]"
    />

    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow p-4 space-y-4">
            <div class="space-y-4 h-96 overflow-y-auto" id="chat-messages">
                @foreach($messages as $message)
                    <div class="{{ $message['is_own'] ? 'text-right' : 'text-left' }}">
                        <div class="inline-block max-w-xs md:max-w-md lg:max-w-lg xl:max-w-xl p-3 rounded-lg
                                    {{ $message['is_own'] ? 'bg-primary-100' : 'bg-gray-100' }}">
                            <div class="text-sm font-semibold">{{ $message['user'] }}</div>
                            @if($message['message'])
                                <div class="text-sm">{{ $message['message'] }}</div>
                            @endif
                            @if($message['attachment'])
                                <div class="mt-2">
                                    <a href="{{ asset('storage/'.$message['attachment']) }}"
                                       target="_blank"
                                       class="text-primary-600 hover:text-primary-800 text-sm flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                        </svg>
                                        Anexo
                                    </a>
                                </div>
                            @endif
                            <div class="text-xs text-gray-500 mt-1">{{ $message['time'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <form wire:submit.prevent="sendMessage" class="flex flex-col gap-2">
                <textarea
                    wire:model="message"
                    placeholder="Digite sua mensagem..."
                    class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    rows="2"
                ></textarea>

                <div class="flex items-center gap-2">
                    <div class="flex-1">
                        <input
                            type="file"
                            wire:model="attachment"
                            class="text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-primary-50 file:text-primary-700
                                hover:file:bg-primary-100"
                        >
                        @error('attachment') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <button
                        type="submit"
                        class="self-end bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700"
                        :disabled="!$wire.message && !$wire.attachment"
                    >
                        Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @script
    <script>
        Livewire.on('messagesUpdated', () => {
            const container = document.getElementById('chat-messages');
            container.scrollTop = container.scrollHeight;
        });
    </script>
    @endscript
</x-filament-panels::page>
