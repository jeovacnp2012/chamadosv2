<?php

namespace App\Filament\Resources;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use App\Support\ValidationRules;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SupplierResource extends Resource
{

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static ?string $model = Supplier::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?string $navigationGroup = 'Cadastro';
    protected static ?string $navigationLabel = 'Executores';
    protected static ?string $modelLabel = 'Executor';
    protected static ?string $pluralModelLabel = 'Executores';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Dados do Executor')
                ->description('Dados principais do executor cadastrado')
                ->columns([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 3,
                ])
                ->schema([
                    Select::make('company_id')
                        ->label('Empresa')
                        ->relationship('company', 'trade_name')
                        ->preload()
                        ->searchable(),
                    Grid::make(2)
                        ->schema([
                            TextInput::make('corporate_name')
                                ->label('Razão Social')
                                ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                                ->required(),
                            TextInput::make('trade_name')
                                ->label('Nome Fantasia')
                                ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                        ]),
                    Grid::make(3)
                        ->schema([
                            TextInput::make('cnpj')
                                ->label('CNPJ')
                                ->required()
                                ->mask('99.999.999/9999-99')
                                ->placeholder('00.000.000/0000-00')
                                ->dehydrateStateUsing(fn($state) => preg_replace('/\D/', '', $state))
                                ->rule([ValidationRules::cnpj()]),
                            TextInput::make('state_registration')->label('Inscrição Estadual'),
                            TextInput::make('email')->label('E-mail')->email(),
                        ]),
                    Grid::make(2)
                        ->schema([
                            TextInput::make('phone')
                                ->label('Celular')
                                ->mask('(99) 99999-9999')
                                ->placeholder('(00) 00000-0000')
                                ->dehydrateStateUsing(fn($state) => preg_replace('/\D/', '', $state))
                                ->rule([ValidationRules::phone()]),
                            Toggle::make('is_active')
                                ->label('Ativa no sistema')
                                ->default(true)
                                ->visible(function (string $context): bool {
                                    $user = Auth::user();
                                    return $context === 'edit' && $user && $user->hasAnyRole(['Super Admin', 'Gerente']);
                                }),
                        ]),
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
                                ->placeholder('00000-000')
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
                ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('corporate_name')
                ->label('Razão Social')
                ->description(function ($record) {
                    if (! $record->address) {
                        return null;
                    }
                    $endereco = $record->address->formatted_address;
                    return "📍 {$endereco}";
                })
                ->wrap()
                ->sortable()
                ->searchable(),
            TextColumn::make('cnpj')
                ->label('CNPJ')
                ->sortable()
                ->formatStateUsing(fn($state) => \App\Support\ValidationRules::formatCnpj($state))
                ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('cell_phone')
                ->label('Celular')
                ->formatStateUsing(fn($state) => \App\Support\ValidationRules::formatPhone($state))
                ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                ->toggleable(isToggledHiddenByDefault: false),
            IconColumn::make('is_active')
                ->label('Ativa')
                ->boolean()
                ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                ->toggleable(isToggledHiddenByDefault: true),
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
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
//            'view' => Pages\ViewSupplier::route('/{record}'),
        ];
    }
}
