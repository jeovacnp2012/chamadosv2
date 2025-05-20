<?php

namespace App\Filament\Resources;

use App\Traits\ChecksResourcePermission;
use App\Models\Patrimony;
use App\Models\Sector;
use App\Models\User;
use App\Filament\Resources\CalledResource\Pages\ChatPage;
use App\Filament\Resources\CalledResource\Pages;
use App\Filament\Resources\CalledResource\RelationManagers;
use App\Models\Called;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
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
                    Select::make('called_type_id')
                        ->label('Tipo de chamado')
                        ->relationship('calledType', 'name')
                        ->required()
                        ->native(false)
                        ->live(),
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
                                        $tipoChamado = $get('called_type_id');
                                        $sectorId = $get('sector_id');
                                        if (!$sectorId || !$tipoChamado) {
                                            return [];
                                        }
                                        $query = Patrimony::where('sector_id', $sectorId);

                                        if ($tipoChamado == 2) {
                                            // Serviços Gerais → apenas tag = 0
                                            $query->where('tag', 0);
                                        } elseif ($tipoChamado == 1) {
                                            // Patrimonial → tag > 0 e ativos
                                            $query->where('tag', '>', 0)->where('is_active', true);
                                        } else {
                                            return [];
                                        }
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
                                    ->visible(fn (callable $get) => in_array($get('called_type_id'), [1, 2])) // mostra para ambos os tipos
                                    ->dehydrated(fn (callable $get) => true) // sempre salva no banco
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
                            Grid::make(['default' => 1, 'md' => 2])
                                ->schema([
                                    DatePicker::make('closing_date')
                                        ->label('Data de Fechamento')
                                        ->visible(fn (string $context) => $context === 'edit')
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            // Se tem data => Fechado (F), senão => Aberto (A)
                                            $set('status', $state ? 'F' : 'A');
                                        }),
                                    Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'A' => 'Aberto',
                                            'F' => 'Fechado',
                                        ])
                                        ->default('A')
                                        ->required()
                                        ->visible(fn (string $context) => $context === 'edit')
                                        ->disabled(fn (callable $get) => $get('status') === 'F')
                                        ->dehydrated(true),
                                ]),
                        ])
                        ->visible(fn (callable $get) => filled($get('called_type_id'))),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('protocol')
                    ->label('Protocolo')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        $userName = 'Usuário não informado';
                        //dd($record->user_id);
                        //dd(User::find($record->user_id));
                        if ($record->user_id) {
                            $user = User::find($record->user_id);
                            $userName = $user ? $user->name : 'Usuário não encontrado';
                        }
                        $sectorName = $record->sector?->name ?? 'Setor não informado';
                        $executorName = $record->supplier?->trade_name ?? 'Executor não informado';
                        // Busca a TAG do patrimônio diretamente no banco de dados
                        $patrimonyTag = 'Sem patrimônio';
                        if ($record->patrimony_id === 0) {
                            $patrimonyTag = 'Serviços Gerais';
                        } elseif ($record->patrimony_id) {
                            $patrimony = Patrimony::find($record->patrimony_id);
                            $patrimonyTag = $patrimony ? "Plaqueta: {$patrimony->tag}" : 'Patrimônio não encontrado';
                        }

                        $problem = $record->problem ?? 'Sem descrição';

                        return "
                            <div style='display: grid; gap: 0.25rem;'>
                                <div><strong>Protocolo:</strong> {$state}</div>
                                <div><strong>Usuário:</strong> {$userName}</div>
                                <div><strong>Setor:</strong> {$sectorName}</div>
                                <div><strong>Executor:</strong> {$executorName}</div>
                                <div><strong>Patrimônio:</strong> {$patrimonyTag}</div>
                                <div style='word-break: break-word; max-width: 300px; white-space: pre-line;'>
                                    <strong>Problema:</strong> {$problem}
                                </div>
                            </div>
                        ";
                    })
                    ->html()
                    ->wrap()
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: false)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable()
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sector.name')
                    ->label('Setor')
                    ->searchable()
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('supplier.trade_name')
                    ->label('Executor')
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('patrimony.tag')
                    ->label('Patrimônio')
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => $state === 'F' ? 'success' : 'warning')
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('type_maintenance')
                    ->label('Tipo')
                    ->badge()
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('closing_date')
                    ->label('Fechamento')
                    ->date('d/m/Y')
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('Visualizar')->icon('heroicon-o-eye'),
                    EditAction::make()->label('Editar')->icon('heroicon-o-pencil'),
                    DeleteAction::make()->label('Excluir')->icon('heroicon-o-trash'),
                    Action::make('chat')
                        ->label('Chat')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->url(fn ($record) => route('filament.admin.resources.calleds.chat-page', $record))
                        ->url(fn ($record) => ChatPage::getUrl(['record' => $record])),
                    ])

                    ->button()
                    ->label('Ações'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with([
                'user' => fn($query) => $query->select('id', 'name'),
                'patrimony' => fn($query) => $query->select('id', 'tag'),
                'sector' => fn($query) => $query->select('id', 'name'),
                'supplier' => fn($query) => $query->select('id', 'trade_name')
            ]);

        if (auth()->user()?->hasRole('Super Admin')) {
            return $query;
        }

        return $query->whereIn('sector_id', collect(auth()->user()?->departaments)
            ->flatMap->sectors
            ->pluck('id'));
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
            'chat' => ChatPage::route('/{record}/chat'),
        ];
    }
}
