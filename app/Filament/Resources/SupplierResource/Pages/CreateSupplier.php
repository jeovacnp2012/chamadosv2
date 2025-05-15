<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSupplier extends CreateRecord
{

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static string $resource = SupplierResource::class;
}
