<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    @if($step === 1)
        <h2 class="text-2xl font-bold mb-4">Passo 1: Faça upload dos arquivos Excel</h2>
        <form wire:submit.prevent="nextStep">
            <input type="file" wire:model="uploadedFiles" multiple accept=".xlsx,.xls" class="mb-4 block">
            @error('uploadedFiles.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            @if(count($uploadedFiles) > 0)
                <div class="mb-4">
                    <div class="font-bold">Arquivos detectados:</div>
                    <ul class="list-disc pl-6 mt-2 text-sm">
                        @foreach($uploadedFiles as $file)
                            <li>{{ $file->getClientOriginalName() }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Próximo</button>
                <button type="button" wire:click="resetUpload" class="ml-2 px-3 py-2 bg-gray-200 rounded">Limpar</button>
            @endif
        </form>
    @endif

    @if($step === 2)
        <h2 class="text-2xl font-bold mb-4">Passo 2: Selecione as tabelas a importar</h2>
        <form wire:submit.prevent="nextStep">
            <div class="mb-4">
                @foreach($detectedTables as $table => $path)
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="selectedTables" value="{{ $table }}" class="mr-2">
                        {{ $table }}
                    </label>
                @endforeach
            </div>
            <div class="flex gap-3">
                <button wire:click.prevent="prevStep" type="button" class="px-4 py-2 rounded bg-gray-300">Voltar</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded" @if(count($selectedTables) == 0) disabled @endif>Próximo</button>
            </div>
        </form>
    @endif

    @if($step === 3)
        @php $currentTable = $selectedTables[$tableStep] ?? null; @endphp
        <h2 class="text-2xl font-bold mb-4">
            Passo 3: Mapeamento dos Campos - <span class="text-blue-700">{{ $currentTable }}</span>
            ({{ $tableStep + 1 }} de {{ count($selectedTables) }})
        </h2>
        @if($currentTable)
            <form wire:submit.prevent="nextStep">
                <div class="mb-4">
                    <table class="min-w-full border text-xs">
                        <thead>
                        <tr>
                            <th class="border p-2">Campo Origem</th>
                            <th class="border p-2">Campo Destino</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tablesFields[$currentTable]['origem'] ?? [] as $field => $typeOrig)
                            @php
                                $destFields = $tablesFields[$currentTable]['destino'] ?? [];
                                $selectedThis = $fieldsMapping[$currentTable][$field] ?? '';
                                $alreadyMapped = collect($fieldsMapping[$currentTable])->except($field)->values()->all();
                            @endphp
                            <tr>
                                <td class="border p-2">{{ $field }}</td>
                                <td class="border p-2">
                                    <select wire:model="fieldsMapping.{{ $currentTable }}.{{ $field }}" class="border rounded px-2 py-1">
                                        <option value="">Selecione</option>
                                        @foreach($destFields as $destKey => $destType)
                                            @if($selectedThis == $destKey || !in_array($destKey, $alreadyMapped))
                                                <option value="{{ $destKey }}">{{ $destKey }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex gap-3 mt-6">
                    @if($tableStep > 0)
                        <button wire:click.prevent="prevStep" type="button" class="px-4 py-2 rounded bg-gray-300">Anterior</button>
                    @endif
                    <button type="submit"
                            class="px-4 py-2 rounded bg-blue-600 text-white"
                    >
                        {{ $tableStep === count($selectedTables)-1 ? 'Confirmar Mapeamento' : 'Próximo' }}
                    </button>
                </div>
            </form>
        @endif
    @endif

    @if($step === 4)
        <h2 class="text-2xl font-bold mb-4">Resumo e Execução da Migração</h2>

        {{-- Análise de Duplicatas --}}
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-lg font-semibold text-yellow-800">Análise de Duplicatas</h3>
                <button wire:click="runDuplicateAnalysis" type="button"
                        class="px-3 py-1 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700">
                    Analisar
                </button>
            </div>

            @if($duplicateAnalysis)
                @php $totalDuplicates = array_sum(array_column($duplicateAnalysis, 'duplicate_count')); @endphp
                @if($totalDuplicates > 0)
                    <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-3">
                        <h4 class="font-semibold text-blue-800 mb-2">📝 Duplicatas Encontradas (serão renomeadas)</h4>
                        @foreach($duplicateAnalysis as $table => $analysis)
                            @if($analysis['duplicate_count'] > 0)
                                <div class="mb-2">
                                    <strong>{{ $table }}:</strong>
                                    <ul class="text-sm text-blue-700 ml-4 list-disc">
                                        <li>{{ $analysis['duplicate_count'] }} protocols duplicados</li>
                                        <li>{{ $analysis['affected_rows'] }} registros receberão sufixos</li>
                                        <li>{{ $analysis['total_rows'] }} registros serão migrados (nenhum será perdido)</li>
                                    </ul>
                                    <details class="mt-2">
                                        <summary class="cursor-pointer text-sm text-blue-600 hover:text-blue-800">Ver como ficarão após sufixos</summary>
                                        <div class="mt-2 bg-white p-2 rounded border text-xs">
                                            @foreach($analysis['duplicates'] as $protocol => $info)
                                                <div class="mb-1">
                                                    <strong>{{ $protocol }}:</strong>
                                                    {{ $info['count'] }} ocorrências →
                                                    {{ implode(', ', $info['will_become']) }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                </div>
                            @endif
                        @endforeach
                        <p class="text-sm text-blue-600 mt-2">
                            <strong>Ação:</strong> Sufixos serão adicionados às duplicatas (Ex: CHAM2080-1, CHAM2080-2, etc.).
                        </p>
                    </div>
                @else
                    <div class="bg-green-50 border border-green-200 rounded p-3">
                        <p class="text-green-800">✅ Nenhuma duplicata encontrada!</p>
                    </div>
                @endif
            @else
                <p class="text-gray-600 text-sm">Clique em "Analisar" para verificar duplicatas antes da migração.</p>
            @endif
        </div>

        {{-- Resumo das Tabelas --}}
        <div class="mb-4">
            <div class="font-bold">Tabelas a serem migradas:</div>
            <ul class="list-disc pl-6 mt-2 text-sm">
                @foreach($selectedTables as $table)
                    <li>{{ $table }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Botões de Ação --}}
        <div class="flex gap-3 mt-6">
            <button wire:click="prevStep" type="button" class="px-4 py-2 rounded bg-gray-300">Voltar</button>
            <button wire:click="executeMigration" type="button" class="px-4 py-2 rounded bg-green-700 text-white"
                    @if($isMigrating) disabled @endif>
                @if($isMigrating)
                    Migrando...
                @else
                    Executar Migração
                @endif
            </button>
        </div>

        {{-- Resultado da Migração --}}
        @if($migrationResult)
            <div class="mt-6 p-4 rounded bg-{{ $migrationResult['success'] ? 'green' : 'red' }}-100 text-{{ $migrationResult['success'] ? 'green' : 'red' }}-700 text-sm">
                {!! nl2br(e($migrationResult['message'])) !!}
                @if($migrationResult['logfile'])
                    <br><br>
                    <a href="{{ asset($migrationResult['logfile']) }}" class="underline text-blue-700" target="_blank">
                        Baixar Log da Migração
                    </a>
                @endif
            </div>
        @endif
    @endif
</div>
