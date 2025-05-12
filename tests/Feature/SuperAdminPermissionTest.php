<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuperAdminPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_user_resource(): void
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'Super Admin']);
        $user->assignRole($role);

        $this->assertTrue($user->can('view user'));
    }
}
