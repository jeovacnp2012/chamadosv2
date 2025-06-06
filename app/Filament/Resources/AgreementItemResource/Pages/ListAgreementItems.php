<?php

namespace App\Filament\Resources\AgreementItemResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\AgreementItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgreementItems extends ListRecords
{
    use ChecksResourcePermission;

    protected static string $resource = AgreementItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
