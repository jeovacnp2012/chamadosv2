<?php

namespace App\Filament\Resources\PriceAgreementResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\PriceAgreementResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePriceAgreement extends CreateRecord
{
    use ChecksResourcePermission;

    protected static string $resource = PriceAgreementResource::class;

}
