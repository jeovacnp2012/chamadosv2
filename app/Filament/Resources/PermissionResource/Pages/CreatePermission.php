<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Traits\ChecksResourcePermission;







use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static string $resource = PermissionResource::class;
}
