<?php

namespace App\Filament\Resources\PriceAgreementResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\PriceAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPriceAgreement extends EditRecord
{
    use ChecksResourcePermission;

    protected static string $resource = PriceAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
