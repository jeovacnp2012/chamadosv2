<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Criar a role "Admin" se nao existir
        $admin = Role::firstOrCreate(['name' => 'Admin']);

        // Atribuir todas as permissoes existentes
        $admin->syncPermissions(Permission::all());

        // Opcional: exibir permissões aplicadas
        $this->command->info('Role "Admin" sincronizada com todas as permissoes.');
    }
}
