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

        foreach ($skills as $skill) {
            Skill::updateOrCreate([
                'name' => $skill['name'],
                'description' => $skill['description'],
            ]);
        }
    }
}
