<?php

namespace App\Filament\Resources\AddressResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\AddressResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAddress extends EditRecord
{
    use ChecksResourcePermission;

    protected static string $resource = AddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
