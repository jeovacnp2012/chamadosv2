<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\CompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompany extends EditRecord
{
    use ChecksResourcePermission;

    protected static string $resource = CompanyResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function shouldDisableForm(): bool
    {
        return (bool) $this->form->getState()['cnpj_is_duplicate'] ?? false;
    }
}
