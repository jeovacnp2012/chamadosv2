<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ResetPermissionsCommand extends Command
{
    protected $signature = 'permissions:reset';
    protected $description = 'Apaga e recria todas as permissoes e roles padrao com sincronizacao para o Super Admin';

    public function handle(): void
    {
        $this->warn('Limpando permissoes e papeis existentes...');

        // Apagar permissões e roles (cuidado em produção)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::query()->delete();
        Role::query()->delete();

        $this->info('Recriando permissoes com base nos Resources...');

        $resources = ['user', 'department', 'address'];
        $actions = ['view', 'create', 'update', 'delete'];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "$action $resource"]);
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        $this->info('Permissoes e role "Super Admin" recriadas com sucesso.');
    }
}
