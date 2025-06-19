<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class MigrationScreen extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public $uploadedFiles = [];
    public $detectedTables = []; // ['nome_tabela' => 'caminho_tmp']
    public $selectedTables = [];
    public array $tablesFields = []; // [tabela => [fields]]
    public int $tableStep = 0;
    public array $fieldsMapping = [];
    public array $fieldsTypeDiffs = [];
    public bool $acceptAllTypeDiffs = false;

    public $isMigrating = false;
    public $migrationResult = null;

    // PROPRIEDADE ADICIONADA PARA CORRIGIR O ERRO
    public $duplicateAnalysis = null;

    public function resetUpload()
    {
        $this->uploadedFiles = [];
        $this->detectedTables = [];
        $this->selectedTables = [];
        $this->step = 1;
        $this->tablesFields = [];
        $this->tableStep = 0;
        $this->fieldsMapping = [];
        $this->fieldsTypeDiffs = [];
        $this->isMigrating = false;
        $this->migrationResult = null;
        $this->duplicateAnalysis = null; // Reset da análise também
    }

    public function updatedUploadedFiles()
    {
        $this->detectedTables = [];
        foreach ($this->uploadedFiles as $file) {
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $table = str_replace([' ', '-'], '_', strtolower($filename));
            $this->detectedTables[$table] = $file->getRealPath();
        }
        $this->selectedTables = array_keys($this->detectedTables);
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            if (count($this->uploadedFiles) === 0) return;
            $this->step = 2;
        } elseif ($this->step === 2) {
            if (count($this->selectedTables) === 0) return;
            $this->prepareTablesFields();
            $this->tableStep = 0;
            $this->step = 3;
        } elseif ($this->step === 3 && $this->tableStep < count($this->selectedTables) - 1) {
            $this->tableStep++;
        } elseif ($this->step === 3) {
            $this->step = 4;
        }
    }

    public function prevStep()
    {
        if ($this->step === 2) {
            $this->step = 1;
        } elseif ($this->step === 3 && $this->tableStep > 0) {
            $this->tableStep--;
        } elseif ($this->step === 3 && $this->tableStep == 0) {
            $this->step = 2;
        } elseif ($this->step === 4) {
            $this->step = 3;
        }
    }

    private function prepareTablesFields()
    {
        $this->tablesFields = [];
        $this->fieldsMapping = [];
        $this->fieldsTypeDiffs = [];
        foreach ($this->selectedTables as $table) {
            $filePath = $this->detectedTables[$table];
            $headings = [];
            $data = [];

            if (file_exists($filePath)) {
                $data = Excel::toArray([], $filePath, null, \Maatwebsite\Excel\Excel::XLSX)[0] ?? [];
                if (empty($data)) {
                    $data = Excel::toArray([], $filePath, null, \Maatwebsite\Excel\Excel::XLS)[0] ?? [];
                }
                $headings = $data[0] ?? [];
            }

            // Aqui você captura os campos destino
            $destino = $this->getTableColumns($table);

            // ... resto do código
            $this->tablesFields[$table] = [
                'origem' => array_combine($headings, $headings),
                'destino' => $destino,
            ];
            foreach ($headings as $field) {
                if (isset($destino[$field])) {
                    $this->fieldsMapping[$table][$field] = $field;
                } else {
                    $suggest = $this->suggestField($field, $destino);
                    $this->fieldsMapping[$table][$field] = $suggest ?: '';
                }
            }
        }
    }

    private function getTableColumns($table)
    {
        $columns = [];
        try {
            $cols = \DB::connection('destino')->select("SHOW COLUMNS FROM `$table`");
            foreach ($cols as $col) {
                $columns[$col->Field] = $col->Type;
            }
        } catch (\Throwable $e) {}
        return $columns;
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

    public function canProceedTable($table)
    {
        return true;
    }

    public function updatedFieldsMapping($value, $key)
    {
        // Implementar comparação de tipos/campos se desejar
    }

    /**
     * Método para executar análise e atualizar a propriedade
     */
    public function runDuplicateAnalysis()
    {
        $this->duplicateAnalysis = $this->analyzeDuplicates();
    }

    /**
     * Analisa duplicatas em todas as tabelas selecionadas
     */
    public function analyzeDuplicates()
    {
        $analysis = [];

        foreach ($this->selectedTables as $table) {
            $filePath = $this->detectedTables[$table] ?? null;
            if (!$filePath || !file_exists($filePath)) {
                continue;
            }

            // Ler dados do Excel
            $data = Excel::toArray([], $filePath, null, \Maatwebsite\Excel\Excel::XLSX)[0] ?? [];
            if (empty($data)) {
                $data = Excel::toArray([], $filePath, null, \Maatwebsite\Excel\Excel::XLS)[0] ?? [];
            }

            $headings = $data[0] ?? [];
            $rows = array_slice($data, 1);
            $mapping = $this->fieldsMapping[$table] ?? [];

            // Detectar duplicatas
            $duplicates = $this->detectDuplicates($rows, $headings, $mapping, $table);

            $analysis[$table] = [
                'total_rows' => count($rows),
                'duplicates' => $duplicates,
                'duplicate_count' => count($duplicates),
                'affected_rows' => array_sum(array_column($duplicates, 'count')) - count($duplicates)
            ];
        }

        return $analysis;
    }

    public function executeMigration()
    {
        $this->isMigrating = true;
        $log = [];
        $sqlLog = [];
        $success = true;

        $filename = 'migracao-' . now()->format('Ymd-His') . '.txt';
        $logfile = base_path($filename);

        try {
            // Obter a conexão destino
            $connection = \DB::connection('destino');

            // Desabilitar autocommit para controle manual da transação
            $connection->getPdo()->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);

            // Iniciar transação
            $connection->beginTransaction();

            // Desabilitar verificação de chaves estrangeiras
            $connection->statement('SET FOREIGN_KEY_CHECKS=0');
            $sqlLog[] = "SET FOREIGN_KEY_CHECKS=0;";

            foreach ($this->selectedTables as $table) {
                $filePath = $this->detectedTables[$table] ?? null;
                if (!$filePath || !file_exists($filePath) || !is_readable($filePath)) {
                    throw new \Exception("Arquivo $filePath não existe ou não pode ser lido!");
                }

                // Capturar DDL da tabela para o log
                try {
                    $ddlRaw = $connection->select("SHOW CREATE TABLE `$table`");
                    $ddl = $ddlRaw[0]->{'Create Table'} ?? '';
                    $sqlLog[] = "DROP TABLE IF EXISTS `$table`;";
                    if ($ddl) {
                        $sqlLog[] = $ddl . ";";
                    }
                } catch (\Exception $e) {
                    $log[] = "Aviso: Não foi possível capturar DDL da tabela $table: " . $e->getMessage();
                }

                // Truncar tabela
                $connection->statement("TRUNCATE TABLE `$table`");
                $sqlLog[] = "TRUNCATE TABLE `$table`;";

                $log[] = "Tabela: {$table}";

                // Ler dados do Excel
                $data = Excel::toArray([], $filePath, null, \Maatwebsite\Excel\Excel::XLSX)[0] ?? [];
                if (empty($data)) {
                    $data = Excel::toArray([], $filePath, null, \Maatwebsite\Excel\Excel::XLS)[0] ?? [];
                }

                $headings = $data[0] ?? [];
                $rows = array_slice($data, 1);
                $destFields = $this->tablesFields[$table]['destino'] ?? [];
                $mapping = $this->fieldsMapping[$table] ?? [];

                // DETECÇÃO DE DUPLICATAS E ADIÇÃO DE SUFIXOS
                $duplicateLog = [];
                $rows = $this->addSuffixesToDuplicates($rows, $headings, $mapping, $table, $duplicateLog);

                // Log das modificações
                if (!empty($duplicateLog)) {
                    $log[] = "=== DUPLICATAS MODIFICADAS ===";
                    foreach ($duplicateLog as $entry) {
                        $log[] = $entry;
                    }
                    $log[] = "=== FIM DUPLICATAS ===";
                    $log[] = "";
                }

                $totalImported = 0;
                $batchSize = 100;
                $batch = [];

                foreach ($rows as $rowIndex => $row) {
                    $insert = [];

                    // MAPEAMENTO ORIGEM => DESTINO
                    foreach ($destFields as $dest => $type) {
                        $orig = null;
                        foreach ($mapping as $origem => $destino) {
                            if ($destino === $dest) {
                                $orig = $origem;
                                break;
                            }
                        }

                        if ($orig !== null && in_array($orig, $headings)) {
                            $colIdx = array_search($orig, $headings);
                            $valor = $row[$colIdx] ?? null;

                            // Tratar valores vazios
                            if ($valor !== null && $valor !== '') {
                                // Conversão de datas do Excel
                                if (stripos($type, 'date') !== false || stripos($type, 'timestamp') !== false) {
                                    if (is_numeric($valor)) {
                                        try {
                                            $phpDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($valor);
                                            $valor = stripos($type, 'date') !== false && stripos($type, 'time') === false
                                                ? $phpDate->format('Y-m-d')
                                                : $phpDate->format('Y-m-d H:i:s');
                                        } catch (\Exception $e) {
                                            $valor = null;
                                        }
                                    }
                                }
                            } else {
                                $valor = null;
                            }

                            $insert[$dest] = $valor;
                        } else {
                            // Campo não mapeado - definir valor padrão baseado no tipo
                            if (stripos($type, 'int') !== false ||
                                stripos($type, 'decimal') !== false ||
                                stripos($type, 'float') !== false) {
                                $insert[$dest] = 0;
                            } else {
                                $insert[$dest] = null;
                            }
                        }
                    }

                    if (!empty($insert)) {
                        $batch[] = $insert;

                        // Log SQL para auditoria
                        $campos = array_map(function($c) { return "`$c`"; }, array_keys($insert));
                        $valores = array_map(function($v) {
                            if (is_null($v)) return 'NULL';
                            if (is_numeric($v)) return $v;
                            return "'" . addslashes($v) . "'";
                        }, array_values($insert));
                        $sqlLog[] = "INSERT INTO `$table` (" . implode(',', $campos) . ") VALUES (" . implode(',', $valores) . ");";
                    }

                    // Inserir em lotes
                    if (count($batch) >= $batchSize || $rowIndex === count($rows) - 1) {
                        if (!empty($batch)) {
                            $connection->table($table)->insert($batch);
                            $totalImported += count($batch);
                            $batch = [];
                        }
                    }
                }

                $log[] = "- Registros migrados: {$totalImported}";
                $log[] = "";
            }

            // Reabilitar verificação de chaves estrangeiras
            $connection->statement('SET FOREIGN_KEY_CHECKS=1');
            $sqlLog[] = "SET FOREIGN_KEY_CHECKS=1;";

            // Confirmar transação
            $connection->commit();

            // Reabilitar autocommit
            $connection->getPdo()->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);

            $success = true;
            $message = "Migração concluída com sucesso!\nLog salvo em: $filename";

        } catch (\Throwable $e) {
            $success = false;
            $log[] = "ERRO: " . $e->getMessage();
            $log[] = "Linha do erro: " . $e->getLine();
            $log[] = "Arquivo do erro: " . $e->getFile();
            $message = "Erro ao migrar: " . $e->getMessage();

            // Rollback seguro
            try {
                if (\DB::connection('destino')->transactionLevel() > 0) {
                    \DB::connection('destino')->rollBack();
                    $log[] = "Rollback executado com sucesso.";
                }
                // Reabilitar autocommit mesmo em caso de erro
                \DB::connection('destino')->getPdo()->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);
            } catch (\Throwable $rollbackError) {
                $log[] = "Erro ao executar rollback: " . $rollbackError->getMessage();
            }
        }

        // Salvar log (sempre fora do try/catch)
        try {
            file_put_contents($logfile, implode(PHP_EOL, array_merge($log, $sqlLog)));
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar log de migração: ' . $e->getMessage());
        }

        $this->isMigrating = false;
        $this->migrationResult = [
            'success' => $success,
            'message' => $message,
            'logfile' => $success ? $filename : null
        ];

        // Limpeza dos arquivos temporários
        foreach ($this->detectedTables as $path) {
            if (file_exists($path)) {
                try {
                    unlink($path);
                } catch (\Exception $e) {
                    \Log::warning('Não foi possível remover arquivo temporário: ' . $path);
                }
            }
        }
    }

    /**
     * Adiciona sufixos às duplicatas ao invés de removê-las
     */
    private function addSuffixesToDuplicates($rows, $headings, $mapping, $table, &$duplicateLog)
    {
        $protocolField = null;

        // Encontrar qual campo origem está mapeado para 'protocol' no destino
        foreach ($mapping as $origem => $destino) {
            if (strtolower($destino) === 'protocol') {
                $protocolField = $origem;
                break;
            }
        }

        if (!$protocolField || !in_array($protocolField, $headings)) {
            return $rows; // Não há campo protocol mapeado, retorna sem modificar
        }

        $protocolIndex = array_search($protocolField, $headings);
        $protocolCounts = [];
        $modifiedRows = [];

        // Primeiro pass: contar ocorrências
        foreach ($rows as $row) {
            $protocol = $row[$protocolIndex] ?? null;
            if ($protocol !== null && $protocol !== '') {
                if (!isset($protocolCounts[$protocol])) {
                    $protocolCounts[$protocol] = 0;
                }
                $protocolCounts[$protocol]++;
            }
        }

        // Segundo pass: aplicar sufixos
        $protocolCurrentCount = [];
        foreach ($rows as $rowIndex => $row) {
            $protocol = $row[$protocolIndex] ?? null;

            if ($protocol !== null && $protocol !== '') {
                if (!isset($protocolCurrentCount[$protocol])) {
                    $protocolCurrentCount[$protocol] = 0;
                }

                $protocolCurrentCount[$protocol]++;

                // Se há mais de uma ocorrência deste protocol
                if ($protocolCounts[$protocol] > 1) {
                    $newProtocol = $protocol . '-' . $protocolCurrentCount[$protocol];
                    $duplicateLog[] = "Linha " . ($rowIndex + 2) . ": '{$protocol}' → '{$newProtocol}'";

                    // Modificar o valor na linha
                    $row[$protocolIndex] = $newProtocol;
                }
            }

            $modifiedRows[] = $row;
        }

        // Resumo final
        $duplicatesFound = array_filter($protocolCounts, function($count) { return $count > 1; });
        if (!empty($duplicatesFound)) {
            $duplicateLog[] = "";
            $duplicateLog[] = "RESUMO DE DUPLICATAS:";
            foreach ($duplicatesFound as $protocol => $count) {
                $duplicateLog[] = "- Protocol '{$protocol}': {$count} ocorrências → '{$protocol}-1' até '{$protocol}-{$count}'";
            }
            $duplicateLog[] = "Total de protocols duplicados: " . count($duplicatesFound);
            $duplicateLog[] = "Total de modificações aplicadas: " . (array_sum($duplicatesFound) - count($duplicatesFound));
        }

        return $modifiedRows;
    }

    /**
     * Detecta duplicatas (atualizado para mostrar como ficarão após sufixos)
     */
    private function detectDuplicates($rows, $headings, $mapping, $table)
    {
        $duplicates = [];
        $protocolField = null;

        // Encontrar qual campo origem está mapeado para 'protocol' no destino
        foreach ($mapping as $origem => $destino) {
            if (strtolower($destino) === 'protocol') {
                $protocolField = $origem;
                break;
            }
        }

        if (!$protocolField || !in_array($protocolField, $headings)) {
            return []; // Não há campo protocol mapeado
        }

        $protocolIndex = array_search($protocolField, $headings);
        $protocolCounts = [];

        // Contar ocorrências de cada protocol
        foreach ($rows as $rowIndex => $row) {
            $protocol = $row[$protocolIndex] ?? null;
            if ($protocol !== null && $protocol !== '') {
                if (!isset($protocolCounts[$protocol])) {
                    $protocolCounts[$protocol] = [
                        'count' => 0,
                        'rows' => [],
                        'will_become' => []
                    ];
                }
                $protocolCounts[$protocol]['count']++;
                $protocolCounts[$protocol]['rows'][] = $rowIndex + 2; // +2 porque: índice 0-based + linha de cabeçalho
            }
        }

        // Filtrar apenas os duplicados e calcular como ficarão
        foreach ($protocolCounts as $protocol => $info) {
            if ($info['count'] > 1) {
                for ($i = 1; $i <= $info['count']; $i++) {
                    $info['will_become'][] = $protocol . '-' . $i;
                }
                $duplicates[$protocol] = $info;
            }
        }

        return $duplicates;
    }

    public function render()
    {
        return view('livewire.migration-screen');
    }
}
