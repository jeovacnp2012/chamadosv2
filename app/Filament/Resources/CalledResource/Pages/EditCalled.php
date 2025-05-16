<?php

namespace App\Filament\Resources\CalledResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\CalledResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCalled extends EditRecord
{
    use ChecksResourcePermission;
    protected static string $resource = CalledResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
