<x-filament::page>
    <div class="space-y-6" x-data="{ toast: '', showToast: false }" x-init="
        Livewire.on('toast', msg => {
            toast = msg;
            showToast = true;
            setTimeout(() => showToast = false, 3000);
        });

        Livewire.on('scrollToBottom', () => {
            const el = document.getElementById('chat-box');
            if (el) el.scrollTop = el.scrollHeight;
        });
    ">
        <div x-show="showToast" x-transition class="fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow z-50 text-sm">
            <span x-text="toast"></span>
        </div>

        <x-filament::card>
            <livewire:called-chat :record="$record" />
        </x-filament::card>
    </div>
</x-filament::page>
