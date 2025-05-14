<?php

namespace App\Filament\Resources;

use App\Traits\ChecksResourcePermission;
use App\Filament\Resources\AddressResource\Pages;
use App\Filament\Resources\AddressResource\RelationManagers;
use App\Helpers\Formatter;
use App\Models\Address;
use App\Support\ValidationRules;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
// ✅ Adicionado para reconhecer a função global
use function responsiveColumnToggle;
class AddressResource extends Resource
{
    use ChecksResourcePermission;
    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static ?string $model = Address::class;
    protected static ?string $navigationGroup = 'Cadastro';
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Endereços';
    protected static ?string $modelLabel = 'Endereço';
    protected static ?string $pluralModelLabel = 'Endereços';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('postal_code')->label('CEP')
                    ->mask('99999-999')
                    ->required()
                    ->formatStateUsing(function ($state) {
                        if (!$state || strlen($state) !== 8) {
                            return $state;
                        }
                        return preg_replace('/^(\d{5})(\d{3})$/', '$1-$2', $state);
                    })
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $cep = preg_replace('/[^0-9]/', '', $state);
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
            ]);
    }

    public static function table(Table $table): Table
    {
        // Chama a função e armazena na variável
        $settings = responsiveColumnToggle();
        return $table
            ->columns([
                TextColumn::make('postal_code')
                    ->label('CEP')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('street')
                    ->label('Rua')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('neighborhood')
                    ->label('Bairro')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: false)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: false)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('city')
                    ->label('Cidade')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('state')
                    ->label('UF')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: false),
            ])->defaultSort('postal_code')
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
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
            'index' => Pages\ListAddresses::route('/'),
            'create' => Pages\CreateAddress::route('/create'),
            'edit' => Pages\EditAddress::route('/{record}/edit'),
        ];
    }
//    public static function canViewAny(): bool
//    {
//        return auth()->user()->can('view address');
//    }
//
//    public static function canCreate(): bool
//    {
//        return auth()->user()->can('create address');
//    }
//
//    public static function canEdit(Model $record): bool
//    {
//        return auth()->user()->can('update address');
//    }
//
//    public static function canDelete(Model $record): bool
//    {
//        return auth()->user()->can('delete address');
//    }

}
