<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\dev\RoleSeeder;
use Database\Seeders\dev\PermissionSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}
