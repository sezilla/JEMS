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

        // Coordinator Users
        // $coordinationDepartmentId = config('seeder.department.coordination.id');
        // $coordinationTeams = Team::whereHas('departments', function ($query) use ($coordinationDepartmentId) {
        //     $query->where('department_id', $coordinationDepartmentId);
        // })->get();

        // $coordinationTeams = $coordinationTeams->shuffle();

        // User::factory()->count(24)->create()->each(function ($user, $index) use ($coordinationTeams) {
        //     $user->assignRole(config('filament-shield.coordinator_user.name'));

        //     $user->departments()->attach(config('seeder.department.coordination.id'));

        //     if ($index < $coordinationTeams->count()) {
        //         $user->teams()->attach($coordinationTeams[$index]->id);
        //     }
        // });

        // Team Leaders
        // Catering Department leaders
        $cateringDepartmentId = config('seeder.department.catering.id');
        $cateringTeams = Team::whereHas('departments', function ($query) use ($cateringDepartmentId) {
            $query->where('department_id', $cateringDepartmentId);
        })->get();

        $cateringTeams = $cateringTeams->shuffle();

        User::factory()->count(6)->create()->each(function ($user, $index) use ($cateringTeams) {
            $user->assignRole(config('filament-shield.leader_user.name'));

            $user->departments()->attach(config('seeder.department.catering.id'));

            if ($index < $cateringTeams->count()) {
                $user->teams()->attach($cateringTeams[$index]->id);
            }
        });

        // Hair and Makeup Department leaders
        $hairAndMakeupDepartmentId = config('seeder.department.hair_and_makeup.id');
        $hairAndMakeupTeams = Team::whereHas('departments', function ($query) use ($hairAndMakeupDepartmentId) {
            $query->where('department_id', $hairAndMakeupDepartmentId);
        })->get();

        $hairAndMakeupTeams = $hairAndMakeupTeams->shuffle();

        User::factory()->count(6)->create()->each(function ($user, $index) use ($hairAndMakeupTeams) {
            $user->assignRole(config('filament-shield.leader_user.name'));

            $user->departments()->attach(config('seeder.department.hair_and_makeup.id'));

            if ($index < $hairAndMakeupTeams->count()) {
                $user->teams()->attach($hairAndMakeupTeams[$index]->id);
            }
        });

        // Photo and Video Department leaders
        $photoAndVideoDepartmentId = config('seeder.department.photo_and_video.id');
        $photoAndVideoTeams = Team::whereHas('departments', function ($query) use ($photoAndVideoDepartmentId) {
            $query->where('department_id', $photoAndVideoDepartmentId);
        })->get();

        $photoAndVideoTeams = $photoAndVideoTeams->shuffle();

        User::factory()->count(6)->create()->each(function ($user, $index) use ($photoAndVideoTeams) {
            $user->assignRole(config('filament-shield.leader_user.name'));

            $user->departments()->attach(config('seeder.department.photo_and_video.id'));

            if ($index < $photoAndVideoTeams->count()) {
                $user->teams()->attach($photoAndVideoTeams[$index]->id);
            }
        });

        // Designing Department leaders
        $designingDepartmentId = config('seeder.department.designing.id');
        $designingTeams = Team::whereHas('departments', function ($query) use ($designingDepartmentId) {
            $query->where('department_id', $designingDepartmentId);
        })->get();

        $designingTeams = $designingTeams->shuffle();

        User::factory()->count(6)->create()->each(function ($user, $index) use ($designingTeams) {
            $user->assignRole(config('filament-shield.leader_user.name'));

            $user->departments()->attach(config('seeder.department.designing.id'));

            if ($index < $designingTeams->count()) {
                $user->teams()->attach($designingTeams[$index]->id);
            }
        });

        // Entertainment Department leaders
        $entertainmentDepartmentId = config('seeder.department.entertainment.id');
        $entertainmentTeams = Team::whereHas('departments', function ($query) use ($entertainmentDepartmentId) {
            $query->where('department_id', $entertainmentDepartmentId);
        })->get();

        $entertainmentTeams = $entertainmentTeams->shuffle();

        User::factory()->count(6)->create()->each(function ($user, $index) use ($entertainmentTeams) {
            $user->assignRole(config('filament-shield.leader_user.name'));

            $user->departments()->attach(config('seeder.department.entertainment.id'));

            if ($index < $entertainmentTeams->count()) {
                $user->teams()->attach($entertainmentTeams[$index]->id);
            }
        });

        // Members

        // Catering Department
        $cateringDepartmentId = config('seeder.department.catering.id');
        $cateringTeams = Team::whereHas('departments', function ($query) use ($cateringDepartmentId) {
            $query->where('department_id', $cateringDepartmentId);
        })->get();
        $cateringTeams = $cateringTeams->shuffle();
        User::factory()->count(6)->create()->each(function ($user, $index) use ($cateringTeams) {
            $user->assignRole(config('filament-shield.member_user.name'));

            $user->departments()->attach(config('seeder.department.catering.id'));

            if ($index < $cateringTeams->count()) {
                $user->teams()->attach($cateringTeams[$index]->id);
            }
        });

        // Hair and Makeup Department
        $hairAndMakeupDepartmentId = config('seeder.department.hair_and_makeup.id');
        $hairAndMakeupTeams = Team::whereHas('departments', function ($query) use ($hairAndMakeupDepartmentId) {
            $query->where('department_id', $hairAndMakeupDepartmentId);
        })->get();
        $hairAndMakeupTeams = $hairAndMakeupTeams->shuffle();
        User::factory()->count(6)->create()->each(function ($user, $index) use ($hairAndMakeupTeams) {
            $user->assignRole(config('filament-shield.member_user.name'));

            $user->departments()->attach(config('seeder.department.hair_and_makeup.id'));

            if ($index < $hairAndMakeupTeams->count()) {
                $user->teams()->attach($hairAndMakeupTeams[$index]->id);
            }
        });

        // Photo and Video Department
        $photoAndVideoDepartmentId = config('seeder.department.photo_and_video.id');
        $photoAndVideoTeams = Team::whereHas('departments', function ($query) use ($photoAndVideoDepartmentId) {
            $query->where('department_id', $photoAndVideoDepartmentId);
        })->get();
        $photoAndVideoTeams = $photoAndVideoTeams->shuffle();
        User::factory()->count(6)->create()->each(function ($user, $index) use ($photoAndVideoTeams) {
            $user->assignRole(config('filament-shield.member_user.name'));

            $user->departments()->attach(config('seeder.department.photo_and_video.id'));

            if ($index < $photoAndVideoTeams->count()) {
                $user->teams()->attach($photoAndVideoTeams[$index]->id);
            }
        });

        // Designing Department
        $designingDepartmentId = config('seeder.department.designing.id');
        $designingTeams = Team::whereHas('departments', function ($query) use ($designingDepartmentId) {
            $query->where('department_id', $designingDepartmentId);
        })->get();
        $designingTeams = $designingTeams->shuffle();
        User::factory()->count(6)->create()->each(function ($user, $index) use ($designingTeams) {
            $user->assignRole(config('filament-shield.member_user.name'));

            $user->departments()->attach(config('seeder.department.designing.id'));

            if ($index < $designingTeams->count()) {
                $user->teams()->attach($designingTeams[$index]->id);
            }
        });

        // Entertainment Department
        $entertainmentDepartmentId = config('seeder.department.entertainment.id');
        $entertainmentTeams = Team::whereHas('departments', function ($query) use ($entertainmentDepartmentId) {
            $query->where('department_id', $entertainmentDepartmentId);
        })->get();
        $entertainmentTeams = $entertainmentTeams->shuffle();
        User::factory()->count(6)->create()->each(function ($user, $index) use ($entertainmentTeams) {
            $user->assignRole(config('filament-shield.member_user.name'));

            $user->departments()->attach(config('seeder.department.entertainment.id'));

            if ($index < $entertainmentTeams->count()) {
                $user->teams()->attach($entertainmentTeams[$index]->id);
            }
        });

        // Coordination Department
        $coordinationDepartmentId = config('seeder.department.coordination.id');
        $coordinationTeams = Team::whereHas('departments', function ($query) use ($coordinationDepartmentId) {
            $query->where('department_id', $coordinationDepartmentId);
        })->get();
        $coordinationTeams = $coordinationTeams->shuffle();
        User::factory()->count(6)->create()->each(function ($user, $index) use ($coordinationTeams) {
            $user->assignRole(config('filament-shield.coordinator_user.name'));

            $user->departments()->attach(config('seeder.department.coordination.id'));

            if ($index < $coordinationTeams->count()) {
                $user->teams()->attach($coordinationTeams[$index]->id);
            }
        });
    }
}
