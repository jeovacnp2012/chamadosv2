<?php
namespace App\Filament\Resources\PatrimonyResource\Pages;

use App\Filament\Resources\PatrimonyResource;
use App\Traits\ChecksResourcePermission;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPatrimony extends ViewRecord
{
    use ChecksResourcePermission;

    protected static string $resource = PatrimonyResource::class;
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
