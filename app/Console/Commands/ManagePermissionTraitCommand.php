<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ManagePermissionTraitCommand extends Command
{
    protected $signature = 'permissions:manage-trait';
    protected $description = 'Gerencia a trait ChecksResourcePermission em Resources do Filament';

    public function handle()
    {
        $resourcePath = app_path('Filament/Resources');

        if (! File::exists($resourcePath)) {
            $this->error('Diretório de Resources não encontrado.');
            return;
        }

        $option = $this->choice('O que deseja fazer?', [
            'Listar',
            'Aplicar',
            'Remover',
        ], 0);

        $files = File::allFiles($resourcePath);

        foreach ($files as $file) {
            $path = $file->getPathname();
            $content = File::get($path);

            if ($option === 'Listar') {
                if (str_contains($content, 'ChecksResourcePermission')) {
                    $this->line("✅ {$file->getFilename()} já usa a trait.");
                } else {
                    $this->warn("❌ {$file->getFilename()} não usa a trait.");
                }
            }

            if ($option === 'Aplicar') {
                $modified = false;

                if (! str_contains($content, 'use App\\Traits\\ChecksResourcePermission;')) {
                    $content = preg_replace(
                        '/namespace .*?;/',
                        "$0\n\nuse App\\Traits\\ChecksResourcePermission;",
                        $content
                    );
                    $modified = true;
                }

                if (! str_contains($content, 'use ChecksResourcePermission;')) {
                    $content = preg_replace_callback(
                        '/class\s+\w+\s+extends\s+\w+\s*\{/',
                        function ($matches) {
                            return $matches[0] . "\n    use ChecksResourcePermission;";
                        },
                        $content
                    );
                    $modified = true;
                }

                if ($modified) {
                    File::put($path, $content);
                    $this->info("✅ Trait injetada em {$file->getFilename()}.");
                } else {
                    $this->line("⏭️  Trait já presente em {$file->getFilename()}.");
                }
            }

            if ($option === 'Remover') {
                $original = $content;

                // Remove ambas as linhas mesmo com espaços, tabs ou quebras variadas
                $content = preg_replace('/^\s*use App\\\\Traits\\\\ChecksResourcePermission;\s*$/m', '', $content);
                $content = preg_replace('/^\s*use ChecksResourcePermission;\s*$/m', '', $content);

                if ($content !== $original) {
                    File::put($path, $content);
                    $this->warn("❌ Trait removida de {$file->getFilename()}.");
                } else {
                    $this->line("⏭️ Nenhuma trait encontrada em {$file->getFilename()}.");
                }
            }
        }

        $this->info('Concluído!');
    }
}
