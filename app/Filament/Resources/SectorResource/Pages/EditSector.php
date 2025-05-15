<?php

namespace App\Filament\Resources\SectorResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\SectorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSector extends EditRecord
{

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static string $resource = SectorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
