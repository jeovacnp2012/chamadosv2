<?php

namespace App\Filament\Resources;

use App\Models\Departament;
use App\Models\Sector;
use App\Traits\ChecksResourcePermission;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;

class UserResource extends Resource
{
    use ChecksResourcePermission;

    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?string $navigationLabel = 'Usuários';
    protected static ?string $pluralModelLabel = 'Usuários';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        // Se o usuário logado NÃO for super admin, filtra os super admins
        if (! auth()->user()->hasRole('Super Admin')) {
            $query->whereDoesntHave('roles', fn($q) =>
            $q->where('name', 'Super Admin')
            );
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Vínculo com Empresa')
                ->visible(fn () => auth()->user()?->hasRole('Super Admin'))
                ->schema([
                    Grid::make(['sm' => 1, 'md' => 2])
                        ->schema([
                            Select::make('company_id')
                                ->label('Empresa')
                                ->relationship('company', 'trade_name')
                                ->searchable()
                                ->preload()
                                ->visible(fn ($record) => auth()->user()?->hasRole('Super Admin') && auth()->id() !== $record?->id)
                                ->required(fn ($record) => auth()->user()?->hasRole('Super Admin') && auth()->id() !== $record?->id)
                                ->dehydrated(fn ($record) => auth()->user()?->hasRole('Super Admin') && auth()->id() !== $record?->id)
                                ->default(fn ($record) => $record?->company_id ?? auth()->user()?->company_id),
                            Select::make('supplier_id')
                                ->label('Executor vinculado')
                                ->relationship('supplier', 'trade_name')
                                ->searchable()
                                ->preload()
                                ->visible(fn () => !auth()->user()?->hasRole('Executor'))
                        ]),
                ]),
            Section::make('Dados do Usuário')
                ->schema([
                    Grid::make(['sm' => 1, 'md' => 2])->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                            ->required(),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->required()
                            ->dehydrateStateUsing(fn ($state) => strtolower($state))
                            ->email()
                            ->unique(ignoreRecord: true),
                    ]),

                    Grid::make(['sm' => 1, 'md' => 2])->schema([
                        TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->revealable()
                            ->required(fn(string $context) => $context === 'create')
                            ->dehydrateStateUsing(fn($state) => filled($state) ? \Illuminate\Support\Facades\Hash::make($state) : null)
                            ->dehydrated(fn($state) => filled($state)),

                        // ✅ CAMPO IS_ACTIVE - SÓ NA EDIÇÃO
                        Toggle::make('is_active')
                            ->label('Usuário Ativo')
                            ->helperText('Desative para impedir que o usuário acesse o sistema')
                            ->default(true)
                            ->visible(fn (string $context) => $context === 'edit')
                            ->columnSpan(1),
                    ]),
                ]),
            Section::make('Acesso e Segurança')
                ->schema([
                    Grid::make(['sm' => 1, 'md' => 2])->schema([
                        CheckboxList::make('roles')
                            ->label('Papéis')
                            ->relationship('roles', 'name')
                            ->options(function () {
                                $query = \Spatie\Permission\Models\Role::query();
                                if (! auth()->user()?->hasRole('Super Admin')) {
                                    $query->where('name', '!=', 'Super Admin');
                                }
                                return $query->pluck('name', 'id');
                            })
                            ->columns(['sm' => 1, 'md' => 2])
                            ->required(),
                    ]),
                ]),
            Section::make('Departamentos permitidos')
                ->schema([
                    Select::make('departaments')
                        ->label('Departamentos permitidos')
                        ->relationship('departaments', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->hint('Selecione os departamentos para filtrar os setores disponíveis')
                        ->afterStateUpdated(function ($state, $set) {
                            $set('sectors', []);
                        })
                        ->options(function () {
                            $query = Departament::orderBy('name');

                            if (!auth()->user()->hasRole('Super Admin')) {
                                $query->whereIn('id', auth()->user()->departaments->pluck('id'));
                            }

                            return $query->pluck('name', 'id');
                        })
                        ->visible(fn () => auth()->user()?->hasRole('Super Admin'))
                        ->columnSpanFull()
                ]),
            Section::make('Setores permitidos')
                ->schema([
                    Select::make('sectors')
                        ->label('Setores permitidos')
                        ->relationship('sectors', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->hint(fn () => auth()->user()->hasRole('Super Admin')
                            ? 'Setores serão filtrados pelos departamentos selecionados'
                            : 'Você só pode selecionar setores dos seus departamentos')
                        ->options(function (callable $get) {
                            $departamentIds = $get('departaments') ?? [];

                            $query = Sector::orderBy('name');

                            if (!empty($departamentIds)) {
                                $query->whereIn('departament_id', $departamentIds);
                            } elseif (!auth()->user()->hasRole('Super Admin')) {
                                $query->whereIn('departament_id', auth()->user()->departaments->pluck('id'));
                            }

                            return $query->pluck('name', 'id');
                        })
                        ->visible(fn () => auth()->user()?->hasRole('Super Admin'))
                        ->columnSpanFull(),
                    // Seção para usuários normais (não-admin)
                    Section::make('Meus Setores')
                        ->schema([
                            Select::make('sectors')
                                ->label('Setores permitidos')
                                ->relationship('sectors', 'name')
                                ->multiple()
                                ->hint(fn () => auth()->user()->hasRole('Super Admin')
                                    ? 'Setores serão filtrados pelos departamentos selecionados'
                                    : 'Você só pode selecionar setores dos seus departamentos')
                                ->options(fn () =>
                                auth()->user()->sectors()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                )
                                ->visible(fn () => !auth()->user()?->hasRole('Super Admin'))
                                ->columnSpanFull()
                        ])->visible(fn () => !auth()->user()?->hasRole('Super Admin')),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->filtersTriggerAction(fn(Action $action) =>
            $action->icon('heroicon-s-adjustments-vertical')
                ->slideOver()
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Perfil')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),
                // ✅ COLUNA IS_ACTIVE (opcional, pode remover se não quiser mostrar)
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->headerActions([
                // ✅ AÇÃO PARA GERAR PDF
                Action::make('exportToPdf')
                    ->label('Gerar PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        return static::generateUsersPdf($livewire);
                    }),
            ])
            ->filters([
                // ✅ FILTRO DE STATUS DOS USUÁRIOS
                TernaryFilter::make('is_active')
                    ->label('Status do Usuário')
                    ->trueLabel('Somente Ativos')
                    ->falseLabel('Somente Inativos')
                    ->default(true),

                // ✅ FILTRO POR DEPARTAMENTO E SETOR
                Filter::make('filtro_departamento_setor')
                    ->label('Filtro por Departamento e Setor')
                    ->form([
                        Select::make('departament_ids')
                            ->label('Departamentos')
                            ->multiple()
                            ->searchable()
                            ->live()
                            ->options(function () {
                                $user = auth()->user();
                                if ($user->hasRole('Super Admin')) {
                                    // Super Admin vê todos os departamentos
                                    return \App\Models\Departament::orderBy('name')->pluck('name', 'id');
                                }
                                // Usuário normal vê apenas departamentos dos seus setores
                                return $user->sectors()
                                    ->with('departament')
                                    ->get()
                                    ->pluck('departament')
                                    ->filter()
                                    ->unique('id')
                                    ->sortBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->placeholder('Selecione os departamentos'),

                        Select::make('sector_ids')
                            ->label('Setores')
                            ->multiple()
                            ->searchable()
                            ->live()
                            ->options(function (callable $get) {
                                $departamentIds = $get('departament_ids');
                                $user = auth()->user();
                                if (empty($departamentIds)) {
                                    return [];
                                }
                                $query = \App\Models\Sector::query()
                                    ->whereIn('departament_id', $departamentIds);

                                // Se não for Super Admin, limita aos setores do usuário
                                if (!$user->hasRole('Super Admin')) {
                                    $query->whereIn('id', $user->sectors->pluck('id'));
                                }
                                return $query->orderBy('name')->pluck('name', 'id');
                            })
                            ->disabled(fn (callable $get) => empty($get('departament_ids')))
                            ->placeholder('Primeiro selecione os departamentos')
                    ])
                    ->columnSpanFull()
                    ->query(function ($query, array $data) {
                        // Filtrar usuários por departamentos selecionados
                        if (!empty($data['departament_ids'])) {
                            $query->whereHas('sectors', function ($q) use ($data) {
                                $q->whereIn('departament_id', $data['departament_ids']);
                            });
                        }

                        // Filtrar usuários por setores selecionados (mais específico)
                        if (!empty($data['sector_ids'])) {
                            $query->whereHas('sectors', function ($q) use ($data) {
                                $q->whereIn('sectors.id', $data['sector_ids']);
                            });
                        }
                    })
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Calcula estatísticas de chamados para o relatório
     */
    private static function calculateCallStatistics($statusFilter = 'ativos')
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->hasRole('Super Admin');

        // Verificar se existe a tabela called
        try {
            $testQuery = \DB::table('calleds')->count();
            \Log::info('Tabela calleds existe. Total de registros: ' . $testQuery);
        } catch (\Exception $e) {
            \Log::error('Erro ao acessar tabela calleds: ' . $e->getMessage());
            return [
                'user_type' => $isSuperAdmin ? 'Super Admin' : 'Usuário Normal',
                'total_open' => 0,
                'total_closed' => 0,
                'today_open' => 0,
                'departments' => [],
                'sectors' => [],
                'message' => 'Tabela de chamados não encontrada: ' . $e->getMessage()
            ];
        }

        $statistics = [];

        if ($isSuperAdmin) {
            // ===== SUPER ADMIN: Estatísticas globais =====
            try {
                // Total geral de chamados
                $totalOpen = \App\Models\Called::where('status', 'A')->count();
                $totalClosed = \App\Models\Called::where('status', 'F')->count();

                // Chamados do dia corrente
                $todayOpen = \App\Models\Called::where('status', 'A')
                    ->whereDate('created_at', today())
                    ->count();

                // ===== Por departamento (SUPER ADMIN vê todos) =====
                $departmentStats = [];
                $departments = \App\Models\Departament::with('sectors')->get();

                foreach ($departments as $dept) {
                    if ($dept->sectors && $dept->sectors->isNotEmpty()) {
                        $sectorIds = $dept->sectors->pluck('id');

                        $openCount = \App\Models\Called::whereIn('sector_id', $sectorIds)
                            ->where('status', 'A')
                            ->count();
                        $closedCount = \App\Models\Called::whereIn('sector_id', $sectorIds)
                            ->where('status', 'F')
                            ->count();

                        if ($openCount > 0 || $closedCount > 0) {
                            $departmentStats[] = [
                                'name' => $dept->name,
                                'open' => $openCount,
                                'closed' => $closedCount,
                                'total' => $openCount + $closedCount
                            ];
                        }
                    }
                }

                // ===== Por setor (Top 10 para Super Admin) =====
                $sectorStats = [];
                $sectors = \App\Models\Sector::all();

                foreach ($sectors as $sector) {
                    $openCount = \App\Models\Called::where('sector_id', $sector->id)
                        ->where('status', 'A')
                        ->count();
                    $closedCount = \App\Models\Called::where('sector_id', $sector->id)
                        ->where('status', 'F')
                        ->count();

                    if ($openCount > 0 || $closedCount > 0) {
                        $sectorStats[] = [
                            'name' => $sector->name,
                            'open' => $openCount,
                            'closed' => $closedCount,
                            'total' => $openCount + $closedCount
                        ];
                    }
                }

                // Ordenar setores por total e pegar os top 10
                usort($sectorStats, function($a, $b) {
                    return $b['total'] - $a['total'];
                });
                $sectorStats = array_slice($sectorStats, 0, 10);

                $statistics = [
                    'user_type' => 'Super Admin',
                    'total_open' => $totalOpen,
                    'total_closed' => $totalClosed,
                    'today_open' => $todayOpen,
                    'departments' => $departmentStats,
                    'sectors' => $sectorStats
                ];

            } catch (\Exception $e) {
                \Log::error('Erro ao calcular estatísticas Super Admin: ' . $e->getMessage());
                return [
                    'user_type' => 'Super Admin',
                    'total_open' => 0,
                    'total_closed' => 0,
                    'today_open' => 0,
                    'departments' => [],
                    'sectors' => [],
                    'message' => 'Erro ao calcular estatísticas: ' . $e->getMessage()
                ];
            }

        } else {
            // ===== USUÁRIO NORMAL: Apenas setores vinculados =====
            try {
                $userSectors = $currentUser->sectors;

                if ($userSectors->isEmpty()) {
                    return [
                        'user_type' => 'Usuário Normal',
                        'total_open' => 0,
                        'total_closed' => 0,
                        'today_open' => 0,
                        'departments' => [],
                        'sectors' => [],
                        'message' => 'Usuário sem setores vinculados'
                    ];
                }

                // Chamados dos setores do usuário
                $userSectorIds = $userSectors->pluck('id');

                $totalOpen = \App\Models\Called::whereIn('sector_id', $userSectorIds)
                    ->where('status', 'A')
                    ->count();
                $totalClosed = \App\Models\Called::whereIn('sector_id', $userSectorIds)
                    ->where('status', 'F')
                    ->count();

                // Chamados do dia corrente
                $todayOpen = \App\Models\Called::whereIn('sector_id', $userSectorIds)
                    ->where('status', 'A')
                    ->whereDate('created_at', today())
                    ->count();

                // ===== Por departamento (apenas dos setores do usuário) =====
                $departmentStats = [];

                // Pegar departamentos únicos dos setores do usuário
                $userDepartmentIds = $userSectors->pluck('departament_id')->unique();
                $userDepartments = \App\Models\Departament::whereIn('id', $userDepartmentIds)->get();

                foreach ($userDepartments as $dept) {
                    // Pegar apenas os setores do usuário que pertencem a este departamento
                    $deptUserSectorIds = $userSectors->where('departament_id', $dept->id)->pluck('id');

                    $openCount = \App\Models\Called::whereIn('sector_id', $deptUserSectorIds)
                        ->where('status', 'A')
                        ->count();
                    $closedCount = \App\Models\Called::whereIn('sector_id', $deptUserSectorIds)
                        ->where('status', 'F')
                        ->count();

                    if ($openCount > 0 || $closedCount > 0) {
                        $departmentStats[] = [
                            'name' => $dept->name,
                            'open' => $openCount,
                            'closed' => $closedCount,
                            'total' => $openCount + $closedCount
                        ];
                    }
                }

                // ===== Por setor (apenas setores do usuário) =====
                $sectorStats = [];
                foreach ($userSectors as $sector) {
                    $openCount = \App\Models\Called::where('sector_id', $sector->id)
                        ->where('status', 'A')
                        ->count();
                    $closedCount = \App\Models\Called::where('sector_id', $sector->id)
                        ->where('status', 'F')
                        ->count();

                    if ($openCount > 0 || $closedCount > 0) {
                        $sectorStats[] = [
                            'name' => $sector->name,
                            'open' => $openCount,
                            'closed' => $closedCount,
                            'total' => $openCount + $closedCount
                        ];
                    }
                }

                // Ordenar setores por total
                usort($sectorStats, function($a, $b) {
                    return $b['total'] - $a['total'];
                });

                $statistics = [
                    'user_type' => 'Usuário Normal',
                    'total_open' => $totalOpen,
                    'total_closed' => $totalClosed,
                    'today_open' => $todayOpen,
                    'departments' => $departmentStats,
                    'sectors' => $sectorStats
                ];

            } catch (\Exception $e) {
                \Log::error('Erro ao calcular estatísticas Usuário Normal: ' . $e->getMessage());
                return [
                    'user_type' => 'Usuário Normal',
                    'total_open' => 0,
                    'total_closed' => 0,
                    'today_open' => 0,
                    'departments' => [],
                    'sectors' => [],
                    'message' => 'Erro ao calcular estatísticas: ' . $e->getMessage()
                ];
            }
        }

        \Log::info('Estatísticas finais calculadas:', ['statistics' => $statistics]);
        return $statistics;
    }

    /**
     * Gera PDF dos usuários com base nos filtros aplicados
     */
    public static function generateUsersPdf($livewire)
    {
        // Obter os registros filtrados usando a query da própria tabela
        $query = static::getEloquentQuery();

        // Acessar os filtros aplicados
        $tableFilters = $livewire->tableFilters ?? [];

        // Aplicar filtro de status se existir
        $filterStatus = $tableFilters['status_filter'] ?? 'ativos';
        if (isset($tableFilters['status_filter']) && !empty($tableFilters['status_filter'])) {
            $statusFilter = $tableFilters['status_filter'];
            match ($statusFilter) {
                'ativos' => $query->where('is_active', true),
                'inativos' => $query->where('is_active', false),
                'todos' => $query, // Sem filtro adicional
                default => $query->where('is_active', true),
            };
        } else {
            // Filtro padrão se nenhum foi selecionado
            $query->where('is_active', true);
        }

        // Aplicar busca se existir
        $search = $livewire->tableSearch ?? '';
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Carregar relacionamentos necessários
        $users = $query->with(['roles', 'departaments', 'sectors'])
            ->orderBy('name')
            ->get();

        // Determinar o status do filtro para o título
        $filterStatus = $tableFilters['status_filter'] ?? 'ativos';
        $statusText = match($filterStatus) {
            'ativos' => 'Usuários Ativos',
            'inativos' => 'Usuários Inativos',
            'todos' => 'Todos os Usuários',
            default => 'Usuários Ativos'
        };

        // ✅ CALCULAR ESTATÍSTICAS DE CHAMADOS
        $statistics = static::calculateCallStatistics($filterStatus);

        // DEBUG - Verificar se as estatísticas estão sendo geradas
        \Log::info('Estatísticas calculadas:', ['statistics' => $statistics]);

        // Preparar dados para o PDF
        $data = [
            'users' => $users,
            'title' => 'Relatório de Usuários',
            'subtitle' => $statusText,
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'generated_by' => auth()->user()->name,
            'total_users' => $users->count(),
            'search_term' => $search,
            'statistics' => $statistics, // ✅ ADICIONANDO AS ESTATÍSTICAS
        ];

        // Gerar PDF
        $pdf = Pdf::loadView('pdf.users-report', $data)
            ->setPaper('a4', 'landscape') // ✅ MUDANÇA: PAISAGEM
            ->setOptions([
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
            ]);

        // Retornar download
        $filename = 'usuarios_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canEdit($record): bool
    {
        return ! $record->hasRole('Super Admin') || auth()->user()->hasRole('Super Admin');
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        // Nenhum usuário comum pode deletar a si mesmo
        if ($user->id === $record->id && ! $user->hasRole('Super Admin')) {
            return false;
        }
        // Super Admin pode tentar deletar a si mesmo (será bloqueado se for o único na execução)
        return true;
    }

    public static function canView($record): bool
    {
        return ! $record->hasRole('Super Admin') || auth()->user()->hasRole('Super Admin');
    }
}
