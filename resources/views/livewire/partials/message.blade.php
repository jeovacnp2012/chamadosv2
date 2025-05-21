<div class="bg-white border rounded shadow-sm p-3 mb-2">
    <div class="flex items-center justify-between gap-4">
        {{-- Lado esquerdo: conteúdo da mensagem --}}
        <div class="flex-1">
            <div class="text-xs text-gray-600 font-semibold uppercase">
                {{ $message->user->name }}
            </div>

            <div class="text-sm text-gray-800 whitespace-pre-line">
                {{ $message->message }}
            </div>

            @if ($message->attachment_path)
                <div class="mt-1 mb-2">
                    <a href="{{ Storage::url($message->attachment_path) }}"
                       target="_blank"
                       class="text-blue-600 underline text-xs">
                        Ver Anexo ({{ strtoupper(pathinfo($message->attachment_path, PATHINFO_EXTENSION)) }})
                    </a>
                </div>
            @endif

            <div class="text-xs text-gray-400 mt-3">
                {{ $message->created_at->format('d/m/Y H:i') }}
            </div>
        </div>

        {{-- Lado direito: botão Excluir --}}
        <div class="flex-shrink-0 ml-2">
            <x-filament::button
                color="danger"
                size="sm"
                wire:click="deleteMessage({{ $message->id }})"
                icon="heroicon-o-trash"
            >
                Excluir
            </x-filament::button>
        </div>
    </div>
</div>
