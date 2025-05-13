<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Traits\ChecksResourcePermission;







use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static string $resource = UserResource::class;
}
