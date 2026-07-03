<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $permission = Permission::firstOrCreate(['name' => 'create-player-note']);

        $role = Role::firstOrCreate(['name' => 'support-agent']);
        $role->givePermissionTo($permission);

        $user = User::factory()->create([
            'name' => 'Support Agent',
            'email' => 'ejemplo@example.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->count(5)->create();

        $user->assignRole($role);
    }
}
