<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AtaItemResource\Pages;
use App\Filament\Resources\AtaItemResource\RelationManagers;
use App\Models\AgreementItem;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgreementItemResource extends Resource
{
    protected static ?string $model = AgreementItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('ata_id')->label('PriceAgreement')->relationship('ata', 'number')->searchable(),

                TextInput::make('code')->label('Código')->required(),
                Textarea::make('description')->label('Descrição')->required(),
                TextInput::make('quantity')->label('Quantidade')->numeric()->required(),
                TextInput::make('unit_price')->label('Preço')->numeric()->prefix('R$'),
                Select::make('unit')->label('Unidade')->options([
                    'UN' => 'Unidade',
                    'M2' => 'Metro quadrado',
                    'HR' => 'Hora',
                ]),
                Select::make('type')->label('Tipo')->options([
                    'part' => 'Peça',
                    'service' => 'Serviço',
                ]),
                Toggle::make('is_active')->label('Ativo'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
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
            'index' => Pages\ListAtaItems::route('/'),
            'create' => Pages\CreateAtaItem::route('/create'),
            'edit' => Pages\EditAtaItem::route('/{record}/edit'),
        ];
    }
}
