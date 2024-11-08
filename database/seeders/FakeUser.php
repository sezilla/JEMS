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
        $coordinators = User::factory()->count(40)->create()->each(function ($user) {
            $user->assignRole('Coordinator');
        });

        $departmentSkills = [
            6 => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17], // Coordination Department
            1 => [18, 2, 19, 20, 21, 22, 23, 15, 2, 24, 25, 3, 4, 26, 27, 9, 28, 29], // Catering Department
            2 => [2, 30, 20, 31, 32, 33, 34, 9, 35, 13, 36], // Hair and Makeup Department
            3 => [20, 37, 38, 31, 39, 40, 41, 42, 43, 9, 44, 3, 45], // Photo and Video Department
            4 => [20, 49, 50, 51, 52, 53, 54, 45, 55, 4], // Designing Department
            5 => [2, 11, 20, 56, 2, 31, 57, 58, 59, 20, 60], // Entertainment Department
        ];

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
        

        foreach ($coordinators as $index => $coordinator) {
            $assignment = $departments_has_teams[$index % count($departments_has_teams)];
            $departmentId = $assignment['department_id'];
            $coordinator->departments()->attach($departmentId);
            $coordinator->teams()->attach($assignment['team_id']);

            // Attach 3 random skills from the department's skill list
            $skills = $departmentSkills[$departmentId] ?? [];
            $randomSkills = collect($skills)->random(3);
            $coordinator->skills()->syncWithoutDetaching($randomSkills);
        }

        $leaders = User::factory()->count(36)->create()->each(function ($user) {
            $user->assignRole('Team Leader');
        });

        foreach ($leaders as $index => $leader) {
            $assignment = $departments_has_teams[$index % count($departments_has_teams)];
            $departmentId = $assignment['department_id'];
            $leader->departments()->attach($departmentId);
            $leader->teams()->attach($assignment['team_id']);

            // Attach 3 random skills from the department's skill list
            $skills = $departmentSkills[$departmentId] ?? [];
            $randomSkills = collect($skills)->random(3);
            $leader->skills()->syncWithoutDetaching($randomSkills);
        }

        $members = User::factory()->count(432)->create()->each(function ($user) {
            $user->assignRole('Member');
        });

        foreach ($members as $index => $member) {
            $assignment = $departments_has_teams[$index % count($departments_has_teams)];
            $departmentId = $assignment['department_id'];
            $member->departments()->attach($departmentId);
            $member->teams()->attach($assignment['team_id']);

            // Attach 3 random skills from the department's skill list
            $skills = $departmentSkills[$departmentId] ?? [];
            $randomSkills = collect($skills)->random(3);
            $member->skills()->syncWithoutDetaching($randomSkills);
        }
    }
}
