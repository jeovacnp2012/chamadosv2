<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AddressResource\Pages;
use App\Filament\Resources\AddressResource\RelationManagers;
use App\Models\Address;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http;

class AddressResource extends Resource
{
    protected static ?string $model = Address::class;

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
        return $table
            ->columns([
                TextColumn::make('postal_code')->label('CEP'),
                TextColumn::make('street')->label('Rua')->searchable(),
                TextColumn::make('neighborhood')->label('Bairro'),
                TextColumn::make('city')->label('Cidade'),
                TextColumn::make('state')->label('UF'),
            ])->defaultSort('postal_code')
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
            'index' => Pages\ListAddresses::route('/'),
            'create' => Pages\CreateAddress::route('/create'),
            'edit' => Pages\EditAddress::route('/{record}/edit'),
        ];
    }
}
