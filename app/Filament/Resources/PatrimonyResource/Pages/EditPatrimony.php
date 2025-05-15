<?php

namespace App\Filament\Resources\PatrimonyResource\Pages;

use App\Filament\Resources\PatrimonyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPatrimony extends EditRecord
{
    protected static string $resource = PatrimonyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
