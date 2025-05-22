<?php

namespace App\Filament\Resources;

use App\Models\Departament;
use App\Models\Sector;
use App\Traits\ChecksResourcePermission;
use App\Filament\Resources\PatrimonyResource\Pages;
use App\Filament\Resources\PatrimonyResource\RelationManagers;
use App\Models\Patrimony;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PatrimonyResource extends Resource
{
    use ChecksResourcePermission;
    protected static ?string $model = Patrimony::class;
    protected static ?string $navigationGroup = 'Cadastro';
    protected static ?string $navigationLabel = 'Patrimônios';
    protected static ?string $pluralModelLabel = 'Patrimônios';
    protected static ?string $modelLabel = 'Patrimônio';

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados Gerais')
                    ->schema([
                        Grid::make(['sm' => 1, 'md' => 2])->schema([
                            Select::make('sector_id')
                                ->label('Setor')
                                ->searchable()
                                ->preload()
                                ->options(function () {
                                    $user = auth()->user();

                                    $query = Sector::query()->orderBy('name');

                                    if (! $user->hasRole('Super Admin')) {
                                        $query->whereIn('id', $user->sectors->pluck('id'));
                                    }

                                    return $query->pluck('name', 'id')->toArray();
                                })
                                ->required(),
                            TextInput::make('tag')
                                ->label('Plaqueta')
                                ->required()
                                ->maxLength(6),
//                                ->unique(ignoreRecord: true),
                            TextInput::make('description')
                                ->label('Descrição')
                                ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                            Textarea::make('observation')
                                ->label('Observação')
                                ->rows(2)
                                ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                            FileUpload::make('image_path')->label('Imagem'),
                        ])
                    ]),
                Section::make('Aquisição')
                    ->schema([
                        Grid::make(['sm' => 1, 'md' => 2])->schema([
                            DatePicker::make('purchase_date')->label('Data da Compra'),
                            TextInput::make('purchase_value')->label('Valor de Compra')->numeric(),
                            TextInput::make('acquisition_type')->label('Tipo de Aquisição'),
                            TextInput::make('acquisition_value')->label('Valor de Aquisição')->numeric(),
                            DatePicker::make('acquisition_date')->label('Data da Aquisição'),
                            TextInput::make('current_value')->label('Valor Atual')->numeric(),
                        ])
                    ]),
                Section::make('Baixa')
                    ->schema([
                        Grid::make(['sm' => 1, 'md' => 2])->schema([
                            TextInput::make('write_off_reason')->label('Motivo da Baixa'),
                            DatePicker::make('write_off_date')->label('Data da Baixa'),
                            Toggle::make('has_report')->label('Laudo Emitido?'),
                            DatePicker::make('report_date')->label('Data do Laudo'),
                            TextInput::make('type')->label('Tipo do Bem'),
                        ]),
                        Toggle::make('is_active')
                            ->label('Ativa no sistema')
                            ->default(true)
                            ->visible(function (string $context): bool {
                                $user = Auth::user();
                                return $context === 'edit' && $user && $user->hasAnyRole(['Super Admin', 'Gerente']);
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tag')
            ->filtersTriggerAction(fn(Action $action) =>
                $action->icon('heroicon-s-adjustments-vertical')
                ->slideOver()
            )
            ->columns([
                TextColumn::make('tag')
                    ->label('Plaqueta')
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        $tag = "<span class='inline-block px-2 py-1 bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg'>" . e($state) . "</span>";

                        $description = $record->description
                            ? "<div class='text-sm text-gray-500 mt-1'>📝 " . e($record->description) . "</div>"
                            : '';

                        $sector = $record->sector?->name
                            ? "<div class='text-sm text-gray-500'>🏢 Setor: " . e($record->sector->name) . "</div>"
                            : '';

                        return "<div class='space-y-1'>" . $tag . $description . $sector . "</div>";
                    })
                    ->html()
                    ->wrap() // Permite múltiplas linhas
                    ->sortable()
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(30)
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sector.name')
                    ->label('Setor')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_active')
                    ->label('Ativo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Ativo' : 'Inativo')
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('purchase_value')
                    ->label('Valor')
                    ->money('BRL')
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('purchase_date')
                    ->label('Compra')
                    ->date('d/m/Y')
                    ->extraAttributes(responsiveColumnToggle(hideInMobile: true)['extraAttributes'])
                    ->extraHeaderAttributes(responsiveColumnToggle(hideInMobile: true)['extraHeaderAttributes'])
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tag', 'asc')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Ativo?')
                    ->trueLabel('Somente ativos')
                    ->falseLabel('Somente inativos')
                    ->default(true),

                Filter::make('filtro_departamento_setor')
                    ->label('Filtro por Departamento e Setor')
                    ->form([
                        MultiSelect::make('departament_ids')
                            ->label('Departamentos')
                            ->searchable()
                            ->live()
                            ->options(function () {
                                $user = auth()->user();
                                if ($user->hasRole('Super Admin')) {
                                    return Departament::orderBy('name')->pluck('name', 'id');
                                }
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
                        MultiSelect::make('sector_ids')
                            ->label('Setores')
                            ->searchable()
                            ->live()
                            ->options(function (callable $get) {
                                $departamentIds = $get('departament_ids');
                                $user = auth()->user();
                                if (empty($departamentIds)) {
                                    return [];
                                }
                                $query = Sector::query()->whereIn('departament_id', $departamentIds);
                                if (!$user->hasRole('Super Admin')) {
                                    $query->whereIn('id', $user->sectors->pluck('id'));
                                }
                                return $query->orderBy('name')->pluck('name', 'id');
                            })
                            ->disabled(fn (callable $get) => empty($get('departament_ids')))
                    ])
                    ->columnSpanFull(2)
                    ->query(function ($query, array $data) {
                        if (!empty($data['departament_ids'])) {
                            $query->whereHas('sector.departament', function ($q) use ($data) {
                                $q->whereIn('id', $data['departament_ids']);
                            });
                        }
                        if (!empty($data['sector_ids'])) {
                            $query->whereIn('sector_id', $data['sector_ids']);
                        }
                    }),
            ])
            ->filtersFormColumns(2)
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
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->hasRole('Super Admin')) {
            return $query;
        }

        return $query->whereIn('sector_id', auth()->user()->sectors->pluck('id'));
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
            'index' => Pages\ListPatrimonies::route('/'),
            'create' => Pages\CreatePatrimony::route('/create'),
            'edit' => Pages\EditPatrimony::route('/{record}/edit'),
            'view' => Pages\ViewPatrimony::route('/{record}'),
        ];
    }
}
