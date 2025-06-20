<div class="bg-white border rounded shadow-sm p-3 mb-2">
    <div class="flex items-center justify-between gap-4">
        {{-- Lado esquerdo: conteúdo da mensagem --}}
        <div class="flex-1">
            <div class="flex flex-col gap-1 mb-2">
                {{-- Nome do usuário --}}
                <div class="text-xs text-gray-600 font-semibold uppercase">
                    {{ $message->user->name }}
                </div>

                {{-- Setor(es) e Departamento(s) --}}
                <div class="text-xs text-gray-500">
                    {{-- Setores --}}
                    @if($message->user->sectors && $message->user->sectors->isNotEmpty())
                        <div class="inline-flex items-center mb-1">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 8a1 1 0 011-1h4a1 1 0 011 1v4H7v-4z" clip-rule="evenodd"/>
                            </svg>
                            <strong>Setor(es):</strong>
                            <span class="ml-1">{{ $message->user->sectors->pluck('name')->join(', ') }}</span>
                        </div>
                    @else
                        <div class="inline-flex items-center mb-1 text-orange-500">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 8a1 1 0 011-1h4a1 1 0 011 1v4H7v-4z" clip-rule="evenodd"/>
                            </svg>
                            <strong>Setor:</strong>
                            <span class="ml-1 italic">Não informado</span>
                        </div>
                    @endif

                    {{-- Departamentos --}}
                    @if($message->user->departaments && $message->user->departaments->isNotEmpty())
                        <div class="inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            <strong>Depto(s):</strong>
                            <span class="ml-1">{{ $message->user->departaments->pluck('name')->join(', ') }}</span>
                        </div>
                    @else
                        <div class="inline-flex items-center text-orange-500">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            <strong>Departamento:</strong>
                            <span class="ml-1 italic">Não informado</span>
                        </div>
                    @endif
                </div>
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
