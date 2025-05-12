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

        // Exibir lista de permissoes criadas
        echo "Permissões criadas:\n";
        foreach ($permissionsCriadas as $p) {
            echo "- $p\n";
        }
    }
}
