<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use ChecksResourcePermission;

    protected static string $resource = UserResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Garante que company_id continue válido
        if (! array_key_exists('company_id', $data)) {
            $data['company_id'] = $this->record->company_id;
        }

        return $data;
    }


}
