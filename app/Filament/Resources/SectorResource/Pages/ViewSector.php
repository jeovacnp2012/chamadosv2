<?php
namespace App\Filament\Resources\SectorResource\Pages;

use App\Traits\ChecksResourcePermission;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\SectorResource;

class ViewSector extends ViewRecord
{
    use ChecksResourcePermission;

    protected static string $resource = SectorResource::class;
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
