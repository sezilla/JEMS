<?php

namespace Database\Seeders\departmentTask;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HairAndMakeup extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentId = config('seeder.department.hair_and_makeup.id');

        $skills = config('seeder.skill');
        $skillIds = [
            'communication',
            'interior-design',
            'creativity',
            'professionalism',
            'listening-skills',
            'artistic-skills',
            'advanced-makeup-techniques',
            'time-management',
            'precision',
            'adaptability',
            'basic-makeup-skills',
        ];

        foreach ($skillIds as $skillId) {
            $skill = $skills[$skillId];

            $exists = DB::table('department_has_skills')
                ->where('department_id', $departmentId)
                ->where('skill_id', $skill['id'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('department_has_skills')->insert([
                'department_id' => $departmentId,
                'skill_id' => $skill['id'],
            ]);
        }
    }
}
