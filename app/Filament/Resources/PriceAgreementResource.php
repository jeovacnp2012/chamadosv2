<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AtaResource\Pages;
use App\Filament\Resources\AtaResource\RelationManagers;
use App\Models\PriceAgreement;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PriceAgreementResource extends Resource
{
    protected static ?string $model = PriceAgreement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('number')->label('Número')->required(),
                TextInput::make('year')->label('Ano')->numeric()->required(),
                Textarea::make('object')->label('Objeto do Pregão')->columnSpanFull(),

                DatePicker::make('signature_date')->label('Data da Assinatura'),
                DatePicker::make('valid_until')->label('Validade da PriceAgreement'),

                Select::make('executor_id')
                    ->label('Supplier')
                    ->relationship('executor', 'trade_name')
                    ->searchable()
                    ->required(),
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
            'index' => Pages\ListAtas::route('/'),
            'create' => Pages\CreateAta::route('/create'),
            'edit' => Pages\EditAta::route('/{record}/edit'),
        ];
    }
}
