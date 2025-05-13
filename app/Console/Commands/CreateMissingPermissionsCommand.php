<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class CreateMissingPermissionsCommand extends Command
{
    protected $signature = 'permissions:create-missing';
    protected $description = 'Cria permissoes view/create/update/delete para Resources do Filament que ainda nao possuem';

    public function handle(): void
    {
        $resourcePath = app_path('Filament/Resources');
        $actions = ['view', 'create', 'update', 'delete'];
        $criados = [];

        foreach (File::allFiles($resourcePath) as $file) {
            if (! Str::endsWith($file->getFilename(), 'Resource.php')) {
                continue;
            }

            $base = Str::before($file->getFilename(), 'Resource.php');
            $resource = Str::kebab($base);

            foreach ($actions as $action) {
                $name = "$action $resource";
                if (! Permission::where('name', $name)->exists()) {
                    Permission::create(['name' => $name]);
                    $criados[] = $name;
                }
            }
        }

        if (empty($criados)) {
            $this->info('✅ Nenhuma permissao faltando. Tudo em ordem.');
        } else {
            $this->info("✅ Permissoes criadas:");
            foreach ($criados as $p) {
                $this->line(" - $p");
            }
        }
    }
}
