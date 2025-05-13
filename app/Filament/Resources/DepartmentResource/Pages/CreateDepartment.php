<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Traits\ChecksResourcePermission;







use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartment extends CreateRecord
{

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static string $resource = DepartmentResource::class;
}
