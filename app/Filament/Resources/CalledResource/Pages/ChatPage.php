<?php

namespace App\Filament\Resources\CalledResource\Pages;

use App\Filament\Resources\CalledResource;
use App\Models\Called;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\View\View;

class ChatPage extends Page
{
    protected static string $resource = CalledResource::class;

    protected static string $view = 'livewire.called-chat';

    public ?Called $record = null;

    public function mount(Called $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return 'Chat do Chamado';
    }

    public function render(): View
    {
        return view(static::$view, [
            'record' => $this->record,
        ]);
    }
}
