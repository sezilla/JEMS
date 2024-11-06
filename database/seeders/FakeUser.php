<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class FakeUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed roles for Admin and Coordinator
        User::factory()->count(10)->create()->each(function ($user) {
            $user->assignRole('Admin');
        });
        User::factory()->count(40)->create()->each(function ($user) {
            $user->assignRole('Coordinator');
        });

        $departments_has_teams = [
            ["department_id" => 1, "team_id" => 1],
            ["department_id" => 1, "team_id" => 2],
            ["department_id" => 1, "team_id" => 3],
            ["department_id" => 1, "team_id" => 4],
            ["department_id" => 1, "team_id" => 5],
            ["department_id" => 1, "team_id" => 6],
            ["department_id" => 2, "team_id" => 7],
            ["department_id" => 2, "team_id" => 8],
            ["department_id" => 2, "team_id" => 9],
            ["department_id" => 2, "team_id" => 10],
            ["department_id" => 2, "team_id" => 11],
            ["department_id" => 2, "team_id" => 12],
            ["department_id" => 3, "team_id" => 13],
            ["department_id" => 3, "team_id" => 14],
            ["department_id" => 3, "team_id" => 15],
            ["department_id" => 3, "team_id" => 16],
            ["department_id" => 3, "team_id" => 17],
            ["department_id" => 3, "team_id" => 18],
            ["department_id" => 4, "team_id" => 19],
            ["department_id" => 4, "team_id" => 20],
            ["department_id" => 4, "team_id" => 21],
            ["department_id" => 4, "team_id" => 22],
            ["department_id" => 4, "team_id" => 23],
            ["department_id" => 4, "team_id" => 24],
            ["department_id" => 5, "team_id" => 25],
            ["department_id" => 5, "team_id" => 26],
            ["department_id" => 5, "team_id" => 27],
            ["department_id" => 5, "team_id" => 28],
            ["department_id" => 5, "team_id" => 29],
            ["department_id" => 5, "team_id" => 30],
            ["department_id" => 6, "team_id" => 31],
            ["department_id" => 6, "team_id" => 32],
            ["department_id" => 6, "team_id" => 33],
            ["department_id" => 6, "team_id" => 34],
            ["department_id" => 6, "team_id" => 35],
            ["department_id" => 6, "team_id" => 36],
            ["department_id" => 1, "team_id" => 37],
            ["department_id" => 2, "team_id" => 38],
            ["department_id" => 3, "team_id" => 39],
            ["department_id" => 4, "team_id" => 40],
            ["department_id" => 5, "team_id" => 41],
            ["department_id" => 6, "team_id" => 42],
        ];
        

        $leaders = User::factory()->count(36)->create()->each(function ($user) {
            $user->assignRole('Team Leader');
        });
        $members = User::factory()->count(432)->create()->each(function ($user) {
            $user->assignRole('Member');
        });


        // $leaders = User::factory()->count(36)->create();
        // $members = User::factory()->count(432)->create();

        // Assign leaders to departments and teams
        foreach ($leaders as $index => $leader) {
            $assignment = $departments_has_teams[$index % count($departments_has_teams)];
            $leader->departments()->attach($assignment['department_id']);
            $leader->teams()->attach($assignment['team_id']);
        }

        // Assign members to departments and teams
        foreach ($members as $index => $member) {
            $assignment = $departments_has_teams[$index % count($departments_has_teams)];
            $member->departments()->attach($assignment['department_id']);
            $member->teams()->attach($assignment['team_id']);
        }
    }
}
