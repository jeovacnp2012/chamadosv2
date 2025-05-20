<?php

namespace App\Filament\Resources\CalledResource\Pages;

use App\Filament\Resources\CalledResource;
use Filament\Resources\Pages\Page;
use App\Models\Called;

class ChatPage extends Page
{
    public Called $record;

    protected static string $resource = CalledResource::class;

    protected static string $view = 'filament.resources.called-resource.pages.chat-page';

    public function mount(Called $record): void
    {
        $this->record = $record;
    }
}
