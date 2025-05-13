<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class AuditResourcesCommand extends Command
{
    protected $signature = 'permissions:audit-resources';
    protected $description = 'Audita os Resources Filament quanto a duplicacao, uso da trait e existencia de permissoes';

    public function handle(): void
    {
        $resourcePath = app_path('Filament/Resources');
        $resources = collect();

        foreach (File::allFiles($resourcePath) as $file) {
            $filename = $file->getFilename();
            $basename = Str::before($filename, '.php');
            $resources->push($basename);
        }

        $this->info("🔍 Verificando duplicidades...");
        $duplicates = $resources->duplicates();
        if ($duplicates->isNotEmpty()) {
            $this->warn("❗ Duplicatas encontradas:");
            $duplicates->each(fn($dup) => $this->line(" - $dup"));
        } else {
            $this->info("✅ Nenhuma duplicata encontrada.");
        }

        $this->newLine();
        $this->info("🔍 Verificando uso da trait ChecksResourcePermission e permissao 'view' correspondente...");

        foreach (File::allFiles($resourcePath) as $file) {
            if (! Str::endsWith($file->getFilename(), 'Resource.php')) {
                continue;
            }

            $path = $file->getRealPath();
            $content = File::get($path);
            $resourceName = $file->getFilename();

            $hasTrait = Str::contains($content, 'ChecksResourcePermission') ? '✅' : '❌';

            $base = Str::before($resourceName, 'Resource.php');
            $permission = 'view ' . Str::kebab($base);
            $permExists = Permission::where('name', $permission)->exists() ? '✅' : '❌';

            $this->line("{$resourceName} → Trait: {$hasTrait} | Permissao: {$permExists} ({$permission})");
        }

        $this->info("\n📌 Auditoria concluida.");
    }
}
