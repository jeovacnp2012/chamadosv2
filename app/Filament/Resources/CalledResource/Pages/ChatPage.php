<?php

namespace App\Filament\Resources\CalledResource\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\Resources\CalledResource;
use App\Models\Called;

class ChatPage extends Page
{
    protected static string $resource = CalledResource::class;

    protected static string $view = 'filament.resources.called-resource.pages.chat-page';

    public Called $record;

    public function mount(Called $record): void
    {
        $this->record = $record;
    }
}
