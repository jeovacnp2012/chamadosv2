<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! array_key_exists('company_id', $data)) {
            $data['company_id'] = $this->record->company_id;
        }

        return $data;
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
