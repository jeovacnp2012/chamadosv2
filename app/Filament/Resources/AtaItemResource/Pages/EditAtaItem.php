<?php

namespace App\Filament\Resources\AtaItemResource\Pages;

use App\Filament\Resources\AgreementItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAtaItem extends EditRecord
{
    protected static string $resource = AgreementItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
