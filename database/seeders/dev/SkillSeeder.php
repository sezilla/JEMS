<?php

namespace Database\Seeders\dev;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = Config::get('seeder.skill');

        foreach ($skills as $slug => $skill) {
            $skillModel = Skill::updateOrCreate(
                [
                    'name' => $skill['name'],
                    'description' => $skill['description'],
                ]
            );

            if (isset($skill['departments']) && is_array($skill['departments'])) {
                $skillModel->department()->syncWithoutDetaching($skill['departments']);
            }
        }
    }
}
