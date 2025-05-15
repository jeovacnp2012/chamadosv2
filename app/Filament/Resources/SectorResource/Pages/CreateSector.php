<?php

namespace App\Filament\Resources\SectorResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\SectorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSector extends CreateRecord
{
    use ChecksResourcePermission;

    protected static string $resource = SectorResource::class;
}
