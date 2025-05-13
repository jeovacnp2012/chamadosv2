<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InjectPageAuthorizationCommand extends Command
{
    protected $signature = 'permissions:inject-page-authorization';
    protected $description = 'Injeta metodo authorizeAccess() nas Filament Pages de cada Resource, se ausente';

    public function handle(): void
    {
        $pagesPath = app_path('Filament/Resources');
        $pageFiles = File::allFiles($pagesPath);

        foreach ($pageFiles as $file) {
            $path = $file->getRealPath();
            $content = File::get($path);

            if (str_contains($content, 'extends') && str_contains($content, 'Page') && ! str_contains($content, 'authorizeAccess')) {
                $injected = "\n    protected function authorizeAccess(): void\n    {\n        abort_unless(static::getResource()::canViewAny(), 403);\n    }\n";

                $updatedContent = preg_replace('/\{\n/', "{\n{$injected}", $content, 1);
                File::put($path, $updatedContent);
                $this->info("🔐 authorizeAccess() injetado em: " . $file->getFilename());
            }
        }

        $this->info('✅ Todas as Filament Pages foram analisadas.');
    }
}
