<?php

namespace Database\Seeders\departmentTask;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Catering extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentId = config('seeder.department.catering.id');

        $skills = config('seeder.skill');
        $skillIds = [
            'menu-planning',
            'culinary-knowledge',
            'creativity',
            'quality-assessment',
            'sensory-evaluation',
            'logistics',
            'coordination',
            'communication',
            'space-planning',
            'design-sense',
            'organization',
            'attention-to-detail',
            'flexibility',
            'guest-focus',
            'time-management',
            'budgeting',
            'customer-service',
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
