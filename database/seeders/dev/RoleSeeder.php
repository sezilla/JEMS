<?php

namespace Database\Seeders\dev;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin role
        if (config('filament-shield.super_admin.enabled')) {
            Role::updateOrCreate([
                'name' => config('filament-shield.super_admin.name'),
            ]);
        }

        // Panel User role
        if (config('filament-shield.panel_user.enabled')) {
            Role::updateOrCreate([
                'name' => config('filament-shield.panel_user.name'),
            ]);
        }

        // HR Admin role
        if (config('filament-shield.admin_hr.enabled')) {
            Role::updateOrCreate([
                'name' => config('filament-shield.admin_hr.name'),
            ]);
        }

        // Department Admin role
        if (config('filament-shield.admin_dep.enabled')) {
            Role::updateOrCreate([
                'name' => config('filament-shield.admin_dep.name'),
            ]);
        }

        // Coordinator role
        if (config('filament-shield.coordinator_user.enabled')) {
            Role::updateOrCreate([
                'name' => config('filament-shield.coordinator_user.name'),
            ]);
        }

        // Team Leader role
        if (config('filament-shield.leader_user.enabled')) {
            Role::updateOrCreate([
                'name' => config('filament-shield.leader_user.name'),
            ]);
        }

        // Member role
        if (config('filament-shield.member_user.enabled')) {
            Role::updateOrCreate([
                'name' => config('filament-shield.member_user.name'),
            ]);
        }
    }
}
