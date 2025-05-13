<?php
namespace App\Filament\Resources\SectorResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\SectorResource;
use Filament\Notifications\Notification;

class ViewSector extends ViewRecord
{
    protected static string $resource = SectorResource::class;
}
