<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class MigrationScreen extends Component
{
    public int $step = 1;
    public array $tables = [];
    public array $selectedTables = [];
    public bool $reset = false;
    public array $tablesFields = [];
    public int $tableStep = 0; // Inicializada com 0
    public array $fieldsMapping = [];
    public array $fieldsTypeDiffs = [];
    public bool $acceptAllTypeDiffs = false;

    public $isMigrating = false;
    public $migrationResult = null;

    // Tabelas permitidas com nomes em inglês e seus mapeamentos
    public array $allowedTables = [
        'users' => 'users',
        'setor_user' => 'setor_user',
        'sections' => 'sections',
        'section_user' => 'section_user',
        'role' => 'role',
        'role_has_permissions' => 'role_has_permissions',
        'permissions' => 'permissions',
        'patrimonies' => 'patrimonies',
        'model_has_roles' => 'model_has_roles',
        'minutes' => 'price_agreements', // Tabela com nome diferente
        'interactions' => 'interactions',
        'enforcers' => 'suppliers', // Tabela com nome diferente
        'departaments' => 'departaments',
        'departamento_user' => 'departamento_user',
        'companies' => 'companies',
        'calleds' => 'calleds',
        'products' => 'agreement_items' // Tabela com nome diferente
    ];

    public function mount()
    {
        try {
            $allTables = DB::connection('origem')->select('SHOW TABLES');
            $this->tables = [];

            foreach ($allTables as $row) {
                $table = array_values((array)$row)[0];
                if (array_key_exists($table, $this->allowedTables)) {
                    $this->tables[$table] = $table; // Nome em inglês
                }
            }

            $this->selectedTables = array_keys($this->tables);
            $this->tableStep = 0; // Garantir que sempre inicia em 0

        } catch (\Exception $e) {
            $this->migrationResult = [
                'success' => false,
                'message' => 'Erro de conexão: ' . $e->getMessage(),
                'logfile' => null
            ];
        }
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->prepareTablesFields();
            $this->tableStep = 0;
            $this->step = 2;
        } elseif ($this->step === 2 && $this->tableStep < count($this->selectedTables) - 1) {
            $this->tableStep++;
        } elseif ($this->step === 2) {
            $this->step = 3;
        }
    }

    public function prevStep()
    {
        if ($this->step === 2 && $this->tableStep > 0) {
            $this->tableStep--;
        } elseif ($this->step === 2 && $this->tableStep === 0) {
            $this->step = 1;
        } elseif ($this->step === 3) {
            $this->step = 2;
            $this->tableStep = count($this->selectedTables) - 1; // Voltar para a última tabela
        }
    }

    private function prepareTablesFields()
    {
        $this->tablesFields = [];
        $this->fieldsMapping = [];
        $this->fieldsTypeDiffs = [];

        foreach ($this->selectedTables as $table) {
            $origFields = $this->getTableColumns('origem', $table);
            $destTable = $this->destTableName($table);
            $destFields = $this->getTableColumns('destino', $destTable);

            foreach ($origFields as $field => $type) {
                if (isset($destFields[$field])) {
                    $this->fieldsMapping[$table][$field] = $field;
                    if ($type !== $destFields[$field]) {
                        $this->fieldsTypeDiffs[$table][$field] = [
                            'destino' => $field,
                            'type_origem' => $type,
                            'type_destino' => $destFields[$field],
                            'accept' => false
                        ];
                    }
                } else {
                    $suggest = $this->suggestField($field, $destFields);
                    $this->fieldsMapping[$table][$field] = $suggest ?: '';
                    if ($suggest && $type !== ($destFields[$suggest] ?? null)) {
                        $this->fieldsTypeDiffs[$table][$field] = [
                            'destino' => $suggest,
                            'type_origem' => $type,
                            'type_destino' => $destFields[$suggest] ?? '',
                            'accept' => false
                        ];
                    }
                }
            }

            $this->tablesFields[$table] = [
                'origem' => $origFields,
                'destino' => $destFields,
            ];
        }
    }

    private function getTableColumns($conn, $table)
    {
        $columns = [];
        try {
            $cols = DB::connection($conn)->select("SHOW COLUMNS FROM `$table`");
            foreach ($cols as $col) {
                $columns[$col->Field] = $col->Type;
            }
        } catch (\Throwable $e) {
            // Log do erro mas continua
        }
        return $columns;
    }

    private function destTableName($table)
    {
        // Mapeamento das tabelas com nomes diferentes
        return $this->allowedTables[$table] ?? $table;
    }

    private function suggestField($field, $destFields)
    {
        foreach ($destFields as $dest => $type) {
            if (
                strpos($dest, $field) !== false ||
                strpos($field, $dest) !== false ||
                levenshtein($dest, $field) < 4
            ) {
                return $dest;
            }
        }
        return null;
    }

    public function resetMapping($table)
    {
        $origFields = $this->tablesFields[$table]['origem'] ?? [];
        $destFields = $this->tablesFields[$table]['destino'] ?? [];
        foreach ($origFields as $field => $type) {
            $suggest = $this->suggestField($field, $destFields);
            $this->fieldsMapping[$table][$field] = $suggest ?: '';
            if ($suggest && $type !== ($destFields[$suggest] ?? null)) {
                $this->fieldsTypeDiffs[$table][$field] = [
                    'destino' => $suggest,
                    'type_origem' => $type,
                    'type_destino' => $destFields[$suggest] ?? '',
                    'accept' => false
                ];
            } else {
                unset($this->fieldsTypeDiffs[$table][$field]);
            }
        }
    }

    public function acceptAllTypesDiff($table)
    {
        if (!isset($this->fieldsTypeDiffs[$table])) return;
        foreach ($this->fieldsTypeDiffs[$table] as $field => &$diff) {
            $diff['accept'] = true;
        }
        $this->fieldsTypeDiffs[$table] = $this->fieldsTypeDiffs[$table];
    }

    public function canProceedTable($table)
    {
        if (!isset($this->fieldsMapping[$table])) return false;
        $allMapped = !in_array('', $this->fieldsMapping[$table], true);
        $destValues = array_values($this->fieldsMapping[$table]);
        $hasDuplicates = count($destValues) !== count(array_unique($destValues));
        $typesOk = true;
        if (isset($this->fieldsTypeDiffs[$table])) {
            foreach ($this->fieldsTypeDiffs[$table] as $diff) {
                if (isset($diff['destino']) && isset($diff['type_origem']) && isset($diff['type_destino']) && $diff['type_origem'] !== $diff['type_destino']) {
                    if (!($diff['accept'] ?? false)) $typesOk = false;
                }
            }
        }
        return $allMapped && !$hasDuplicates && $typesOk;
    }

    public function updatedFieldsMapping($value, $key)
    {
        [$table, $field] = explode('.', $key, 2);
        $destField = $value;
        $typeOrig = $this->tablesFields[$table]['origem'][$field] ?? null;
        $typeDest = $this->tablesFields[$table]['destino'][$destField] ?? null;

        if ($typeOrig && $typeDest) {
            if ($typeOrig !== $typeDest) {
                $this->fieldsTypeDiffs[$table][$field]['accept'] = false;
                $this->fieldsTypeDiffs[$table][$field]['destino'] = $destField;
                $this->fieldsTypeDiffs[$table][$field]['type_origem'] = $typeOrig;
                $this->fieldsTypeDiffs[$table][$field]['type_destino'] = $typeDest;
            } else {
                unset($this->fieldsTypeDiffs[$table][$field]);
            }
        }
    }

    public function executeMigration()
    {
        $this->isMigrating = true;
        $log = [];
        $success = true;

        $filename = 'migracao-' . now()->format('Ymd-His') . '.txt';
        $logfile = storage_path('logs/' . $filename);

        // Garantir que o diretório logs existe
        if (!is_dir(storage_path('logs'))) {
            mkdir(storage_path('logs'), 0755, true);
        }

        try {
            DB::connection('destino')->beginTransaction();

            $log[] = "=== INICIANDO MIGRAÇÃO ===";
            $log[] = "Data/Hora: " . now()->format('d/m/Y H:i:s');
            $log[] = "Reset do banco destino: " . ($this->reset ? 'SIM' : 'NÃO');
            $log[] = "Base Origem: " . config('database.connections.origem.database');
            $log[] = "Base Destino: " . config('database.connections.destino.database');
            $log[] = "";

            foreach ($this->selectedTables as $table) {
                $log[] = "Tabela: {$table}";

                $origTable = $table;
                $destTable = $this->destTableName($table);

                if ($this->reset) {
                    try {
                        DB::connection('destino')->statement("SET FOREIGN_KEY_CHECKS=0");
                        DB::connection('destino')->statement("TRUNCATE TABLE `$destTable`");
                        DB::connection('destino')->statement("ALTER TABLE `$destTable` AUTO_INCREMENT = 1");
                        DB::connection('destino')->statement("SET FOREIGN_KEY_CHECKS=1");
                        $log[] = "- Resetada (TRUNCATE)";
                    } catch (\Exception $e) {
                        $log[] = "- ERRO ao resetar: " . $e->getMessage();
                    }
                }

                $origFields = $this->tablesFields[$origTable]['origem'] ?? [];
                $destFields = $this->tablesFields[$origTable]['destino'] ?? [];
                $mapping = $this->fieldsMapping[$origTable] ?? [];

                $dataOrig = DB::connection('origem')->table($origTable)->get();
                $totalImported = 0;

                foreach ($dataOrig as $row) {
                    $insert = [];

                    foreach ($mapping as $orig => $dest) {
                        if (!$dest) continue;
                        $valor = $row->$orig ?? null;
                        $typeOrig = $origFields[$orig] ?? '';
                        $typeDest = $destFields[$dest] ?? '';
                        $accepted = $this->fieldsTypeDiffs[$origTable][$orig]['accept'] ?? false;

                        if ($typeOrig !== $typeDest && !$accepted) {
                            continue;
                        }
                        $insert[$dest] = $valor;
                    }

                    // Preencher campos de destino que não estão mapeados
                    foreach ($destFields as $dest => $type) {
                        if (!in_array($dest, $mapping)) {
                            if (stripos($type, 'int') !== false || stripos($type, 'decimal') !== false || stripos($type, 'float') !== false) {
                                $insert[$dest] = 0;
                            } else {
                                $insert[$dest] = null;
                            }
                        }
                    }

                    if (!empty($insert)) {
                        DB::connection('destino')->table($destTable)->insert($insert);
                        $totalImported++;
                    }
                }

                $log[] = "- Registros migrados: {$totalImported}";
                $log[] = "- Mapeamento:";
                foreach ($mapping as $orig => $dest) {
                    $typeOrig = $origFields[$orig] ?? '';
                    $typeDest = $destFields[$dest] ?? '';
                    $accept = $this->fieldsTypeDiffs[$origTable][$orig]['accept'] ?? false;
                    $log[] = "   {$orig} [{$typeOrig}] => {$dest} [{$typeDest}] ".($typeOrig !== $typeDest ? ($accept ? '[ACEITO]' : '[NÃO ACEITO]') : '');
                }
                $log[] = "";
            }

            DB::connection('destino')->commit();
            $log[] = "=== MIGRAÇÃO CONCLUÍDA COM SUCESSO ===";
            $message = "Migração concluída com sucesso!\nLog salvo em: storage/logs/{$filename}";

        } catch (\Throwable $e) {
            $success = false;
            DB::connection('destino')->rollBack();
            $log[] = "ERRO: " . $e->getMessage();
            $log[] = "ROLLBACK executado - nenhuma alteração foi salva!";
            $message = "Erro ao migrar: " . $e->getMessage() . "\nTodas as alterações foram desfeitas (rollback)!";
        }

        // Salvar log
        file_put_contents($logfile, implode("\n", $log));

        $this->isMigrating = false;
        $this->migrationResult = [
            'success' => $success,
            'message' => $message,
            'logfile' => $success ? $filename : null
        ];
    }
    public function debugStep2()
    {
        $currentTable = $this->selectedTables[$this->tableStep ?? 0] ?? null;

        if (!$currentTable) {
            dd('Tabela atual não encontrada', $this->selectedTables, $this->tableStep);
        }

        $mapping = $this->fieldsMapping[$currentTable] ?? [];
        $typeDiffs = $this->fieldsTypeDiffs[$currentTable] ?? [];

        dd([
            'currentTable' => $currentTable,
            'mapping' => $mapping,
            'typeDiffs' => $typeDiffs,
            'canProceed' => $this->canProceedTable($currentTable),
            'allMapped' => !in_array('', $mapping, true),
            'destValues' => array_values($mapping),
            'hasDuplicates' => count(array_values($mapping)) !== count(array_unique(array_values($mapping))),
        ]);
    }
    public function autoFixMapping($table)
    {
        $origFields = $this->tablesFields[$table]['origem'] ?? [];
        $destFields = $this->tablesFields[$table]['destino'] ?? [];

        // Resetar mapeamento
        $this->fieldsMapping[$table] = [];
        $this->fieldsTypeDiffs[$table] = [];

        $usedDestFields = [];

        foreach ($origFields as $field => $type) {
            // Primeiro, tentar mapeamento exato
            if (isset($destFields[$field]) && !in_array($field, $usedDestFields)) {
                $this->fieldsMapping[$table][$field] = $field;
                $usedDestFields[] = $field;

                // Verificar diferença de tipo
                if ($type !== $destFields[$field]) {
                    $this->fieldsTypeDiffs[$table][$field] = [
                        'destino' => $field,
                        'type_origem' => $type,
                        'type_destino' => $destFields[$field],
                        'accept' => true // AUTO-ACEITAR
                    ];
                }
            } else {
                // Tentar sugestão que não está em uso
                $suggest = null;
                foreach ($destFields as $dest => $destType) {
                    if (!in_array($dest, $usedDestFields)) {
                        if (
                            strpos($dest, $field) !== false ||
                            strpos($field, $dest) !== false ||
                            levenshtein($dest, $field) < 4
                        ) {
                            $suggest = $dest;
                            break;
                        }
                    }
                }

                if ($suggest) {
                    $this->fieldsMapping[$table][$field] = $suggest;
                    $usedDestFields[] = $suggest;

                    // Verificar diferença de tipo
                    if ($type !== $destFields[$suggest]) {
                        $this->fieldsTypeDiffs[$table][$field] = [
                            'destino' => $suggest,
                            'type_origem' => $type,
                            'type_destino' => $destFields[$suggest],
                            'accept' => true // AUTO-ACEITAR
                        ];
                    }
                } else {
                    // Se não encontrar, mapear para um campo livre qualquer
                    foreach ($destFields as $dest => $destType) {
                        if (!in_array($dest, $usedDestFields)) {
                            $this->fieldsMapping[$table][$field] = $dest;
                            $usedDestFields[] = $dest;

                            $this->fieldsTypeDiffs[$table][$field] = [
                                'destino' => $dest,
                                'type_origem' => $type,
                                'type_destino' => $destType,
                                'accept' => true // AUTO-ACEITAR
                            ];
                            break;
                        }
                    }
                }
            }
        }

        // Se ainda tiver campos sem mapeamento, remover
        foreach ($this->fieldsMapping[$table] as $orig => $dest) {
            if (empty($dest)) {
                unset($this->fieldsMapping[$table][$orig]);
            }
        }
    }

    public function render()
    {
        return view('livewire.migration-screen');
    }
}
