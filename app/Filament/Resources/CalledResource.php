<?php

namespace App\Filament\Resources;

use App\Models\Patrimony;
use App\Models\Sector;
use App\Models\Supplier;
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
            Section::make('Chamado')
                ->columns(['sm' => 1, 'md' => 2])
                ->schema([
                    Select::make('patrimony')
                        ->label('Tipo de chamado')
                        ->options([
                            ''  => 'Selecione um item', // Para evitar seleção automática
                            '1' => 'Patrimônio',
                            '0' => 'Serviços Gerais',
                        ])
                        ->native(false) // Usa um dropdown estilizado pelo Filament
                        ->default('') // Define "Selecione um item" como padrão
                        ->afterStateUpdated(function ($state, $get, $set) {
                            // Limpa o patrimônio se "Patrimônio" for selecionado
                            if ($state === '1') {
                                $set('patrimony_id', null); // Limpa o patrimônio
                            }
                        })
                        ->live()
                        ->helperText('Selecione o tipo de chamado para continuar.')
                        ->columnSpanFull(),
                    Grid::make()
                        ->schema([
                            TextInput::make('protocol')
                                ->label('Protocolo')
                                ->default(fn () => 'CHAM' . ((Called::max('id') ?? 0) + 1))
                                ->disabled()
                                ->dehydrated()
                                ->required(),
                            Select::make('type_maintenance')
                                ->label('Tipo de Manutenção')
                                ->options([
                                    'P' => 'Preventiva',
                                    'C' => 'Corretiva',
                                ])
                                ->default('C')
                                ->required(),
                            Grid::make()->schema([
                                Select::make('user_id')
                                    ->label('Usuário Solicitante')
                                    ->options(fn () => \App\Models\User::orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->default(auth()->id())
                                    ->disabled(fn () => !auth()->user()?->hasRole('Super Admin'))
                                    ->dehydrated()
                                    ->required(),
                                Select::make('sector_id')
                                    ->label('Setor')
                                    ->hint('Vem com 5 resultados na lista, caso queira mais, é só digitar.')
                                    ->options(function () {
                                        $user = auth()->user();

                                        return $user->hasRole('Super Admin')
                                            ? Sector::orderBy('name')->pluck('name', 'id')
                                            : $user->sectors->sortBy('name')->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload(5)
                                    ->live()
                                    ->required(),
                                Select::make('patrimony_id')
                                    ->label('Patrimônio')
                                    ->options(function (callable $get, ?string $search = null) {
                                        $tipoChamado = $get('patrimony');
                                        // Serviços Gerais: sempre uma única opção fixa
                                        if ($tipoChamado === '0') {
                                            return [0 => '0 · Serviços Gerais'];
                                        }
                                        // Se for Patrimonial, verifica se tem setor selecionado
                                        $sectorId = $get('sector_id');
                                        if (!$sectorId) {
                                            return [];
                                        }
                                        // Busca patrimônios ativos do setor, excluindo os que são "Serviços Gerais"
                                        $query = Patrimony::where('sector_id', $sectorId)
                                            ->where('is_active', true)
                                            ->where('description', 'not like', '%Serviços Gerais%');
                                        if ($search) {
                                            $query->where(function ($q) use ($search) {
                                                $q->where('tag', 'like', "%{$search}%")
                                                    ->orWhere('description', 'like', "%{$search}%");
                                            });
                                        }
                                        return $query->orderBy('tag')->get()->mapWithKeys(function ($p) {
                                            return [$p->id => "{$p->tag} · {$p->description}"];
                                        });
                                    })
                                    ->disabled(function (callable $get) {
                                        $tipoChamado = $get('patrimony');
                                        if ($tipoChamado === null) {
                                            return true; // Desabilita se não escolheu o tipo
                                        }
                                        if ($tipoChamado === '0') {
                                            return false; // Sempre habilitado para Serviços Gerais
                                        }
                                        return blank($get('sector_id')); // Patrimonial exige setor
                                    })
                                    ->searchable()
                                    ->preload(5)
                                    ->reactive()
                                    ->live()
                                    ->required(),
                                Select::make('supplier_id')
                                    ->label('Executor')
                                    ->options(function (?string $search = null) {
                                        $query = \App\Models\Supplier::where('is_active', true)->orderBy('trade_name');

                                        if ($search) {
                                            $query->where(function ($q) use ($search) {
                                                $q->where('trade_name', 'like', "%{$search}%")
                                                    ->orWhere('corporate_name', 'like', "%{$search}%");
                                            });
                                        }

                                        return $query->get()->mapWithKeys(fn ($s) => [
                                            $s->id => "{$s->trade_name} · {$s->corporate_name}",
                                        ]);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                            Textarea::make('problem')
                                ->label('Problema Relatado')
                                ->rows(3)
                                ->required()
                            ->columnSpanFull(),

                            DatePicker::make('closing_date')
                                ->label('Data de Fechamento')
                                ->columnSpanFull(),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'A' => 'Aberto',
                                    'F' => 'Fechado',
                                ])
                                ->default('A')
                                ->required()
                                ->visible(fn (string $context) => $context === 'edit')
                                ->dehydrated(),
                        ])
                        ->visible(fn (callable $get) => filled($get('patrimony'))),
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
