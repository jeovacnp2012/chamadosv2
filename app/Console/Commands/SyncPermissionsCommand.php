<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'permissions:sync';
    protected $description = 'Gera e sincroniza permissoes com base nos Filament Resources';

    public function handle(): void
    {
        $actions = ['view', 'create', 'update', 'delete'];
        $resourcePath = app_path('Filament/Resources');
        $files = File::allFiles($resourcePath);
        $permissionsCriadas = [];

        foreach ($files as $file) {
            $name = $file->getFilenameWithoutExtension();
            if (Str::endsWith($name, 'Resource')) {
                $resource = Str::kebab(str_replace('Resource', '', $name));

                foreach ($actions as $action) {
                    $permissionName = "$action $resource";
                    Permission::firstOrCreate(['name' => $permissionName]);
                    $permissionsCriadas[] = $permissionName;
                }
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        $this->info('Permissoes sincronizadas com sucesso:');
        foreach ($permissionsCriadas as $p) {
            $this->line("- $p");
        }
    }
}
