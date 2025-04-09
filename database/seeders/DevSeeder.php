<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\dev\TaskSeeder;
use Database\Seeders\dev\SkillSeeder;
use Database\Seeders\dev\PackageSeeder;
use Database\Seeders\dev\DepartmentSeeder;
use Database\Seeders\dev\TaskCategorySeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            PackageSeeder::class,
            SkillSeeder::class,
            TaskCategorySeeder::class,
            TaskSeeder::class,
        ]);
    }
}
