<div>
<form wire:submit.prevent="save" class="space-y-4 p-4">
    <div>
        <label class="block text-xs font-bold mb-1">Mensagem</label>
        <textarea wire:model.defer="message" rows="3" class="w-full border rounded p-2 text-xs" required></textarea>
    </div>

    <div>
        <label class="block text-xs font-bold mb-1">Anexo (opcional)</label>
        <input type="file" wire:model="attachment" class="text-xs">

        @if ($attachment)
            <div class="mt-2 text-xs text-gray-700">
                <strong>Prévia do Anexo:</strong>
                @if ($attachment->extension() === 'pdf')
                    📄 {{ $attachment->getClientOriginalName() }}
                @elseif (in_array($attachment->extension(), ['jpg', 'jpeg', 'png', 'gif']))
                    <img src="{{ $attachment->temporaryUrl() }}" class="max-w-xs mt-1 rounded border shadow">
                @else
                    📎 {{ $attachment->getClientOriginalName() }}
                @endif
            </div>
        @endif
    </div>

    <div class="text-right">
        <x-filament::button type="submit">
            Enviar
        </x-filament::button>
    </div>
</form>
</div>
