<?php

namespace App\Filament\Resources\AgreementItemResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\AgreementItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAgreementItem extends CreateRecord
{
    use ChecksResourcePermission;

    protected static string $resource = AgreementItemResource::class;
}
