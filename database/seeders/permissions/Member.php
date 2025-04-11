<?php

namespace Database\Seeders\permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Member extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => config('filament-shield.member_user.name')]);

        $permissions = [
            'view_department',
            'view_any_department',

            'view_package',
            'view_any_package',

            'view_project',
            'view_any_project',

            'view_skill',
            'view_any_skill',

            'view_task',
            'view_any_task',

            'view_task::category',
            'view_any_task::category',

            'view_team',
            'view_any_team',

            'view_user',
            'view_any_user',
            
            'page_Calendar',
            'page_Chat',
            'page_EditProfilePage',
            'widget_ProjectCalendar',
            'widget_StatsOverview',
            'widget_UsersLineChart'
            ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $role->givePermissionTo($permission);
        }
    }
}
