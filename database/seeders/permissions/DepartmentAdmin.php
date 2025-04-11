<?php

namespace Database\Seeders\permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentAdmin extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => config('filament-shield.admin_dep.name')]);

        $permissions = [
            'view_department',
            'view_any_department',
            'create_department',
            'update_department',
            'delete_department',
            'delete_any_department',
            'restore_department',
            'force_delete_department',

            'view_package',
            'view_any_package',
            'create_package',
            'delete_package',
            'update_package',

            'view_project',
            'view_any_project',
            'create_project',
            'update_project',
            'delete_project',
            'restore_project',

            'view_skill',
            'view_any_skill',
            'create_skill',
            'update_skill',
            'delete_skill',

            'view_task',
            'view_any_task',
            'create_task',
            'update_task',
            'delete_task',

            'view_task::category',
            'view_any_task::category',
            'create_task::category',
            'update_task::category',
            'delete_task::category',

            'view_team',
            'view_any_team',
            'create_team',
            'update_team',
            'delete_team',

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
