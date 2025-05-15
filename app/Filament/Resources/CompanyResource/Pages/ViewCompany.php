<?php
namespace App\Filament\Resources\CompanyResource\Pages;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\CompanyResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCompany extends ViewRecord
{

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static string $resource = CompanyResource::class;
    public function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                EditAction::make()->label('Editar')->icon('heroicon-m-pencil'),
                DeleteAction::make()->label('Excluir')->icon('heroicon-m-trash')->color('danger'),
            ])
                ->label('Ações') // 👈 nome do grupo (opcional)
                ->icon('heroicon-o-cog')->button() // 👈 ícone do grupo (opcional)
        ];
    }
}
