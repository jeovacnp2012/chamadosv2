<?php

namespace App\Filament\Resources\CalledResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\CalledResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCalleds extends ListRecords
{
    use ChecksResourcePermission;
    protected static string $resource = CalledResource::class;
    protected static string $view = 'filament.resources.called-resource.pages.datatables';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
