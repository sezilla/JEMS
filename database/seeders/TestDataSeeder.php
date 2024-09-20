<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Teams
        DB::table('teams')->insert([
            ['name' => 'Team Alpha', 'description' => 'Alpha team description'],
            ['name' => 'Team Beta', 'description' => 'Beta team description'],
            ['name' => 'Team Gamma', 'description' => 'Gamma team description'],
        ]);

        // Seed Event Types
        DB::table('event_types')->insert([
            ['name' => 'Event Type A', 'description' => 'Description for Event Type A'],
            ['name' => 'Event Type B', 'description' => 'Description for Event Type B'],
            ['name' => 'Event Type C', 'description' => 'Description for Event Type C'],
        ]);

        // Seed Packages
        DB::table('packages')->insert([
            ['name' => 'Package One', 'description' => 'Description for Package One', 'event_type_id' => 1],
            ['name' => 'Package Two', 'description' => 'Description for Package Two', 'event_type_id' => 2],
            ['name' => 'Package Three', 'description' => 'Description for Package Three', 'event_type_id' => 3],
        ]);

        // Seed Departments
        DB::table('departments')->insert([
            ['name' => 'Department X', 'description' => 'Description for Department X'],
            ['name' => 'Department Y', 'description' => 'Description for Department Y'],
            ['name' => 'Department Z', 'description' => 'Description for Department Z'],
        ]);

        // Seed Attributes
        DB::table('attributes')->insert([
            ['name' => 'Attribute 1', 'description' => 'Description for Attribute 1'],
            ['name' => 'Attribute 2', 'description' => 'Description for Attribute 2'],
            ['name' => 'Attribute 3', 'description' => 'Description for Attribute 3'],
        ]);



        
    }
}
