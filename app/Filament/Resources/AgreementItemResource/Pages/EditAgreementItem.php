<?php

namespace App\Filament\Resources\AgreementItemResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\AgreementItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgreementItem extends EditRecord
{
    use ChecksResourcePermission;

    protected static string $resource = AgreementItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
