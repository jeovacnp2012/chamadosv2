<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ListPermissionTraitUsageCommand extends Command
{
    protected $signature = 'permissions:list-trait-usage';
    protected $description = 'Lista os Resources do Filament e indica se a trait ChecksResourcePermission esta aplicada';

    public function handle(): void
    {
        $resourcePath = app_path('Filament/Resources');
        $files = File::allFiles($resourcePath);

        $this->info("📋 Verificando uso da trait ChecksResourcePermission:");

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $content = File::get($file->getRealPath());

            $usesTrait = str_contains($content, 'ChecksResourcePermission') ? '✅ Sim' : '❌ Não';

            $this->line("{$filename}: {$usesTrait}");
        }
    }
}
