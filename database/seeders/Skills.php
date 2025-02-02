<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Skills extends Seeder
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
    }
}
