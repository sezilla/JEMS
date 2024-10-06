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
        $coorRole = Role::create(['name' => 'Coordinator']);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Team Leader']);
        Role::create(['name' => 'Member']);

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
        $coor->assignRole($coorRole);

        

        // Seed Packages
        DB::table('packages')->insert([
            ['name' => 'Ruby', 'description' => 'Description for Package One', 'event_type_id' => 1],
            ['name' => 'Garnet', 'description' => 'Description for Package Two', 'event_type_id' => 1],
            ['name' => 'Emerald', 'description' => 'Description for Package Three', 'event_type_id' => 1],
            ['name' => 'Infinity', 'description' => 'Description for Package Three', 'event_type_id' => 1],
            ['name' => 'Sapphire', 'description' => 'Description for Package Three', 'event_type_id' => 1],
        ]);

        // Seed Departments
        DB::table('departments')->insert([
            ['name' => 'Coordination', 'description' => 'Description for Department X'],
            ['name' => 'Catering', 'description' => 'Description for Department Y'],
            ['name' => 'Hair and Makeup', 'description' => 'Description for Department Z'],
            ['name' => 'Photo and Video', 'description' => 'Description for Department Z'],
            ['name' => 'Designing', 'description' => 'Description for Department Z'],
            ['name' => 'Entertainment', 'description' => 'Description for Department Z'],
            ['name' => 'drivers', 'description' => 'Description for Department Z'],
        ]);


        
    }
}
