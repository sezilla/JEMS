<?php

namespace Database\Seeders\departmentTask;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Designing extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentId = config('seeder.department.designing.id');

        $skills = config('seeder.skill');
        $skillIds = [
            'communication',
            'planning',
            'creativity',
            'floral-arrangement',
            'professionalism',
            'gardening',
            'project-planning',
            'finishing-skills',
            'event-planning',
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
