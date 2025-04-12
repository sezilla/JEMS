<?php

namespace Database\Seeders\departmentTask;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhotoAndVideo extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departmentId = config('seeder.department.photo_and_video.id');

        $skills = config('seeder.skill');
        $skillIds = [
            'creativity',
            'grooming-knowledge',
            'versatility',
            'professionalism',
            'quick-setup',
            'storytelling',
            'photography',
            'aesthetic-sense',
            'technical-skills',
            'time-management',
            'drone-operation',
            'organization',
            'av-skills',
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
