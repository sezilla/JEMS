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

        // Seed Event Types
        // DB::table('event_types')->insert([
        //     ['name' => 'Event Type A', 'description' => 'Description for Event Type A'],
        //     ['name' => 'Event Type B', 'description' => 'Description for Event Type B'],
        //     ['name' => 'Event Type C', 'description' => 'Description for Event Type C'],
        // ]);

        
        //test
        User::factory()->count(10)->create()->each(function ($user) {
            $user->assignRole('Admin');
        });
        User::factory()->count(20)->create()->each(function ($user) {
            $user->assignRole('Coordinator');
        });
        User::factory()->count(20)->create()->each(function ($user) {
            $user->assignRole('Team Leader');
        });
        User::factory()->count(100)->create()->each(function ($user) {
            $user->assignRole('Member');
        });
        
    }
}
