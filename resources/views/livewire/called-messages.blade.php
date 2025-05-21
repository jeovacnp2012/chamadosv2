<div>
    <div class="space-y-3">
        @foreach ($recentMessages as $message)
            <div class="p-3 border border-gray-300 rounded-xl bg-white shadow-sm text-xs">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-semibold text-gray-700">{{ $message->user->name ?? 'Usuário Desconhecido' }}</span>
                    <span class="text-gray-400">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                </div>

                <div class="text-gray-800">{{ $message->message }}</div>

                @if ($message->attachment_path)
                    @php $ext = pathinfo($message->attachment_path, PATHINFO_EXTENSION); @endphp
                    <div class="mt-2">
                        <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="text-blue-500 underline">
                            Ver Anexo ({{ strtoupper($ext) }})
                        </a>
                    </div>
                @endif

{{--                <div class="text-right mt-2">--}}
{{--                        <x-filament::button--}}
{{--                            icon="heroicon-o-pencil"--}}
{{--                            size="sm"--}}
{{--                            x-on:click="Livewire.dispatch('open-edit-modal', { id: {{ $message->id }} })"--}}
{{--                        >--}}
{{--                            Editar--}}
{{--                        </x-filament::button>--}}
{{--                </div>--}}
                <div>
                    <x-filament::button
                        color="danger"
                        icon="heroicon-o-trash"
                        size="sm"
                        wire:click="deleteMessage({{ $message->id }})"
                        onclick="return confirm('Tem certeza que deseja excluir esta mensagem?')"
                    >
                        Excluir
                    </x-filament::button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $recentMessages->links() }}
    </div>
</div>
