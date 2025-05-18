<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Database\Seeders\dev\RoleSeeder;
use Database\Seeders\dev\TeamSeeder;
use Database\Seeders\dev\SkillSeeder;
use Database\Seeders\dev\DepartmentSeeder;
use Database\Seeders\dev\PermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@email.com',
        ]);

        // $this->call([
        //     RoleSeeder::class,
        //     PermissionSeeder::class,
        //     DepartmentSeeder::class,
        //     TeamSeeder::class,
        //     SkillSeeder::class,
        // ]);
    }
}
