<?php

namespace App\Filament\Resources\DepartamentResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\DepartamentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartament extends CreateRecord
{
    use ChecksResourcePermission;

    protected static string $resource = DepartamentResource::class;
}
