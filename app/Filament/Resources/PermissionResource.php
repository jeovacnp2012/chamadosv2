<?php

namespace App\Filament\Resources;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    use ChecksResourcePermission;

    protected static ?string $model = Permission::class;
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationLabel = 'Permissões';
    protected static ?string $modelLabel = 'Permissão';
    protected static ?string $pluralModelLabel = 'Permissões';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nome da Permissão')
                ->required()
                ->unique(ignoreRecord: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Permissão')
                    ->searchable(),
            ])
            ->defaultSort('name')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
//    public static function canViewAny(): bool
//    {
//        return auth()->user()->can('view permission');
//    }
//
//    public static function canCreate(): bool
//    {
//        return auth()->user()->can('create permission');
//    }
//
//    public static function canEdit(Model $record): bool
//    {
//        return auth()->user()->can('update permission');
//    }
//
//    public static function canDelete(Model $record): bool
//    {
//        return auth()->user()->can('delete permission');
//    }
}
