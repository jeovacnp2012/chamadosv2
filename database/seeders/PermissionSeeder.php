<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Acoes aplicadas a cada resource
        $actions = ['view', 'create', 'update', 'delete'];

        // Caminho dos resources
        $resourcePath = app_path('Filament/Resources');

        // Detectar arquivos de resources
        $files = File::allFiles($resourcePath);

        foreach ($files as $file) {
            $name = $file->getFilenameWithoutExtension();
            if (Str::endsWith($name, 'Resource')) {
                $resource = Str::kebab(str_replace('Resource', '', $name));

                foreach ($actions as $action) {
                    Permission::firstOrCreate(['name' => "$action $resource"]);
                }
            }
        }

        // Atribuir todas as permissoes ao Super Admin
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());
    }
}
