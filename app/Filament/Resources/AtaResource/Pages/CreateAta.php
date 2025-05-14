<?php

namespace App\Filament\Resources\AtaResource\Pages;

use App\Filament\Resources\PriceAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAta extends CreateRecord
{
    protected static string $resource = PriceAgreementResource::class;
}
