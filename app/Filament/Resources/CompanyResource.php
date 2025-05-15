<?php

namespace App\Filament\Resources;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use App\Support\ValidationRules;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
// ✅ Adicionado para reconhecer a função global
use function responsiveColumnToggle;
class CompanyResource extends Resource
{
    use ChecksResourcePermission;
    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static ?string $model = Company::class;
    protected static ?string $navigationGroup = 'Cadastro';
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Empresas';
    protected static ?string $modelLabel = 'Empresa';
    protected static ?string $pluralModelLabel = 'Empresas';
    public static function canViewAny(): bool
    {
        Log::info('🔍 canViewAny chamado em: ' . static::class);
        return self::checkPermission('view');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações da Empresa')
                    ->description('Dados principais da empresa cadastrada')
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 3,
                    ])
                    ->schema([
                        TextInput::make('corporate_name')
                            ->label('Razão Social')
                            ->required()
                            ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                        TextInput::make('trade_name')
                            ->label('Nome Fantasia')
                            ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                        TextInput::make('state_registration')
                            ->label('Inscrição Estadual'),
                    ]),
                Section::make('Contato e Identificação')
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        TextInput::make('cnpj')
                            ->label('CNPJ')
                            ->required()
                            ->mask('99.999.999/9999-99')
                            ->placeholder('00.000.000/0000-00')
                            ->dehydrateStateUsing(fn($state) => preg_replace('/\D/', '', $state))
                            ->rule([ValidationRules::cnpj()]),
                        TextInput::make('phone')
                            ->label('Celular')
                            ->mask('(99) 99999-9999')
                            ->placeholder('(00) 00000-0000')
                            ->dehydrateStateUsing(fn($state) => preg_replace('/\D/', '', $state))
                            ->rule([ValidationRules::phone()]),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->dehydrateStateUsing(fn ($state) => strtolower($state)),
                    ]),
                Section::make('Endereço e Status')
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                TextColumn::make('trade_name')
                    ->label('Fantasia')
                    ->sortable()
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cnpj')
                    ->label('CNPJ')
                    ->sortable()
                    ->formatStateUsing(fn($state) => ValidationRules::cnpj($state))
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Ativa')
                    ->boolean()
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone')
                    ->label('Celular')
                    ->formatStateUsing(fn($state) => ValidationRules::formatPhone($state))
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),

            ])->defaultSort('corporate_name')
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
            'view' => Pages\ViewCompany::route('/{record}'),
        ];
    }
//    public static function canViewAny(): bool
//    {
//        return auth()->user()->can('view company');
//    }
//
//    public static function canCreate(): bool
//    {
//        return auth()->user()->can('create company');
//    }
//
//    public static function canEdit(Model $record): bool
//    {
//        return auth()->user()->can('update company');
//    }
//
//    public static function canDelete(Model $record): bool
//    {
//        return auth()->user()->can('delete company');
//    }
}
