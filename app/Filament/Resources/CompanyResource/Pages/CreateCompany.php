<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Facades\FilamentAsset;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;
    protected function shouldDisableForm(): bool
    {
        return (bool) $this->form->getState()['cnpj_is_duplicate'] ?? false;
    }
}
