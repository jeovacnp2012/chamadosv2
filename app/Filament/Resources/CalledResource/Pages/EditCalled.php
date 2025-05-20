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
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! auth()->user()?->hasRole('Super Admin')) {
            $data['user_id'] = auth()->id(); // força o ID do usuário logado
        }

        return $data;
    }
}
