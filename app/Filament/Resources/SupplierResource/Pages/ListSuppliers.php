<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuppliers extends ListRecords
{
    use ChecksResourcePermission;

    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
