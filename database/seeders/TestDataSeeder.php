<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Team;


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



        // Create teams
        $teams = Team::all();

        // Create team leader and assign them to teams
        $leaders = User::factory()->count(36)->create();
        $members = User::factory()->count(216)->create();

        $teamCount = $teams->count();

        $leaders->each(function ($leader, $index) use ($teams) {
            $leader->assignRole('Team Leader');
            
            DB::table('users_has_teams')->insert([
                'user_id' => $leader->id,
                'team_id' => $teams[$index]->id,  // Assign each leader to a unique team
            ]);
        });

        $members->each(function ($user, $index) use ($teams, $teamCount) {
            $user->assignRole('Member');
            
            DB::table('users_has_teams')->insert([
                'user_id' => $user->id,
                'team_id' => $teams[$index % $teamCount]->id,
            ]);
        });




        // User::factory()->count(36)->create()->each(function ($user) {
        //     $user->assignRole('Team Leader');
        // });
        // User::factory()->count(216)->create()->each(function ($user) {
        //     $user->assignRole('Member');
        // });
        
    }
}
