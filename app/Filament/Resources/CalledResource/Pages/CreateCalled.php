<?php

namespace App\Filament\Resources\CalledResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\CalledResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCalled extends CreateRecord
{
    use ChecksResourcePermission;
    protected static string $resource = CalledResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->hasRole('Super Admin')) {
            $data['user_id'] = auth()->id(); // força o ID do usuário logado
        }

        return $data;
    }
}
