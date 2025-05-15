<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ApplyPermissionTraitCommand extends Command
{
    protected $signature = 'permissions:inject-trait';
    protected $description = 'Aplica a trait ChecksResourcePermission a todos os Resources do Filament';

    public function handle(): void
    {
        $resourcePath = app_path('Filament/Resources');
        $traitUseLine = "use App\\Traits\\ChecksResourcePermission;";

        $files = File::allFiles($resourcePath);
        foreach ($files as $file) {
            $path = $file->getRealPath();
            $content = File::get($path);

            if (! str_contains($content, 'ChecksResourcePermission')) {
                // Inserir o use na parte de cima
                $content = preg_replace(
                    '/namespace .*?;/',
                    "\$0\n\n$traitUseLine",
                    $content
                );

                // Inserir a trait no corpo da classe
                $content = preg_replace(
                    '/class .*?\{/',
                    "\$0\n    use ChecksResourcePermission;\n",
                    $content
                );

                File::put($path, $content);
                $this->info("✔ Trait aplicada: {$file->getFilename()}");
            }
        }

        $this->info('✅ Todos os Resources foram atualizados com a trait.');
    }
}
