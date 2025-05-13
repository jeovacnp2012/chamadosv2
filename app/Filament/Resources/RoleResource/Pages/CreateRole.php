<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Traits\ChecksResourcePermission;







use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static string $resource = RoleResource::class;
}
