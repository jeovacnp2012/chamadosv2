<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RemovePageAuthorizationCommand extends Command
{
    protected $signature = 'permissions:remove-page-authorization';
    protected $description = 'Remove o metodo authorizeAccess() das Pages do Filament se presente';

    public function handle(): void
    {
        $pagesPath = app_path('Filament/Resources');
        $pageFiles = File::allFiles($pagesPath);

        foreach ($pageFiles as $file) {
            $path = $file->getRealPath();
            $content = File::get($path);

            if (str_contains($content, 'authorizeAccess')) {
                $newContent = preg_replace(
                    '/\n\s*protected function authorizeAccess\(\): void\n\s*\{[^}]*\}\n/m',
                    "\n",
                    $content
                );

                File::put($path, $newContent);
                $this->info("🧹 Metodo authorizeAccess removido de: " . $file->getFilename());
            }
        }

        $this->info('✅ Todas as Pages foram processadas.');
    }
}
