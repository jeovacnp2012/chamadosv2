<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    use ChecksResourcePermission;

    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
