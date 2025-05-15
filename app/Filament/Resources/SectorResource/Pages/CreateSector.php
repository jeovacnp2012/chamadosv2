<?php

namespace App\Filament\Resources\SectorResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\SectorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSector extends CreateRecord
{

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static string $resource = SectorResource::class;
}
