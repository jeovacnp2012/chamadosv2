<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\CompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanies extends ListRecords
{
    use ChecksResourcePermission;

    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
