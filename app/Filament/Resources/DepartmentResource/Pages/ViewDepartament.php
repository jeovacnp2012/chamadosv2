<?php
namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDepartament extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;
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
