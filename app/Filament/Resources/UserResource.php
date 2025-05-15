<?php

namespace App\Filament\Resources;

use App\Traits\ChecksResourcePermission;







use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
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
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    use ChecksResourcePermission;
    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?string $navigationLabel = 'Usuários';
    protected static ?string $pluralModelLabel = 'Usuários';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        // Se o usuário logado NÃO for super admin, filtra os super admins
        if (! auth()->user()->hasRole('Super Admin')) {
            $query->whereDoesntHave('roles', fn($q) =>
            $q->where('name', 'Super Admin')
            );
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Vínculo com Empresa')
                ->visible(fn () => auth()->user()?->hasRole('Super Admin'))
                ->schema([
                    Grid::make(['sm' => 1, 'md' => 2])
                        ->schema([
                            Select::make('company_id')
                                ->label('Empresa')
                                ->relationship('company', 'trade_name')
                                ->searchable()
                                ->preload()
                                ->visible(fn ($record) => auth()->user()?->hasRole('Super Admin') && auth()->id() !== $record?->id)
                                ->required(fn ($record) => auth()->user()?->hasRole('Super Admin') && auth()->id() !== $record?->id)
                                ->dehydrated(fn ($record) => auth()->user()?->hasRole('Super Admin') && auth()->id() !== $record?->id)
                                ->default(fn ($record) => $record?->company_id),
                        ]),
                ]),
            Section::make('Dados do Usuário')
                ->schema([
                    Grid::make(['sm' => 1, 'md' => 2])->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                            ->required(),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->required()
                            ->dehydrateStateUsing(fn ($state) => strtolower($state))
                            ->email()
                            ->unique(ignoreRecord: true),
                    ]),

                    Grid::make(['sm' => 1, 'md' => 2])->schema([
                        TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->revealable()
                            ->required(fn(string $context) => $context === 'create')
                            ->dehydrateStateUsing(fn($state) => filled($state) ? \Illuminate\Support\Facades\Hash::make($state) : null)
                            ->dehydrated(fn($state) => filled($state)),
                    ]),
                ]),

            Section::make('Acesso e Segurança')
                ->schema([
                    Grid::make(['sm' => 1, 'md' => 2])->schema([
                        CheckboxList::make('roles')
                            ->label('Papéis')
                            ->relationship('roles', 'name')
                            ->options(function () {
                                $query = \Spatie\Permission\Models\Role::query();
                                if (! auth()->user()?->hasRole('Super Admin')) {
                                    $query->where('name', '!=', 'Super Admin');
                                }
                                return $query->pluck('name', 'id');
                            })
                            ->columns(['sm' => 1, 'md' => 2])
                            ->required(),
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    public static function canEdit($record): bool
    {
        return ! $record->hasRole('Super Admin') || auth()->user()->hasRole('Super Admin');
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        // Nenhum usuário comum pode deletar a si mesmo
        if ($user->id === $record->id && ! $user->hasRole('Super Admin')) {
            return false;
        }
        // Super Admin pode tentar deletar a si mesmo (será bloqueado se for o único na execução)
        return true;
    }
    public static function canView($record): bool
    {
        return ! $record->hasRole('Super Admin') || auth()->user()->hasRole('Super Admin');
    }
}
