<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Usuários';
    protected static ?string $pluralModelLabel = 'Usuários';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados do Usuário')
                    ->columns(['sm' => 1, 'md' => 2])
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required(),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->revealable()
                            ->required(fn(string $context) => $context === 'create')
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn($state) => filled($state)),
                    ]),
                Section::make('Acesso e Segurança')
                    ->columns(['sm' => 1, 'md' => 2])
                    ->schema([
                        Select::make('roles')
                            ->label('Perfil de Acesso')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable(),

//                        Select::make('permissions')
//                            ->label('Permissões Individuais')
//                            ->multiple()
//                            ->relationship('permissions', 'name')
//                            ->preload()
//                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nome')->searchable(),
                TextColumn::make('email')->label('E-mail'),
                TextColumn::make('roles.name')->label('Perfil')->badge()->color('primary'),
            ])
            ->defaultSort('name')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view user');
//        return true;
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create user');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('update user');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('delete user');
    }

}
