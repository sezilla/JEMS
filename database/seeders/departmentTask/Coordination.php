<?php

namespace Database\Seeders\departmentTask;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Coordination extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentId = config('seeder.department.coordination.id');

        $skills = config('seeder.skill');
        $skillIds = [
            'scheduling',
            'communication',
            'organization',
            'attention-to-detail',
            'delegation',
            'leadership',
            'documentation',
            'oversight',
            'time-management',
            'reporting',
            'planning',
            'organizational-skills',
            'adaptability',
            'problem-solving',
            'coordination',
            'crisis-management',
            'multitasking',
            'event-management',
            'event-coordination'
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
