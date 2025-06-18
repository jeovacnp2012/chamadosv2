<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">

    {{-- Info das Bases - APENAS ADICIONADO ISSO --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
            <h3 class="font-bold text-blue-800 text-sm">📤 ORIGEM (de onde vem)</h3>
            <p class="text-xs text-blue-700">
                <strong>Database:</strong> {{ config('database.connections.origem.database') }}
            </p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
            <h3 class="font-bold text-green-800 text-sm">📥 DESTINO (para onde vai)</h3>
            <p class="text-xs text-green-700">
                <strong>Database:</strong> {{ config('database.connections.destino.database') }}
            </p>
        </div>
    </div>

    @if($step === 1)
        <h2 class="text-2xl font-bold mb-4">Passo 1: Selecione as Tabelas</h2>
        <form wire:submit.prevent="nextStep">
            <div class="mb-4">
                <label class="block font-bold mb-1">Tabelas disponíveis:</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($tables as $key => $label)
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="selectedTables" value="{{ $key }}" class="mr-2">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="reset" class="mr-2">
                    Zerar dados das tabelas selecionadas antes de importar
                </label>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Próximo</button>
        </form>
    @elseif($step === 2)
        @php
            $currentTable = $selectedTables[$tableStep ?? 0] ?? null;
            $origFields = $tablesFields[$currentTable]['origem'] ?? [];
            $destFields = $tablesFields[$currentTable]['destino'] ?? [];
        @endphp

        <h2 class="text-2xl font-bold mb-4">
            Passo 2: Mapeamento de Campos - <span class="text-blue-700">{{ $currentTable }}</span>
            ({{ ($tableStep ?? 0) + 1 }} de {{ count($selectedTables) }})
        </h2>

        <form wire:submit.prevent="nextStep">
            @if(!$this->canProceedTable($currentTable))
                <div class="p-2 bg-red-100 text-red-700 rounded mb-2 text-xs">
                    Preencha todos os mapeamentos, evite duplicidade e aceite os tipos diferentes para avançar.
                </div>
            @endif

            <div class="mb-4">
                <table class="min-w-full border text-xs">
                    <thead>
                    <tr>
                        <th class="border p-2">Campo Origem</th>
                        <th class="border p-2">Tipo Origem</th>
                        <th class="border p-2">Campo Destino</th>
                        <th class="border p-2">Tipo Destino</th>
                        <th class="border p-2">Tipo Diferente?</th>
                        <th class="border p-2">Aceitar?</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($origFields as $field => $typeOrig)
                        @php
                            $destField = $fieldsMapping[$currentTable][$field] ?? '';
                            $typeDest = $destFields[$destField] ?? '';
                            $typeDiff = ($typeDest && $typeDest !== $typeOrig);
                            $selectedThis = $fieldsMapping[$currentTable][$field] ?? '';
                            $alreadyMapped = collect($fieldsMapping[$currentTable])->except($field)->values()->all();
                        @endphp
                        <tr>
                            <td class="border p-2">{{ $field }}</td>
                            <td class="border p-2">{{ $typeOrig }}</td>
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
                            <td class="border p-2">{{ $typeDest }}</td>
                            <td class="border p-2">
                                @if($typeDiff)
                                    <span class="text-red-600 font-bold">Sim</span>
                                @else
                                    <span class="text-green-600">Não</span>
                                @endif
                            </td>
                            <td class="border p-2 text-center">
                                @if($typeDiff)
                                    <input type="checkbox"
                                           wire:model="fieldsTypeDiffs.{{ $currentTable }}.{{ $field }}.accept"
                                    >
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-2 flex flex-wrap items-center gap-2">
                @if(collect($fieldsTypeDiffs[$currentTable] ?? [])->filter(fn($x) => !($x['accept'] ?? false))->count())
                    <label class="flex items-center gap-2 text-xs text-orange-700 font-bold">
                        <input type="checkbox" wire:click="acceptAllTypesDiff('{{ $currentTable }}')">
                        Aceitar todos os campos de tipo diferente desta tabela
                    </label>
                @endif

                <button type="button" wire:click="resetMapping('{{ $currentTable }}')" class="text-xs text-orange-700 underline">
                    Resetar Mapeamento
                </button>

                <button type="button" wire:click="autoFixMapping('{{ $currentTable }}')" class="text-xs bg-green-600 text-white px-2 py-1 rounded">
                    🔧 Corrigir Automaticamente
                </button>
            </div>

            <div class="flex gap-3 mt-6">
                @if(($tableStep ?? 0) > 0)
                    <button wire:click.prevent="prevStep" type="button" class="px-4 py-2 rounded bg-gray-300">Anterior</button>
                @endif

                <button type="submit"
                        class="px-4 py-2 rounded bg-blue-600 text-white"
                        @if(!$this->canProceedTable($currentTable)) disabled @endif
                >
                    {{ ($tableStep ?? 0) === count($selectedTables)-1 ? 'Confirmar Mapeamento' : 'Próximo' }}
                </button>

                {{-- Botão de debug temporário --}}
                <button wire:click="debugStep2" type="button" class="px-3 py-2 rounded bg-red-500 text-white text-xs">
                    Debug
                </button>
            </div>
        </form>
    @elseif($step === 3)
        <h2 class="text-2xl font-bold mb-4">Resumo do Mapeamento</h2>
        @foreach($selectedTables as $table)
            <div class="mb-4 border-b pb-3">
                <strong class="block text-blue-700 mb-1">{{ $table }}</strong>
                <table class="min-w-full border text-xs mb-2">
                    <thead>
                    <tr>
                        <th class="border p-2">Campo Origem</th>
                        <th class="border p-2">Campo Destino</th>
                        <th class="border p-2">Tipo Origem</th>
                        <th class="border p-2">Tipo Destino</th>
                        <th class="border p-2">Tipo Aceito?</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($fieldsMapping[$table] ?? [] as $orig => $dest)
                        @php
                            $typeOrig = $tablesFields[$table]['origem'][$orig] ?? '';
                            $typeDest = $tablesFields[$table]['destino'][$dest] ?? '';
                            $typeDiff = ($typeDest && $typeDest !== $typeOrig);
                            $accepted = $fieldsTypeDiffs[$table][$orig]['accept'] ?? false;
                        @endphp
                        <tr>
                            <td class="border p-2">{{ $orig }}</td>
                            <td class="border p-2">{{ $dest }}</td>
                            <td class="border p-2">{{ $typeOrig }}</td>
                            <td class="border p-2">{{ $typeDest }}</td>
                            <td class="border p-2">
                                @if($typeDiff)
                                    @if($accepted)
                                        <span class="text-green-700">Aceito</span>
                                    @else
                                        <span class="text-red-700">Não aceito</span>
                                    @endif
                                @else
                                    <span class="text-gray-600">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

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
