<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\dev\RoleSeeder;
use Database\Seeders\dev\TaskSeeder;
use Database\Seeders\dev\TeamSeeder;
use Database\Seeders\dev\PackageTask;
use Database\Seeders\dev\SkillSeeder;
use Database\Seeders\dev\PackageSeeder;
use Database\Seeders\dev\DummyUserSeeder;
use Database\Seeders\dev\DepartmentSeeder;
use Database\Seeders\dev\PermissionSeeder;
use Database\Seeders\dev\TestProjectSeeder;
use Database\Seeders\dev\TaskCategorySeeder;

class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            DepartmentSeeder::class,
            TeamSeeder::class,
            SkillSeeder::class,
            PackageSeeder::class,
            TaskCategorySeeder::class,
            TaskSeeder::class,
            PackageTask::class,
            DummyUserSeeder::class,
            // TestProjectSeeder::class,
        ]);
    }
}
