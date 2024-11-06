<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Skill;
use Illuminate\Database\Seeder;
class UserSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skillIds = Skill::pluck('id')->toArray();

        // Loop through each user
        User::all()->each(function ($user) use ($skillIds) {
            // Randomly select 3 skill IDs for each user
            $randomSkills = collect($skillIds)->random(3);
            
            // Attach the skills to the user
            $user->skills()->syncWithoutDetaching($randomSkills);
        });
    }
}
