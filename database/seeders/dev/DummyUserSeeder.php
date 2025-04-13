<?php

namespace Database\Seeders\dev;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // HR Admin
        $hrAdmin = User::factory()->create();
        $hrAdmin->assignRole(config('filament-shield.admin_hr.name'));

        // Admin Dep - Catering
        $adminDepCatering = User::factory()->create();
        $adminDepCatering->assignRole(config('filament-shield.admin_dep.name'));
        $adminDepCatering->departments()->attach(config('seeder.department.catering.id'));

        // Admin Dep - Hair and Makeup
        $adminDepHairAndMakeup = User::factory()->create();
        $adminDepHairAndMakeup->assignRole(config('filament-shield.admin_dep.name'));
        $adminDepHairAndMakeup->departments()->attach(config('seeder.department.hair_and_makeup.id'));

        // Admin Dep - Photo and Video
        $adminDepPhotoAndVideo = User::factory()->create();
        $adminDepPhotoAndVideo->assignRole(config('filament-shield.admin_dep.name'));
        $adminDepPhotoAndVideo->departments()->attach(config('seeder.department.photo_and_video.id'));

        // Admin Dep - Designing
        $adminDepDesigning = User::factory()->create();
        $adminDepDesigning->assignRole(config('filament-shield.admin_dep.name'));
        $adminDepDesigning->departments()->attach(config('seeder.department.designing.id'));

        // Admin Dep - Entertainment
        $adminDepEntertainment = User::factory()->create();
        $adminDepEntertainment->assignRole(config('filament-shield.admin_dep.name'));
        $adminDepEntertainment->departments()->attach(config('seeder.department.entertainment.id'));

        // Admin Dep - Coordination
        $adminDepCoordination = User::factory()->create();
        $adminDepCoordination->assignRole(config('filament-shield.admin_dep.name'));
        $adminDepCoordination->departments()->attach(config('seeder.department.coordination.id'));



        //----------------------------------------------------------

        $departmentSkillsMap = [
            'coordination' => [
                1,  // Scheduling
                2,  // Communication
                3,  // Organization
                4,  // Attention to Detail
                5,  // Delegation
                6,  // Leadership
                7,  // Documentation
                8,  // Oversight
                9,  // Time Management
                10, // Reporting
                11, // Planning
                12, // Organizational Skills
                13, // Adaptability
                14, // Problem-Solving
                15, // Coordination
                16, // Crisis Management
                17, // Multitasking
                63, // Event Management
                64, // Event Coordination
            ],
            'catering' => [
                2,  // Communication
                3,  // Organization
                4,  // Attention to Detail
                9,  // Time Management
                15, // Coordination
                18, // Menu Planning
                19, // Culinary Knowledge
                20, // Creativity
                21, // Quality Assessment
                22, // Sensory Evaluation
                23, // Logistics
                24, // Space Planning
                25, // Design Sense
                26, // Flexibility
                27, // Guest Focus
                28, // Budgeting
                29, // Customer Service
            ],
            'hair_and_makeup' => [
                2,  // Communication
                9,  // Time Management
                13, // Adaptability
                20, // Creativity
                30, // Interior Design
                31, // Professionalism
                32, // Listening Skills
                33, // Artistic Skills
                34, // Advanced Makeup Techniques
                35, // Precision
                36, // Basic Makeup Skills
            ],
            'photo_and_video' => [
                3,  // Organization
                9,  // Time Management
                20, // Creativity
                31, // Professionalism
                37, // Grooming Knowledge
                38, // Versatility
                39, // Quick Setup
                40, // Storytelling
                41, // Photography
                42, // Aesthetic Sense
                43, // Technical Skills
                44, // Drone Operation
                45, // AV Skills
                46, // Technical Setup
                47, // Video Editing
                48, // Photo Editing
                61, // Audio-Visual Knowledge
                62, // Music Knowledge
            ],
            'designing' => [
                2,  // Communication
                11, // Planning
                20, // Creativity
                31, // Professionalism
                56, // Floral Arrangement
                57, // Gardening
                58, // Project Planning
                59, // Finishing Skills
                60, // Event Planning
            ],
            'entertainment' => [
                4,  // Attention to Detail
                20, // Creativity
                45, // AV Skills
                49, // Efficiency
                50, // Data Handling
                51, // Design Knowledge
                52, // Graphic Design
                53, // Typography
                54, // Spatial Design
                55, // Project Coordination
            ]
        ];
        // Coordinators -------------------------------------------------
        $coordinationDepartmentId = config('seeder.department.coordination.id');
        $coordinationTeams = Team::whereHas('departments', function ($query) use ($coordinationDepartmentId) {
            $query->where('department_id', $coordinationDepartmentId);
        })->get();

        $coordinationTeams = $coordinationTeams->shuffle();

        User::factory()->count(20)->create()->each(function ($user, $index) use ($coordinationTeams, $departmentSkillsMap) {
            $user->assignRole(config('filament-shield.coordinator_user.name'));

            $departmentKey = 'coordination';
            $departmentId = config('seeder.department.' . $departmentKey . '.id');
            $user->departments()->attach($departmentId);

            if ($index < $coordinationTeams->count()) {
                $user->teams()->attach($coordinationTeams[$index]->id);
            }

            $departmentSkills = $departmentSkillsMap[$departmentKey];

            $randomSkills = collect($departmentSkills)->shuffle()->take(3)->all();
            $user->skills()->attach($randomSkills);
        });

        // Team Leaders -------------------------------------------------
        // Catering Department leaders
        $cateringDepartmentId = config('seeder.department.catering.id');
        $cateringTeams = Team::whereHas('departments', function ($query) use ($cateringDepartmentId) {
            $query->where('department_id', $cateringDepartmentId);
        })->get();

        $cateringTeams = $cateringTeams->shuffle();

        User::factory()->count(5)->create()->each(function ($user, $index) use ($cateringTeams, $departmentSkillsMap) {
            $user->assignRole(config('filament-shield.leader_user.name'));

            $departmentKey = 'catering';
            $departmentId = config('seeder.department.' . $departmentKey . '.id');
            $user->departments()->attach($departmentId);

            if ($index < $cateringTeams->count()) {
                $user->teams()->attach($cateringTeams[$index]->id);
            }

            $departmentSkills = $departmentSkillsMap[$departmentKey];

            $randomSkills = collect($departmentSkills)->shuffle()->take(3)->all();
            $user->skills()->attach($randomSkills);
        });

        // Hair and Makeup Department leaders
        $hair_and_makeupDepartmentId = config('seeder.department.hair_and_makeup.id');
        $hair_and_makeupTeams = Team::whereHas('departments', function ($query) use ($hair_and_makeupDepartmentId) {
            $query->where('department_id', $hair_and_makeupDepartmentId);
        })->get();

        $hair_and_makeupTeams = $hair_and_makeupTeams->shuffle();

        User::factory()->count(5)->create()->each(function ($user, $index) use ($hair_and_makeupTeams, $departmentSkillsMap) {
            $user->assignRole(config('filament-shield.leader_user.name'));

            $departmentKey = 'hair_and_makeup';
            $departmentId = config('seeder.department.' . $departmentKey . '.id');
            $user->departments()->attach($departmentId);

            if ($index < $hair_and_makeupTeams->count()) {
                $user->teams()->attach($hair_and_makeupTeams[$index]->id);
            }

            $departmentSkills = $departmentSkillsMap[$departmentKey];

            $randomSkills = collect($departmentSkills)->shuffle()->take(3)->all();
            $user->skills()->attach($randomSkills);
        });

        // Photo and Video Department leaders
        $photo_and_videoDepartmentId = config('seeder.department.photo_and_video.id');
        $photo_and_videoTeams = Team::whereHas('departments', function ($query) use ($photo_and_videoDepartmentId) {
            $query->where('department_id', $photo_and_videoDepartmentId);
        })->get();

        $photo_and_videoTeams = $photo_and_videoTeams->shuffle();

        User::factory()->count(5)->create()->each(function ($user, $index) use ($photo_and_videoTeams, $departmentSkillsMap) {
            $user->assignRole(config('filament-shield.leader_user.name'));

            $departmentKey = 'photo_and_video';
            $departmentId = config('seeder.department.' . $departmentKey . '.id');
            $user->departments()->attach($departmentId);

            if ($index < $photo_and_videoTeams->count()) {
                $user->teams()->attach($photo_and_videoTeams[$index]->id);
            }

            $departmentSkills = $departmentSkillsMap[$departmentKey];

            $randomSkills = collect($departmentSkills)->shuffle()->take(3)->all();
            $user->skills()->attach($randomSkills);
        });

        // Designing Department leaders
        $designingDepartmentId = config('seeder.department.designing.id');
        $designingTeams = Team::whereHas('departments', function ($query) use ($designingDepartmentId) {
            $query->where('department_id', $designingDepartmentId);
        })->get();

        $designingTeams = $designingTeams->shuffle();

        User::factory()->count(5)->create()->each(function ($user, $index) use ($designingTeams, $departmentSkillsMap) {
            $user->assignRole(config('filament-shield.leader_user.name'));

            $departmentKey = 'designing';
            $departmentId = config('seeder.department.' . $departmentKey . '.id');
            $user->departments()->attach($departmentId);

            if ($index < $designingTeams->count()) {
                $user->teams()->attach($designingTeams[$index]->id);
            }

            $departmentSkills = $departmentSkillsMap[$departmentKey];

            $randomSkills = collect($departmentSkills)->shuffle()->take(3)->all();
            $user->skills()->attach($randomSkills);
        });

        // Entertainment Department leaders
        $entertainmentDepartmentId = config('seeder.department.entertainment.id');
        $entertainmentTeams = Team::whereHas('departments', function ($query) use ($entertainmentDepartmentId) {
            $query->where('department_id', $entertainmentDepartmentId);
        })->get();

        $entertainmentTeams = $entertainmentTeams->shuffle();

        User::factory()->count(5)->create()->each(function ($user, $index) use ($entertainmentTeams, $departmentSkillsMap) {
            $user->assignRole(config('filament-shield.leader_user.name'));

            $departmentKey = 'entertainment';
            $departmentId = config('seeder.department.' . $departmentKey . '.id');
            $user->departments()->attach($departmentId);

            if ($index < $entertainmentTeams->count()) {
                $user->teams()->attach($entertainmentTeams[$index]->id);
            }

            $departmentSkills = $departmentSkillsMap[$departmentKey];

            $randomSkills = collect($departmentSkills)->shuffle()->take(3)->all();
            $user->skills()->attach($randomSkills);
        });

        // Team Members -------------------------------------------------

        // Catering Department members
        $cateringDepartmentId = config('seeder.department.catering.id');
        $cateringTeams = Team::whereHas('departments', function ($query) use ($cateringDepartmentId) {
            $query->where('department_id', $cateringDepartmentId);
        })->get();

        $cateringTeams = $cateringTeams->shuffle();

        User::factory()->count(18)->create()->each(function ($user, $index) use ($cateringTeams, $departmentSkillsMap) {
            $user->assignRole(config('filament-shield.member_user.name'));

            $departmentKey = 'catering';
            $departmentId = config('seeder.department.' . $departmentKey . '.id');
            $user->departments()->attach($departmentId);

            if ($index < $cateringTeams->count()) {
                $user->teams()->attach($cateringTeams[$index]->id);
            }

            $departmentSkills = $departmentSkillsMap[$departmentKey];

            $randomSkills = collect($departmentSkills)->shuffle()->take(3)->all();
            $user->skills()->attach($randomSkills);
        });

        // Hair and Makeup Department members
        $hair_and_makeupDepartmentId = config('seeder.department.hair_and_makeup.id');
        $hair_and_makeupTeams = Team::whereHas('departments', function ($query) use ($hair_and_makeupDepartmentId) {
            $query->where('department_id', $hair_and_makeupDepartmentId);
        })->get();

        $hair_and_makeupTeams = $hair_and_makeupTeams->shuffle();

        User::factory()->count(18)->create()->each(function ($user, $index) use ($hair_and_makeupTeams, $departmentSkillsMap) {
            $user->assignRole(config('filament-shield.member_user.name'));

            $departmentKey = 'hair_and_makeup';
            $departmentId = config('seeder.department.' . $departmentKey . '.id');
            $user->departments()->attach($departmentId);

            if ($index < $hair_and_makeupTeams->count()) {
                $user->teams()->attach($hair_and_makeupTeams[$index]->id);
            }

            $departmentSkills = $departmentSkillsMap[$departmentKey];

            $randomSkills = collect($departmentSkills)->shuffle()->take(3)->all();
            $user->skills()->attach($randomSkills);
        });

        // Photo and Video Department members
        $photo_and_videoDepartmentId = config('seeder.department.photo_and_video.id');
        $photo_and_videoTeams = Team::whereHas('departments', function ($query) use ($photo_and_videoDepartmentId) {
            $query->where('department_id', $photo_and_videoDepartmentId);
        })->get();

        $photo_and_videoTeams = $photo_and_videoTeams->shuffle();

        User::factory()->count(18)->create()->each(function ($user, $index) use ($photo_and_videoTeams, $departmentSkillsMap) {
            $user->assignRole(config('filament-shield.member_user.name'));

            $departmentKey = 'photo_and_video';
            $departmentId = config('seeder.department.' . $departmentKey . '.id');
            $user->departments()->attach($departmentId);

            if ($index < $photo_and_videoTeams->count()) {
                $user->teams()->attach($photo_and_videoTeams[$index]->id);
            }

            $departmentSkills = $departmentSkillsMap[$departmentKey];

            $randomSkills = collect($departmentSkills)->shuffle()->take(3)->all();
            $user->skills()->attach($randomSkills);
        });

        // Designing Department members
        $designingDepartmentId = config('seeder.department.designing.id');
        $designingTeams = Team::whereHas('departments', function ($query) use ($designingDepartmentId) {
            $query->where('department_id', $designingDepartmentId);
        })->get();

        $designingTeams = $designingTeams->shuffle();

        User::factory()->count(18)->create()->each(function ($user, $index) use ($designingTeams, $departmentSkillsMap) {
            $user->assignRole(config('filament-shield.member_user.name'));

            $departmentKey = 'designing';
            $departmentId = config('seeder.department.' . $departmentKey . '.id');
            $user->departments()->attach($departmentId);

            if ($index < $designingTeams->count()) {
                $user->teams()->attach($designingTeams[$index]->id);
            }

            $departmentSkills = $departmentSkillsMap[$departmentKey];

            $randomSkills = collect($departmentSkills)->shuffle()->take(3)->all();
            $user->skills()->attach($randomSkills);
        });

        // Entertainment Department members
        $entertainmentDepartmentId = config('seeder.department.entertainment.id');
        $entertainmentTeams = Team::whereHas('departments', function ($query) use ($entertainmentDepartmentId) {
            $query->where('department_id', $entertainmentDepartmentId);
        })->get();

        $entertainmentTeams = $entertainmentTeams->shuffle();

        User::factory()->count(18)->create()->each(function ($user, $index) use ($entertainmentTeams, $departmentSkillsMap) {
            $user->assignRole(config('filament-shield.member_user.name'));

            $departmentKey = 'entertainment';
            $departmentId = config('seeder.department.' . $departmentKey . '.id');
            $user->departments()->attach($departmentId);

            if ($index < $entertainmentTeams->count()) {
                $user->teams()->attach($entertainmentTeams[$index]->id);
            }

            $departmentSkills = $departmentSkillsMap[$departmentKey];

            $randomSkills = collect($departmentSkills)->shuffle()->take(3)->all();
            $user->skills()->attach($randomSkills);
        });
    }
}
