<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class SkillTask extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('skills')->insert([
            ['name' => 'Scheduling'],
            ['name' => 'Communication'],
            ['name' => 'Organization'],
            ['name' => 'Attention to Detail'],
            ['name' => 'Delegation'],
            ['name' => 'Leadership'],
            ['name' => 'Documentation'],
            ['name' => 'Oversight'],
            ['name' => 'Time Management'],
            ['name' => 'Reporting'],
            ['name' => 'Planning'],
            ['name' => 'Organizational Skills'],
            ['name' => 'Adaptability'],
            ['name' => 'Problem-Solving'],
            ['name' => 'Coordination'],
            ['name' => 'Crisis Management'],
            ['name' => 'Multitasking'],
            ['name' => 'Menu Planning'],
            ['name' => 'Culinary Knowledge'],
            ['name' => 'Creativity'],
            ['name' => 'Quality Assessment'],
            ['name' => 'Sensory Evaluation'],
            ['name' => 'Logistics'],
            ['name' => 'Space Planning'],
            ['name' => 'Design Sense'],
            ['name' => 'Flexibility'],
            ['name' => 'Guest Focus'],
            ['name' => 'Budgeting'],
            ['name' => 'Customer Service'],
            ['name' => 'Interior Design'],
            ['name' => 'Professionalism'],
            ['name' => 'Listening Skills'],
            ['name' => 'Artistic Skills'],
            ['name' => 'Advanced Makeup Techniques'],
            ['name' => 'Precision'],
            ['name' => 'Basic Makeup Skills'],
            ['name' => 'Grooming Knowledge'],
            ['name' => 'Versatility'],
            ['name' => 'Quick Setup'],
            ['name' => 'Storytelling'],
            ['name' => 'Photography'],
            ['name' => 'Aesthetic Sense'],
            ['name' => 'Technical Skills'],
            ['name' => 'Drone Operation'],
            ['name' => 'AV Skills'],
            ['name' => 'Technical Setup'],
            ['name' => 'Video Editing'],
            ['name' => 'Photo Editing'],
            ['name' => 'Efficiency'],
            ['name' => 'Data Handling'],
            ['name' => 'Design Knowledge'],
            ['name' => 'Graphic Design'],
            ['name' => 'Typography'],
            ['name' => 'Spatial Design'],
            ['name' => 'Project Coordination'],
            ['name' => 'Floral Arrangement'],
            ['name' => 'Gardening'],
            ['name' => 'Project Planning'],
            ['name' => 'Finishing Skills'],
            ['name' => 'Event Planning'],
            ['name' => 'Audio-Visual Knowledge'],
            ['name' => 'Music Knowledge'],
            ['name' => 'Event Management'],
            ['name' => 'Event Coordination']
        ]);

        DB::table('task_skills')->insert([
            ['task_id' => 1, 'skill_id' => 1],
            ['task_id' => 1, 'skill_id' => 2],
            ['task_id' => 1, 'skill_id' => 3],
            ['task_id' => 2, 'skill_id' => 1],
            ['task_id' => 2, 'skill_id' => 4],
            ['task_id' => 3, 'skill_id' => 5],
            ['task_id' => 3, 'skill_id' => 6],
            ['task_id' => 3, 'skill_id' => 2],
            ['task_id' => 4, 'skill_id' => 7],
            ['task_id' => 4, 'skill_id' => 2],
            ['task_id' => 5, 'skill_id' => 8],
            ['task_id' => 5, 'skill_id' => 9],
            ['task_id' => 5, 'skill_id' => 10],
            ['task_id' => 6, 'skill_id' => 11],
            ['task_id' => 6, 'skill_id' => 12],
            ['task_id' => 6, 'skill_id' => 13],
            ['task_id' => 7, 'skill_id' => 14],
            ['task_id' => 7, 'skill_id' => 4],
            ['task_id' => 8, 'skill_id' => 2],
            ['task_id' => 8, 'skill_id' => 15],
            ['task_id' => 9, 'skill_id' => 16],
            ['task_id' => 9, 'skill_id' => 17],
            ['task_id' => 10, 'skill_id' => 18],
            ['task_id' => 10, 'skill_id' => 2],
            ['task_id' => 11, 'skill_id' => 19],
            ['task_id' => 11, 'skill_id' => 20],
            ['task_id' => 12, 'skill_id' => 21],
            ['task_id' => 12, 'skill_id' => 22],
            ['task_id' => 13, 'skill_id' => 23],
            ['task_id' => 13, 'skill_id' => 15],
            ['task_id' => 13, 'skill_id' => 2],
            ['task_id' => 14, 'skill_id' => 24],
            ['task_id' => 14, 'skill_id' => 25],
            ['task_id' => 15, 'skill_id' => 3],
            ['task_id' => 15, 'skill_id' => 4],
            ['task_id' => 16, 'skill_id' => 26],
            ['task_id' => 16, 'skill_id' => 27],
            ['task_id' => 16, 'skill_id' => 28],
            ['task_id' => 17, 'skill_id' => 3],
            ['task_id' => 17, 'skill_id' => 29],
            ['task_id' => 18, 'skill_id' => 30],
            ['task_id' => 18, 'skill_id' => 20],
            ['task_id' => 19, 'skill_id' => 13],
            ['task_id' => 19, 'skill_id' => 15],
            ['task_id' => 20, 'skill_id' => 9],
            ['task_id' => 20, 'skill_id' => 4],
            ['task_id' => 21, 'skill_id' => 2],
            ['task_id' => 21, 'skill_id' => 31],
            ['task_id' => 22, 'skill_id' => 20],
            ['task_id' => 22, 'skill_id' => 32],
            ['task_id' => 23, 'skill_id' => 33],
            ['task_id' => 23, 'skill_id' => 9],
            ['task_id' => 24, 'skill_id' => 34],
            ['task_id' => 24, 'skill_id' => 35],
            ['task_id' => 25, 'skill_id' => 36],
            ['task_id' => 25, 'skill_id' => 37],
            ['task_id' => 26, 'skill_id' => 9],
            ['task_id' => 26, 'skill_id' => 38],
            ['task_id' => 27, 'skill_id' => 13],
            ['task_id' => 27, 'skill_id' => 39],
            ['task_id' => 28, 'skill_id' => 20],
            ['task_id' => 28, 'skill_id' => 40],
            ['task_id' => 29, 'skill_id' => 41],
            ['task_id' => 29, 'skill_id' => 33],
            ['task_id' => 30, 'skill_id' => 3],
            ['task_id' => 30, 'skill_id' => 11],
            ['task_id' => 31, 'skill_id' => 20],
            ['task_id' => 31, 'skill_id' => 42],
            ['task_id' => 32, 'skill_id' => 43],
            ['task_id' => 32, 'skill_id' => 29],
            ['task_id' => 33, 'skill_id' => 44],
            ['task_id' => 33, 'skill_id' => 43],
            ['task_id' => 34, 'skill_id' => 45],
            ['task_id' => 34, 'skill_id' => 46],
            ['task_id' => 35, 'skill_id' => 47],
            ['task_id' => 35, 'skill_id' => 9],
            ['task_id' => 36, 'skill_id' => 48],
            ['task_id' => 36, 'skill_id' => 49],
            ['task_id' => 37, 'skill_id' => 50],
            ['task_id' => 37, 'skill_id' => 3],
            ['task_id' => 38, 'skill_id' => 43],
            ['task_id' => 38, 'skill_id' => 2],
            ['task_id' => 39, 'skill_id' => 20],
            ['task_id' => 39, 'skill_id' => 51],
            ['task_id' => 40, 'skill_id' => 52],
            ['task_id' => 40, 'skill_id' => 53],
            ['task_id' => 41, 'skill_id' => 54],
            ['task_id' => 41, 'skill_id' => 20],
            ['task_id' => 42, 'skill_id' => 55],
            ['task_id' => 42, 'skill_id' => 42],
            ['task_id' => 43, 'skill_id' => 56],
            ['task_id' => 43, 'skill_id' => 20],
            ['task_id' => 44, 'skill_id' => 57],
            ['task_id' => 44, 'skill_id' => 51],
            ['task_id' => 45, 'skill_id' => 46],
            ['task_id' => 45, 'skill_id' => 45],
            ['task_id' => 46, 'skill_id' => 58],
            ['task_id' => 46, 'skill_id' => 4],
            ['task_id' => 47, 'skill_id' => 59],
            ['task_id' => 47, 'skill_id' => 4],
            ['task_id' => 48, 'skill_id' => 2],
            ['task_id' => 48, 'skill_id' => 11],
            ['task_id' => 49, 'skill_id' => 20],
            ['task_id' => 49, 'skill_id' => 60],
            ['task_id' => 50, 'skill_id' => 43],
            ['task_id' => 50, 'skill_id' => 61],
            ['task_id' => 51, 'skill_id' => 2],
            ['task_id' => 51, 'skill_id' => 32],
            ['task_id' => 52, 'skill_id' => 46],
            ['task_id' => 52, 'skill_id' => 45],
            ['task_id' => 53, 'skill_id' => 15],
            ['task_id' => 53, 'skill_id' => 62],
            ['task_id' => 54, 'skill_id' => 2],
            ['task_id' => 54, 'skill_id' => 63],
            ['task_id' => 55, 'skill_id' => 64],
            ['task_id' => 55, 'skill_id' => 20],
        ]);

    }
}
