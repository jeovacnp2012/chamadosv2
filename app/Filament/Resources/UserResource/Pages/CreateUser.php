<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use ChecksResourcePermission;

    protected static string $resource = UserResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Define company_id automaticamente se não estiver presente
        if (! array_key_exists('company_id', $data)) {
            $data['company_id'] = auth()->user()->company_id;
        }

        return $data;
    }


}
