<?php

namespace App\Filament\Resources;

use App\Traits\ChecksResourcePermission;

use App\Filament\Resources\CalledResource\Pages;
use App\Filament\Resources\CalledResource\RelationManagers;
use App\Models\Called;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CalledResource extends Resource
{
    use ChecksResourcePermission;
    protected static ?string $model = Called::class;
    protected static ?string $navigationGroup = 'Cadastro';

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Chamados';
    protected static ?string $pluralModelLabel = 'Chamados';
    protected static ?string $modelLabel = 'Chamado';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informações do Chamado')
                ->columns(['sm' => 1, 'md' => 2])
                ->schema([
                    Grid::make()->schema([
                        Select::make('user_id')
                            ->label('Usuário Solicitante')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => auth()->id())
                            ->disabled(fn () => !auth()->user()?->hasRole('Super Admin')) // desativa para não-admins
                            ->dehydrated(),

                        Select::make('sector_id')
                            ->label('Setor')
                            ->relationship('sector', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('supplier_id')
                            ->label('Executor')
                            ->relationship('supplier', 'trade_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('patrimony_id')
                            ->label('Patrimônio')
                            ->relationship('patrimony', 'tag')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                    Textarea::make('problem')
                        ->label('Problema Relatado')
                        ->rows(3)
                        ->required(),
                ]),

            Section::make('Informações do Processo')
                ->columns(['sm' => 1, 'md' => 2])
                ->schema([
                    Grid::make()->schema([
                        TextInput::make('protocol')
                            ->label('Protocolo')
                            ->default(fn () => 'CHAM-' . ((Called::max('id') ?? 0) + 1))
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'A' => 'Aberto',
                                'F' => 'Fechado',
                            ])
                            ->required(),

                        Select::make('type_maintenance')
                            ->label('Tipo de Manutenção')
                            ->options([
                                'P' => 'Preventiva',
                                'C' => 'Corretiva',
                            ])
                            ->required(),

                        DatePicker::make('closing_date')
                            ->label('Data de Fechamento'),

                        Select::make('patrimony')
                            ->label('É Patrimonial?')
                            ->options([
                                1 => 'Sim',
                                0 => 'Não',
                            ]),
                    ])
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('protocol')->label('Protocolo')->searchable()->sortable(),
                TextColumn::make('user.name')->label('Usuário')->searchable(),
                TextColumn::make('sector.name')->label('Setor')->searchable(),
                TextColumn::make('supplier.trade_name')->label('Executor'),
                TextColumn::make('patrimony.tag')->label('Patrimônio'),
                TextColumn::make('status')->label('Status')->badge()->color(fn($state) => $state === 'F' ? 'success' : 'warning'),
                TextColumn::make('type_maintenance')->label('Tipo')->badge(),
                TextColumn::make('closing_date')->label('Fechamento')->date('d/m/Y'),
            ])
            ->defaultSort('id', 'desc')
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
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->hasRole('Super Admin')) {
            return $query;
        }

        return $query->whereIn('sector_id', collect(auth()->user()?->departaments)->flatMap->sectors->pluck('id'));
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
            'index' => Pages\ListCalleds::route('/'),
            'create' => Pages\CreateCalled::route('/create'),
            'edit' => Pages\EditCalled::route('/{record}/edit'),
        ];
    }
}
