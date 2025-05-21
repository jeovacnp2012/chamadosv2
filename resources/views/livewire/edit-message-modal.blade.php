<div>
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-4">
                <h2 class="text-sm font-bold mb-3">Editar Mensagem</h2>

                <textarea wire:model="editingMessageText" rows="4" class="w-full border rounded p-2 text-sm"></textarea>

                <div class="flex justify-end gap-2 mt-4">
                    <x-filament::button color="gray" wire:click="$set('showEditModal', false)">
                        Cancelar
                    </x-filament::button>
                    <x-filament::button wire:click="saveEditedMessage">
                        Salvar
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif
</div>
