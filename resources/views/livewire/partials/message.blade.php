<div class="p-3 border border-gray-300 rounded-xl bg-white shadow-sm">
    <div class="flex items-center justify-between mb-1">
        <span class="font-semibold text-gray-700">{{ $message->user->name ?? 'Usuário Desconhecido' }}</span>
        <span class="text-gray-400">{{ $message->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="text-gray-800">{{ $message->message }}</div>

    @if ($message->attachment_path)
        @php $ext = pathinfo($message->attachment_path, PATHINFO_EXTENSION); @endphp
        <div class="mt-2">
            @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                <img src="{{ asset('storage/' . $message->attachment_path) }}" class="max-w-xs rounded shadow border">
            @elseif (in_array($ext, ['mp4', 'webm']))
                <video controls class="max-w-xs rounded shadow border">
                    <source src="{{ asset('storage/' . $message->attachment_path) }}" type="video/{{ $ext }}">
                </video>
            @else
                <a href="{{ asset('storage/' . $message->attachment_path) }}" target="_blank" class="text-blue-600 underline">
                    📎 Baixar anexo ({{ strtoupper($ext) }})
                </a>
            @endif
        </div>
    @endif
    <div class="text-right mt-2">
        <x-filament::button
            x-data
            x-on:click="$wire.call('handleEditRequest', {{ $message->id }})"
            icon="heroicon-o-pencil"
            size="sm"
        >
            Editar
        </x-filament::button>
    </div>

</div>
{{--<script>--}}
{{--    window.addEventListener('open-edit-modal', e => {--}}
{{--        console.log('Livewire escutou open-edit-modal:', e.detail);--}}
{{--    });--}}
{{--</script>--}}
