<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermissions extends ListRecords
{
    use ChecksResourcePermission;

    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
