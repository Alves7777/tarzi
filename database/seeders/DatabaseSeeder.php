<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BaseAdminRolesSeeder::class);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@zeivoll.com.br'],
            [
                'name' => 'Admin Zeivoll',
                'password' => Hash::make('password'),
            ],
        );

        $superAdminRole = Role::findByName('super_admin', 'web');
        $admin->syncRoles([$superAdminRole]);

        $this->call(SignageSeeder::class);
    }
}
