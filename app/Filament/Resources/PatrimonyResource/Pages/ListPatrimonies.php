<?php

namespace App\Filament\Resources\PatrimonyResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\PatrimonyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPatrimonies extends ListRecords
{
    use ChecksResourcePermission;
    protected static string $resource = PatrimonyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
