<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class Roles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coorRole = Role::create(['name' => 'Coordinator']);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Team Leader']);
        Role::create(['name' => 'Member']);

        //test admin
        $admin = User::factory()->create([
            'name' => 'ako',
            'email' => 'ako@me.com',
        ]);

        //test coor
        $coor = User::factory()->create([
            'name' => 'coor',
            'email' => 'coor@email.com',
        ]);
        $coor->assignRole($coorRole);

        

        // Seed Packages
        DB::table('packages')->insert([
            ['name' => 'Ruby', 'description' => 'Description for Package One'],
            ['name' => 'Garnet', 'description' => 'Description for Package Two'],
            ['name' => 'Emerald', 'description' => 'Description for Package Three'],
            ['name' => 'Infinity', 'description' => 'Description for Package Three'],
            ['name' => 'Sapphire', 'description' => 'Description for Package Three'],
        ]);

        DB::table('departments')->insert([
            ['name' => 'Coordination', 'description' => 'Description for Coordination Department'],
            ['name' => 'Catering', 'description' => 'Description for Catering Department'],
            ['name' => 'Hair and Makeup', 'description' => 'Description for Hair and Makeup Department'],
            ['name' => 'Photo and Video', 'description' => 'Description for Photo and Video Department'],
            ['name' => 'Designing', 'description' => 'Description for Designing Department'],
            ['name' => 'Entertainment', 'description' => 'Description for Entertainment Department'],
            ['name' => 'Drivers', 'description' => 'Description for Drivers Department'],
        ]);

        // Get department IDs
        $coordinationId = DB::table('departments')->where('name', 'Coordination')->value('id');
        $cateringId = DB::table('departments')->where('name', 'Catering')->value('id');
        $hairMakeupId = DB::table('departments')->where('name', 'Hair and Makeup')->value('id');
        $photoVideoId = DB::table('departments')->where('name', 'Photo and Video')->value('id');
        $designingId = DB::table('departments')->where('name', 'Designing')->value('id');
        $entertainmentId = DB::table('departments')->where('name', 'Entertainment')->value('id');
        $driversId = DB::table('departments')->where('name', 'Drivers')->value('id');

        // Seed Teams and link them to their departments
        $teams = [
            'Catering' => [
                ['name' => 'Catering Team A', 'description' => 'Catering team A description'],
                ['name' => 'Catering Team B', 'description' => 'Catering team B description'],
                ['name' => 'Catering Team C', 'description' => 'Catering team C description'],
                ['name' => 'Catering Team D', 'description' => 'Catering team D description'],
                ['name' => 'Catering Team E', 'description' => 'Catering team E description'],
                ['name' => 'Catering Team F', 'description' => 'Catering team F description'],
            ],
            'Hair and Makeup' => [
                ['name' => 'Hair and Makeup Team A', 'description' => 'Hair and Makeup team A description'],
                ['name' => 'Hair and Makeup Team B', 'description' => 'Hair and Makeup team B description'],
                ['name' => 'Hair and Makeup Team C', 'description' => 'Hair and Makeup team C description'],
                ['name' => 'Hair and Makeup Team D', 'description' => 'Hair and Makeup team D description'],
                ['name' => 'Hair and Makeup Team E', 'description' => 'Hair and Makeup team E description'],
                ['name' => 'Hair and Makeup Team F', 'description' => 'Hair and Makeup team F description'],
            ],
            'Photo and Video' => [
                ['name' => 'Photo and Video Team A', 'description' => 'Photo and Video team A description'],
                ['name' => 'Photo and Video Team B', 'description' => 'Photo and Video team B description'],
                ['name' => 'Photo and Video Team C', 'description' => 'Photo and Video team C description'],
                ['name' => 'Photo and Video Team D', 'description' => 'Photo and Video team D description'],
                ['name' => 'Photo and Video Team E', 'description' => 'Photo and Video team E description'],
                ['name' => 'Photo and Video Team F', 'description' => 'Photo and Video team F description'],
            ],
            'Designing' => [
                ['name' => 'Designing Team A', 'description' => 'Designing team A description'],
                ['name' => 'Designing Team B', 'description' => 'Designing team B description'],
                ['name' => 'Designing Team C', 'description' => 'Designing team C description'],
                ['name' => 'Designing Team D', 'description' => 'Designing team D description'],
                ['name' => 'Designing Team E', 'description' => 'Designing team E description'],
                ['name' => 'Designing Team F', 'description' => 'Designing team F description'],
            ],
            'Entertainment' => [
                ['name' => 'Entertainment Team A', 'description' => 'Entertainment team A description'],
                ['name' => 'Entertainment Team B', 'description' => 'Entertainment team B description'],
                ['name' => 'Entertainment Team C', 'description' => 'Entertainment team C description'],
                ['name' => 'Entertainment Team D', 'description' => 'Entertainment team D description'],
                ['name' => 'Entertainment Team E', 'description' => 'Entertainment team E description'],
                ['name' => 'Entertainment Team F', 'description' => 'Entertainment team F description'],
            ],
            'Drivers' => [
                ['name' => 'Drivers Team A', 'description' => 'Drivers team A description'],
                ['name' => 'Drivers Team B', 'description' => 'Drivers team B description'],
                ['name' => 'Drivers Team C', 'description' => 'Drivers team C description'],
                ['name' => 'Drivers Team D', 'description' => 'Drivers team D description'],
                ['name' => 'Drivers Team E', 'description' => 'Drivers team E description'],
                ['name' => 'Drivers Team F', 'description' => 'Drivers team F description'],
            ]
        ];

        foreach ($teams as $departmentName => $teamData) {
            // Get the department ID
            $departmentId = DB::table('departments')->where('name', $departmentName)->value('id');

            foreach ($teamData as $team) {
                // Insert team and get team ID
                $teamId = DB::table('teams')->insertGetId([
                    'name' => $team['name'],
                    'description' => $team['description'],
                ]);

                // Insert into departments_has_teams
                DB::table('departments_has_teams')->insert([
                    'department_id' => $departmentId,
                    'team_id' => $teamId,
                ]);
            }
        }
    }
}
