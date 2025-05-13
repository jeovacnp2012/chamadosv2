<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ManagePermissionTraitCommand extends Command
{
    protected $signature = 'permissions:manage-trait';
    protected $description = 'Gerencia a trait ChecksResourcePermission em todos os Resources do Filament';

    public function handle(): void
    {
        $resourcePath = app_path('Filament/Resources');
        $files = File::allFiles($resourcePath);

        $choice = $this->choice(
            'O que deseja fazer?',
            ['Listar', 'Aplicar', 'Remover'],
            0
        );

        foreach ($files as $file) {
            $path = $file->getRealPath();
            $content = File::get($path);
            $filename = $file->getFilename();

            switch ($choice) {
                case 'Listar':
                    $hasTrait = str_contains($content, 'ChecksResourcePermission') ? '✅ Sim' : '❌ Não';
                    $this->line("{$filename}: {$hasTrait}");
                    break;

                case 'Aplicar':
                    if (! str_contains($content, 'ChecksResourcePermission')) {
                        $content = preg_replace(
                            '/namespace .*?;/',
                            "\$0\n\nuse App\\Traits\\ChecksResourcePermission;",
                            $content
                        );
                        $content = preg_replace(
                            '/class .*?\{/',
                            "\$0\n    use ChecksResourcePermission;\n",
                            $content
                        );
                        File::put($path, $content);
                        $this->info("✔ Trait aplicada: {$filename}");
                    }
                    break;

                case 'Remover':
                    if (str_contains($content, 'ChecksResourcePermission')) {
                        $content = str_replace("use App\\Traits\\ChecksResourcePermission;\n", '', $content);
                        $content = str_replace("use ChecksResourcePermission;", '', $content);
                        File::put($path, $content);
                        $this->info("❌ Trait removida: {$filename}");
                    }
                    break;
            }
        }

        $this->info('🏁 Operação concluída.');
    }
}
