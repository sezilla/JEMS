<?php

namespace Database\Seeders\dev;

use Illuminate\Database\Seeder;
use Database\Seeders\permissions\Member;
use Database\Seeders\permissions\TeamLeader;
use Database\Seeders\permissions\Coordinator;
use Database\Seeders\permissions\HrDepartment;
use Database\Seeders\permissions\DepartmentAdmin;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            Coordinator::class,
            DepartmentAdmin::class,
            HrDepartment::class,
            TeamLeader::class,
            Member::class,
        ]);
    }
}
