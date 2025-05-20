
<div class="flex flex-col h-[calc(100vh-150px)] max-w-5xl mx-auto bg-white rounded shadow border border-gray-200">
    <div class="bg-blue-100 px-6 py-4 border-b border-blue-300">
        <div class="text-lg font-bold text-blue-900">Chamado: {{ $record->protocol }}</div>
        <div class="text-sm text-blue-800">
            <strong>Solicitante:</strong> {{ $record->user->name ?? '-' }} |
            <strong>Executor:</strong> {{ $record->executor->name ?? '-' }} |
            <strong>Setor:</strong> {{ $record->sector->name ?? '-' }} |
            <strong>Problema:</strong> {{ $record->problem }}
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4 flex flex-col-reverse bg-gray-50">
        @foreach ($record->interactions()->latest()->get() as $interaction)
            <div class="flex {{ $interaction->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="flex items-end space-x-2 {{ $interaction->user_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(mb_substr($interaction->user->name, 0, 1)) }}
                    </div>

                    <div class="max-w-[75%] bg-{{ $interaction->user_id === auth()->id() ? 'blue' : 'gray' }}-100 rounded-lg px-4 py-2 shadow text-sm">
                        <div class="text-gray-800">{{ $interaction->message }}</div>
                        @if ($interaction->attachment)
                            <div class="mt-2">
                                @if (Str::endsWith($interaction->attachment, ['.jpg', '.jpeg', '.png', '.webp']))
                                    <img src="{{ Storage::url($interaction->attachment) }}" alt="Anexo" class="rounded max-w-full">
                                @elseif (Str::endsWith($interaction->attachment, ['.mp4', '.mov', '.avi']))
                                    <video controls class="rounded max-w-full">
                                        <source src="{{ Storage::url($interaction->attachment) }}">
                                    </video>
                                @endif
                            </div>
                        @endif
                        <div class="text-right text-xs text-gray-500 mt-1">
                            {{ $interaction->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <form wire:submit.prevent="sendMessage" class="px-6 py-4 border-t bg-white space-y-2">
        <textarea wire:model.defer="newMessage" rows="2" class="w-full border border-gray-300 rounded p-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Digite sua mensagem..."></textarea>
        <div class="flex items-center justify-between">
            <input type="file" wire:model="newAttachment" class="text-sm text-gray-600">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700">Enviar</button>
        </div>
        @error('newMessage') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        @error('newAttachment') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </form>
</div>
