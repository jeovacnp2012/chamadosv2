<?php

namespace App\Filament\Resources;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\PriceAgreementResource\Pages;
use App\Models\PriceAgreement;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Log;

class PriceAgreementResource extends Resource
{
    use ChecksResourcePermission;

    protected static ?string $model = PriceAgreement::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text'; // 📄
    protected static ?string $navigationGroup = 'Licitações e Contratos';
    protected static ?string $navigationLabel = 'Atas ';
    protected static ?string $modelLabel = 'Ata';
    protected static ?string $pluralModelLabel = 'Atas';
    public static function canViewAny(): bool
    {
        Log::info('🔍 canViewAny chamado em: ' . static::class);
        return self::checkPermission('view');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informações da Ata')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('number')
                                ->label('Número')
                                ->required(),
                            TextInput::make('year')
                                ->label('Ano')
                                ->numeric()
                                ->required(),
                            Select::make('supplier_id')
                                ->label('Executor')
                                ->relationship('supplier', 'trade_name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),
                    Grid::make(2)
                        ->schema([
                            DatePicker::make('signature_date')
                                ->label('Data de Assinatura')
                                ->date('d/m/Y'),
                            DatePicker::make('valid_until')
                                ->label('Vencimento ata')
                                ->date('d/m/Y'),
                        ]),
                    Textarea::make('object')
                        ->label('Objeto do Pregão')
                        ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                        ->columnSpanFull(),
                ])->columns(1)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('number')
                ->label('Número')
                ->searchable()
                ->sortable(),
            TextColumn::make('year')
                ->label('Ano')
                ->date('Y')
                ->searchable(),
            TextColumn::make('supplier.trade_name')
                ->label('Executor')
                ->searchable(),
            TextColumn::make('valid_until')
                ->label('Validade')
                ->date('d/m/Y')
                ->searchable(),
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
            'index' => Pages\ListPriceAgreements::route('/'),
            'create' => Pages\CreatePriceAgreement::route('/create'),
            'edit' => Pages\EditPriceAgreement::route('/{record}/edit'),
//            'view' => Pages\ViewPriceAgreement::route('/{record}'),
        ];
    }
}
