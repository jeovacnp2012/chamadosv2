<?php

namespace App\Filament\Resources\AddressResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\AddressResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAddresses extends ListRecords
{
    use ChecksResourcePermission;

    protected static string $resource = AddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
