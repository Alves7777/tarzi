<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class BaseAdminRolesSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = (string) config('base-admin.guard', 'web');

        $superAdminName = (string) config('filament-shield.super_admin.name', 'super_admin');
        $panelUserName = (string) config('filament-shield.panel_user.name', 'panel_user');

        $superAdmin = Role::firstOrCreate([
            'name' => $superAdminName,
            'guard_name' => $guard,
        ]);

        Role::firstOrCreate([
            'name' => $panelUserName,
            'guard_name' => $guard,
        ]);

        $permissions = Permission::query()
            ->where('guard_name', $guard)
            ->get();

        if ($permissions->isEmpty()) {
            $this->command?->warn("No permissions found for guard [{$guard}]. Run `php artisan shield:generate --all` first.");

            return;
        }

        $superAdmin->syncPermissions($permissions);

        $this->command?->info("Base admin roles seeded for guard [{$guard}] ({$permissions->count()} permissions).");
    }
}
