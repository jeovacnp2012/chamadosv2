<?php

namespace App\Filament\Resources;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\AgreementItemResource\Pages;
use App\Models\AgreementItem;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class AgreementItemResource extends Resource
{

    protected function authorizeAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }
    protected static ?string $model = AgreementItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box'; // 📦
    protected static ?string $navigationGroup = 'Licitações e Contratos';
    protected static ?string $navigationLabel = 'Itens da Ata';
    protected static ?string $modelLabel = 'Item da Ata';
    protected static ?string $pluralModelLabel = 'Itens da Ata';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Dados do Item')
                ->schema([
                    Select::make('price_agreement_id')
                        ->label('Ata')
                        ->relationship('priceAgreement', 'number')
                        ->searchable(),
                    Grid::make(3)->schema([
                        TextInput::make('code')
                            ->label('Código'),
                        TextInput::make('quantity')
                            ->label('Quantidade')->numeric(),
                        TextInput::make('unit_price')
                            ->label('Preço Unitário')
                            ->numeric()->prefix('R$'),
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('unit')->label('Unidade'),
                        Select::make('type')->label('Tipo')->options([
                            'part' => 'Peça',
                            'service' => 'Serviço',
                        ]),
                    ]),
                    Textarea::make('description')->label('Descrição')->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label('Ativa no sistema')
                        ->default(true)
                        ->visible(function (string $context): bool {
                            $user = Auth::user();
                            return $context === 'edit' && $user && $user->hasAnyRole(['Super Admin', 'Gerente']);
                        }),
                ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')
                ->label('Código'),
            TextColumn::make('description')
                ->label('Descrição')
                ->limit(40),
            TextColumn::make('unit_price')
                ->label('Preço')
                ->money('BRL'),
            TextColumn::make('type')
                ->label('Tipo')
                ->badge(),
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
            'index' => Pages\ListAgreementItems::route('/'),
            'create' => Pages\CreateAgreementItem::route('/create'),
            'edit' => Pages\EditAgreementItem::route('/{record}/edit'),
//            'view' => Pages\ViewAgreementItem::route('/{record}'),
        ];
    }
}
