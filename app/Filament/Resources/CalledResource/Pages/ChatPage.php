<?php

namespace App\Filament\Resources\CalledResource\Pages;

use Filament\Actions\Action;
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
    public function getHeaderActions(): array
    {
        return [
            Action::make('voltar')
                ->label('Voltar')
                ->icon('heroicon-o-arrow-left')
                ->color('warning')
                ->url(route('filament.admin.resources.calleds.index'))
                ->outlined(false),
            Action::make('imprimir')
                ->label('Imprimir')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->extraAttributes([
                    'x-data' => '',
                    'x-on:click' => 'window.print()',
                ]),
        ];

    }
}
