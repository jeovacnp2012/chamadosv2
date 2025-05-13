<?php

namespace App\Filament\Resources;

use App\Traits\ChecksResourcePermission;







use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Models\Department;
use App\Support\ValidationRules;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DepartmentResource extends Resource
{
    use ChecksResourcePermission;
    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static ?string $model = Department::class;
    protected static ?string $navigationGroup = 'Cadastro';
    protected static ?string $navigationLabel = 'Departamentos';
    protected static ?string $pluralModelLabel = 'Departamentos';
    protected static ?string $modelLabel = 'Departamento';
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Section::make('Informações do Departamento')
                ->columns(['sm' => 1, 'md' => 2])
                ->schema([
                    Select::make('company_id')
                        ->label('Empresa')
                        ->relationship('company', 'corporate_name')
                        ->required(),

                    TextInput::make('name')
                        ->label('Nome do Departamento')
                        ->required()
                        ->dehydrateStateUsing(fn($state) => strtoupper($state)),

                    TextInput::make('contact_person')
                        ->label('Responsável')
                        ->dehydrateStateUsing(fn ($state) => strtoupper($state)),

                    TextInput::make('cell_phone')
                        ->label('Celular')
                        ->mask('(99) 99999-9999')
                        ->dehydrateStateUsing(fn($state) => preg_replace('/\D/', '', $state))
                        ->rule([ValidationRules::phone()]),

                    TextInput::make('extension')
                        ->label('Ramal'),

                    TextInput::make('email')
                        ->label('Email')
                        ->email(),
                ]),

            Section::make('Endereço e Status')
                ->columns(['sm' => 1, 'md' => 2])
                ->schema([
                    Select::make('address_id')
                        ->label('Endereço')
                        ->options(fn () => \App\Models\Address::all()->mapWithKeys(fn ($address) => [
                            $address->id => $address->formatted_address,
                        ]))
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('postal_code')
                                ->label('CEP')
                                ->mask('99999-999')
                                ->dehydrateStateUsing(fn($state) => preg_replace('/\D/', '', $state))
                                ->required()
                                ->live(debounce: 500)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $cep = preg_replace('/\D/', '', $state);
                                    if (strlen($cep) !== 8) {
                                        return;
                                    }

                                    $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");
                                    if ($response->successful() && !isset($response['erro'])) {
                                        $data = $response->json();
                                        $set('street', $data['logradouro'] ?? '');
                                        $set('neighborhood', $data['bairro'] ?? '');
                                        $set('city', $data['localidade'] ?? '');
                                        $set('state', $data['uf'] ?? '');
                                    } else {
                                        Notification::make()
                                            ->title('CEP não encontrado')
                                            ->body('Verifique se o CEP informado está correto.')
                                            ->danger()
                                            ->persistent()
                                            ->send();
                                    }
                                }),

                            TextInput::make('street')->label('Rua')->required(),
                            TextInput::make('number')->label('Número'),
                            TextInput::make('complement')->label('Complemento'),
                            TextInput::make('neighborhood')->label('Bairro')->required(),
                            TextInput::make('city')->label('Cidade')->required(),
                            TextInput::make('state')->label('UF')->required()->maxLength(2),
                        ]),

                    Toggle::make('is_active')
                        ->label('Ativa no sistema')
                        ->default(true)
                        ->visible(function (string $context): bool {
                            $user = Auth::user();
                            return $context === 'edit' && $user && $user->hasAnyRole(['Super Admin', 'Gerente']);
                        }),
                ])
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        // Chama a função e armazena na variável
        $settings = responsiveColumnToggle(true, false);
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Departamento')
                    ->searchable(),
                TextColumn::make('company.corporate_name')
                    ->label('Empresa')
                    ->extraAttributes($settings['extraAttributes'])
                    ->extraHeaderAttributes($settings['extraHeaderAttributes']),
                TextColumn::make('contact_person')
                    ->label('Responsável')
                    ->extraAttributes($settings['extraAttributes'])
                    ->extraHeaderAttributes($settings['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cell_phone')
                    ->label('Celular')
                    ->formatStateUsing(fn($state) => \App\Support\ValidationRules::formatPhone($state))
                    ->extraAttributes($settings['extraAttributes'])
                    ->extraHeaderAttributes($settings['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->extraAttributes($settings['extraAttributes'])
                    ->extraHeaderAttributes($settings['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                TernaryFilter::make('is_active')->label('Ativo')->default(true),
            ])
            ->actions([
                ActionGroup::make([
//                    ViewAction::make()->label('Visualizar')->icon('heroicon-o-eye'),
                    EditAction::make()->label('Editar')->icon('heroicon-o-pencil'),
                    DeleteAction::make()->label('Excluir')->icon('heroicon-o-trash'),
                ])
                    ->button()
                    ->label('Ações'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
//    public static function canViewAny(): bool
//    {
//        return auth()->user()->can('view department');
//    }

//    public static function canCreate(): bool
//    {
//        return auth()->user()->can('create department');
//    }
//
//    public static function canEdit(Model $record): bool
//    {
//        return auth()->user()->can('update department');
//    }
//
//    public static function canDelete(Model $record): bool
//    {
//        return auth()->user()->can('delete department');
//    }

}
