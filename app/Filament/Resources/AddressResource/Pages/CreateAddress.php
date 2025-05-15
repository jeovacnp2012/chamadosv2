<?php

namespace App\Filament\Resources\AddressResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\AddressResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAddress extends CreateRecord
{
    use ChecksResourcePermission;

    protected static string $resource = AddressResource::class;
}
