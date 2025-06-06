<?php

namespace App\Filament\Resources;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms\Form;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class RoleResource extends Resource
{
    use ChecksResourcePermission;

    protected static ?string $model = Role::class;
    protected static ?string $navigationGroup = 'Configurações';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Papéis de Acesso';
    protected static ?string $modelLabel = 'Papel';
    protected static ?string $pluralModelLabel = 'Papéis';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nome do Papel')
                ->required()
                ->unique(ignoreRecord: true),

            Select::make('permissions')
                ->label('Permissões')
                ->multiple()
                ->relationship('permissions', 'name')
                ->preload()
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Papel')->searchable(),
                TextColumn::make('permissions.name')
                    ->label('Permissões')
                    ->badge()
                    ->color('info')
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
//    public static function canViewAny(): bool
//    {
//        return auth()->user()->can('view role');
//    }
//
//    public static function canCreate(): bool
//    {
//        return auth()->user()->can('create role');
//    }
//
//    public static function canEdit(Model $record): bool
//    {
//        return auth()->user()->can('update role');
//    }
//
//    public static function canDelete(Model $record): bool
//    {
//        return auth()->user()->can('delete role');
//    }
}
