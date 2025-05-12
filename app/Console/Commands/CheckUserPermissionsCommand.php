<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class CheckUserPermissionsCommand extends Command
{
    protected $signature = 'permissions:check {userId?}';
    protected $description = 'Exibe as permissoes e papeis do usuario logado ou de um ID especifico';

    public function handle(): void
    {
        $user = $this->argument('userId')
            ? \App\Models\User::find($this->argument('userId'))
            : Auth::user();

        if (! $user) {
            $this->error('Usuário não encontrado.');
            return;
        }

        $this->info("Usuário: {$user->name} ({$user->email})");
        $this->line("ID: {$user->id}");

        $roles = $user->getRoleNames();
        $this->info('Papeis:');
        $roles->isEmpty()
            ? $this->line('  - (nenhum)')
            : $roles->each(fn ($r) => $this->line("  - $r"));

        $permissions = $user->getPermissionNames();
        $this->info('Permissões diretas ou via papel:');
        $permissions->isEmpty()
            ? $this->line('  - (nenhuma)')
            : $permissions->each(fn ($p) => $this->line("  - $p"));

        $this->newLine();
    }
}
