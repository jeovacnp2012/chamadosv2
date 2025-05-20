<div class="space-y-6" x-data="{ toast: '', showToast: false }" x-init="
    Livewire.on('toast', msg => {
        toast = msg;
        showToast = true;
        setTimeout(() => showToast = false, 3000);
    });

    Livewire.on('scrollToBottom', () => {
    window.requestAnimationFrame(() => {
            const el = document.getElementById('chat-box');
            if (el) el.scrollTop = el.scrollHeight;
        });
    });
">
    {{-- Toast --}}
    <div x-show="showToast" x-transition class="fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow z-50 text-sm">
        <span x-text="toast"></span>
    </div>

    {{-- Mensagens --}}
    <div id="chat-box" class="bg-white p-4 rounded-xl shadow max-h-[28rem] overflow-y-auto space-y-4 border border-gray-200">
        @forelse($messages as $message)
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm text-sm relative border-l-4
                @if($message->user_id === auth()->id()) border-blue-500 @else border-gray-300 @endif">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                            {{ substr($message->user->name, 0, 1) }}
                        </div>
                        <span class="font-semibold text-gray-700">{{ $message->user->name }}</span>
                    </div>
                    <span class="text-xs text-gray-500">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                </div>

                @if($editMessageId === $message->id)
                    <textarea wire:model.defer="editMessageText" class="w-full border mt-2 rounded p-2 text-sm focus:ring focus:ring-blue-400"></textarea>
                    <div class="flex justify-end gap-2 mt-2">
                        <button
                            wire:click="updateMessage"
                            class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-sm font-medium px-4 py-2 rounded-md shadow-sm transition duration-150 ease-in-out"
                        >
                            Salvar
                        </button>
                        <button
                            wire:click="cancelEdit"
                            class="inline-flex items-center justify-center bg-gray-300 hover:bg-gray-400 text-gray-800 text-sm font-medium px-4 py-2 rounded-md shadow-sm transition"
                        >
                            Cancelar
                        </button>
                    </div>
                @else
                    <div class="text-gray-800 mt-2 ml-10">{{ $message->message }}</div>

                    @if($message->attachment_path)
                        <div class="mt-2 ml-10 space-y-1">
                            @php $ext = pathinfo($message->attachment_path, PATHINFO_EXTENSION); @endphp

                            @if(in_array($ext, ['jpg','jpeg','png','webp']))
                                <img src="{{ Storage::url($message->attachment_path) }}" class="max-w-xs rounded border border-gray-300">
                            @elseif(in_array($ext, ['mp4','mov']))
                                <video controls class="max-w-xs rounded border border-gray-300">
                                    <source src="{{ Storage::url($message->attachment_path) }}">
                                </video>
                            @else
                                <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="text-blue-600 underline text-sm">
                                    📎 Ver anexo: {{ basename($message->attachment_path) }}
                                </a>
                            @endif
                        </div>
                    @endif

                    @if($message->user_id === auth()->id())
                        <div class="absolute top-3 right-3 space-x-2 text-xs">
                            <button wire:click="editMessage({{ $message->id }})" class="text-blue-600 hover:text-blue-800 transition">✏️</button>
                            <button wire:click="deleteMessage({{ $message->id }})" class="text-red-600 hover:text-red-800 transition">🗑️</button>
                        </div>
                    @endif
                @endif
            </div>
        @empty
            <p class="text-center text-gray-500 italic">Nenhuma mensagem ainda</p>
        @endforelse
    </div>

    {{-- Formulário de envio (somente se não estiver editando) --}}
    @if(is_null($editMessageId))
        <form wire:submit.prevent="sendMessage" class="space-y-4 bg-white p-6 rounded-xl shadow border border-gray-100" enctype="multipart/form-data">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem</label>
                <textarea
                    wire:model.defer="newMessage"
                    rows="3"
                    class="w-full border rounded p-3 text-sm focus:outline-none focus:ring focus:ring-blue-500"
                    placeholder="Digite sua mensagem..."
                ></textarea>
                @error('newMessage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Anexo (opcional)</label>
                <input
                    type="file"
                    wire:model="newAttachment"
                    class="block w-full text-sm border rounded px-3 py-2"
                />
                @error('newAttachment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="bg-gradient-to-r from-blue-500 to-blue-600 font-medium px-6 py-2 rounded-lg hover:from-blue-600 hover:to-blue-700 shadow hover:shadow-lg flex items-center gap-2"
                >
                    <svg class="w-5 h-5 animate-bounce" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <span>Enviar</span>
                </button>
            </div>
        </form>
    @endif
</div>
