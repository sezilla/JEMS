<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;


class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Teams
        DB::table('teams')->insert([
            ['name' => 'Team 1', 'description' => 'Alpha team description'],
            ['name' => 'Team 2', 'description' => 'Beta team description'],
            ['name' => 'Team 3', 'description' => 'Gamma team description'],
            ['name' => 'Team 4', 'description' => 'Gamma team description'],
            ['name' => 'Team 5', 'description' => 'Gamma team description'],
            ['name' => 'Team 6', 'description' => 'Gamma team description'],
            ['name' => 'Team 7', 'description' => 'Gamma team description'],
            ['name' => 'Team 8', 'description' => 'Gamma team description'],
            ['name' => 'Team 9', 'description' => 'Gamma team description'],
            ['name' => 'Team 10', 'description' => 'Gamma team description'],
            ['name' => 'Team 11', 'description' => 'Alpha team description'],
            ['name' => 'Team 12', 'description' => 'Beta team description'],
            ['name' => 'Team 13', 'description' => 'Gamma team description'],
            ['name' => 'Team 14', 'description' => 'Gamma team description'],
            ['name' => 'Team 15', 'description' => 'Gamma team description'],
            ['name' => 'Team 16', 'description' => 'Gamma team description'],
            ['name' => 'Team 17', 'description' => 'Gamma team description'],
            ['name' => 'Team 18', 'description' => 'Gamma team description'],
            ['name' => 'Team 19', 'description' => 'Gamma team description'],
            ['name' => 'Team 20', 'description' => 'Gamma team description'],
            ['name' => 'Team 21', 'description' => 'Gamma team description'],
        ]);

        // Seed Event Types
        // DB::table('event_types')->insert([
        //     ['name' => 'Event Type A', 'description' => 'Description for Event Type A'],
        //     ['name' => 'Event Type B', 'description' => 'Description for Event Type B'],
        //     ['name' => 'Event Type C', 'description' => 'Description for Event Type C'],
        // ]);

        
        //test
        User::factory()->count(3)->create()->each(function ($user) {
            $user->assignRole('Admin');
        });
        User::factory()->count(10)->create()->each(function ($user) {
            $user->assignRole('Coordinator');
        });
        User::factory()->count(10)->create()->each(function ($user) {
            $user->assignRole('Team Leader');
        });
        User::factory()->count(30)->create()->each(function ($user) {
            $user->assignRole('Member');
        });
        
    }
}
