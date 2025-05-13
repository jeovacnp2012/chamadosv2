<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectorResource\Pages;
use App\Filament\Resources\SectorResource\RelationManagers;
use App\Models\Sector;
use App\Traits\ChecksResourcePermission;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SectorResource extends Resource
{
    use ChecksResourcePermission;

    protected static ?string $model = Sector::class;
    protected static ?string $navigationGroup = 'Cadastro';
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $modelLabel = 'Setor';
    protected static ?string $pluralModelLabel = 'Setores';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações do Setor')
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                            ->required(),

                        Select::make('department_id')
                            ->label('Departamento')
                            ->relationship('department', 'name')
                            ->required()
                            ->preload()
                            ->searchable(),
                    ]),
                Section::make('Contato e Status')
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextInput::make('extension')
                            ->label('Ramal'),

                        TextInput::make('cell_phone')
                            ->label('Celular'),

                        TextInput::make('responsible')
                            ->label('Responsável')
                            ->dehydrateStateUsing(fn ($state) => strtoupper($state)),

                        TextInput::make('email')
                            ->label('Email')
                            ->dehydrateStateUsing(fn ($state) => strtolower($state))
                            ->email(),

                        Toggle::make('is_active')
                            ->label('Ativa no sistema')
                            ->default(true)
                            ->visible(function (string $context): bool {
                                $user = Auth::user();
                                return $context === 'edit' && $user && $user->hasAnyRole(['Super Admin', 'Gerente']);
                            }),
                    ]),

                Section::make('Endereço')
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        Select::make('address_id')
                            ->label('Endereço')
                            ->options(fn() => \App\Models\Address::all()->mapWithKeys(fn($address) => [
                                $address->id => $address->formatted_address,
                            ]))
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('postal_code')
                                    ->label('CEP')
                                    ->mask('99999-999')
                                    ->required()
                                    ->dehydrateStateUsing(fn($state) => preg_replace('/\\D/', '', $state))
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $cep = preg_replace('/\\D/', '', $state);
                                        if (strlen($cep) !== 8) return;

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
                    ]),

            ]);

    }

    public static function table(Table $table): Table
    {
        // Chama a função e armazena na variável
        $settings = responsiveColumnToggle();
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label('Departamento')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address.formatted_address')
                    ->label('Endereço')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('extension')
                    ->label('Ramal')
                    ->extraAttributes($settings['extraAttributes'])
                    ->extraHeaderAttributes($settings['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: $settings['toggleable']),
                TextColumn::make('cell_phone')
                    ->label('Celular')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('responsible')
                    ->label('Responsável')
                    ->extraAttributes($settings['extraAttributes'])
                    ->extraHeaderAttributes($settings['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: $settings['toggleable']),
                TextColumn::make('email')
                    ->label('Email')
                    ->extraAttributes($settings['extraAttributes'])
                    ->extraHeaderAttributes($settings['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: $settings['toggleable']),
                IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->extraAttributes($settings['extraAttributes'])
                    ->extraHeaderAttributes($settings['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: $settings['toggleable']),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Ativo')->default(true),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('Visualizar')->icon('heroicon-o-eye'),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSectors::route('/'),
            'create' => Pages\CreateSector::route('/create'),
            'edit' => Pages\EditSector::route('/{record}/edit'),
            'view' => Pages\ViewSector::route('/{record}'), // 👈 Adicione esta linha
        ];
    }
}
