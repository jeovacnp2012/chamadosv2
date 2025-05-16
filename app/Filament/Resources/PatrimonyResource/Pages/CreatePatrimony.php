<?php

namespace App\Filament\Resources\PatrimonyResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\PatrimonyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePatrimony extends CreateRecord
{
    use ChecksResourcePermission;
    protected static string $resource = PatrimonyResource::class;
}
