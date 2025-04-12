<?php

namespace Database\Seeders\departmentTask;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Entertainment extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentId = config('seeder.department.entertainment.id');

        $skills = config('seeder.skill');
        $skillIds = [
            'creativity',
            'efficiency',
            'data-handling',
            'design-knowledge',
            'graphic-design',
            'typography',
            'spatial-design',
            'av-skills',
            'project-coordination',
            'attention-to-detail',
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
