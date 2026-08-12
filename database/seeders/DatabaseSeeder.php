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

        $tarziAdvertiser = \App\Models\Advertiser::query()->where('email', 'contato@tarzi.com.br')->first();

        if ($tarziAdvertiser !== null) {
            $advertiserUser = User::query()->updateOrCreate(
                ['email' => 'anunciante@tarzi.com.br'],
                [
                    'name' => 'Tarzi Anunciante',
                    'password' => Hash::make('password'),
                    'advertiser_id' => $tarziAdvertiser->id,
                ],
            );

            $advertiserRole = Role::findByName('advertiser', 'web');
            $advertiserUser->syncRoles([$advertiserRole]);
        }

        // Garante que super_admin tenha todas as permissões (após shield:generate).
        $this->call(BaseAdminRolesSeeder::class);
    }
}
