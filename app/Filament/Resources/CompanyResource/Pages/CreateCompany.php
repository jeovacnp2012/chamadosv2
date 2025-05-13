<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Traits\ChecksResourcePermission;







use App\Filament\Resources\CompanyResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Facades\FilamentAsset;

class CreateCompany extends CreateRecord
{

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static string $resource = CompanyResource::class;
    protected function shouldDisableForm(): bool
    {
        return (bool) $this->form->getState()['cnpj_is_duplicate'] ?? false;
    }
}
