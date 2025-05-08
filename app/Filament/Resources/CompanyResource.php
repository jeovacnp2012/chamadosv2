<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Empresas';
    protected static ?string $modelLabel = 'Empresa';
    protected static ?string $pluralModelLabel = 'Empresas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('corporate_name')
                    ->label('Razão Social')
                    ->required(),

                TextInput::make('trade_name')
                    ->label('Nome Fantasia'),

                TextInput::make('state_registration')
                    ->label('Inscrição Estadual'),

                TextInput::make('cnpj')
                    ->label('CNPJ')
                    ->required(),

                TextInput::make('phone')
                    ->label('Celular'),

                TextInput::make('email')
                    ->label('Email')
                    ->email(),

                Toggle::make('is_active')
                    ->label('Ativa no sistema')
                    ->default(true),

                Select::make('address_id')
                    ->label('Endereço')
                    ->options(function () {
                        return \App\Models\Address::all()->mapWithKeys(function ($address) {
                            return [$address->id => $address->formatted_address];
                        });
                    })
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('corporate_name')->label('Razão Social')
                    ->description(function ($record) {
                        if (! $record->address) return null;

                        $endereco = $record->address->formatted_address;

                        return "<div style='
                                    background-color: #f3f4f6;
                                    color: #374151;
                                    padding: 4px 8px;
                                    border-radius: 6px;
                                    font-size: 12px;
                                    display: inline-block;
                                '>
                            📍 $endereco
                        </div>";
                    })
                    ->html()
                    ->wrap()
                    ->searchable(),
                TextColumn::make('trade_name')->label('Fantasia'),
                TextColumn::make('cnpj')->label('CNPJ'),
                IconColumn::make('is_active')
                    ->label('Ativa')
                    ->boolean(),
            ])->defaultSort('corporate_name')
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
