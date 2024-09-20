<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class Roles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'Admin']);
        $coorRole = Role::create(['name' => 'Coordinator']);
        Role::create(['name' => 'Team Leader']);
        Role::create(['name' => 'Member']);

        //supah admin
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@email.com',
        ]);
        //test admin
        $admin = User::factory()->create([
            'name' => 'ako',
            'email' => 'ako@me.com',
        ]);
        //test coor
        $coor = User::factory()->create([
            'name' => 'coor',
            'email' => 'coor@email.com',
        ]);
        $admin->assignRole($adminRole);
        $coor->assignRole($coorRole);

        



        //test
        User::factory()->count(3)->create()->each(function ($user) {
            $user->assignRole('Admin');
        });
        User::factory()->count(5)->create()->each(function ($user) {
            $user->assignRole('Coordinator');
        });
        User::factory()->count(5)->create()->each(function ($user) {
            $user->assignRole('Team Leader');
        });
        User::factory()->count(5)->create()->each(function ($user) {
            $user->assignRole('Member');
        });
    }
}
