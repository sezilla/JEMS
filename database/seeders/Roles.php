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
        // $coorRole = Role::create(['name' => 'Coordinator']);
        // Role::create(['name' => 'Admin']);
        // Role::create(['name' => 'Team Leader']);
        // Role::create(['name' => 'Member']);

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
            ['name' => 'Ruby', 'description' => 'Description for Package One', 'trello_board_template_id' => '672005ebbe4e5d63395fdb71'],
            ['name' => 'Garnet', 'description' => 'Description for Package Two', 'trello_board_template_id' => '6720067b832a7827b2adcf9b'],
            ['name' => 'Emerald', 'description' => 'Description for Package Three', 'trello_board_template_id' => '672006f1b976b21096aa85c1'],
            ['name' => 'Infinity', 'description' => 'Description for Package Four', 'trello_board_template_id' => '67200700704b0514a4591cbe'],
            ['name' => 'Sapphire', 'description' => 'Description for Package Five', 'trello_board_template_id' => '67200712519e80e1e1ddf9d4'],
        ]);

        // Seed Departments
        DB::table('departments')->insert([
            ['name' => 'Catering', 'description' => 'Description for Catering Department', 'id' => 1],
            ['name' => 'Hair and Makeup', 'description' => 'Description for Hair and Makeup Department', 'id' => 2],
            ['name' => 'Photo and Video', 'description' => 'Description for Photo and Video Department', 'id' => 3],
            ['name' => 'Designing', 'description' => 'Description for Designing Department', 'id' => 4],
            ['name' => 'Entertainment', 'description' => 'Description for Entertainment Department', 'id' => 5],
            ['name' => 'Coordination', 'description' => 'Description for Coordination Department', 'id' => 6],
        ]);

        // Seed Teams
        DB::table('teams')->insert([
            // Catering Teams
            ['name' => 'Catering Team A', 'description' => 'Catering team A description', 'id' => 1],
            ['name' => 'Catering Team B', 'description' => 'Catering team B description', 'id' => 2],
            ['name' => 'Catering Team C', 'description' => 'Catering team C description', 'id' => 3],
            ['name' => 'Catering Team D', 'description' => 'Catering team D description', 'id' => 4],
            ['name' => 'Catering Team E', 'description' => 'Catering team E description', 'id' => 5],
            ['name' => 'Catering Team F', 'description' => 'Catering team F description', 'id' => 6],

            ['name' => 'Catering Management', 'description' => 'Catering Management', 'id' => 37],
            
            // Hair and Makeup Teams
            ['name' => 'Hair and Makeup Team A', 'description' => 'Hair and Makeup team A description', 'id' => 7],
            ['name' => 'Hair and Makeup Team B', 'description' => 'Hair and Makeup team B description', 'id' => 8],
            ['name' => 'Hair and Makeup Team C', 'description' => 'Hair and Makeup team C description', 'id' => 9],
            ['name' => 'Hair and Makeup Team D', 'description' => 'Hair and Makeup team D description', 'id' => 10],
            ['name' => 'Hair and Makeup Team E', 'description' => 'Hair and Makeup team E description', 'id' => 11],
            ['name' => 'Hair and Makeup Team F', 'description' => 'Hair and Makeup team F description', 'id' => 12],

            ['name' => 'Hair and Makeup Management', 'description' => 'Hair and Makeup Management', 'id' => 38],
            
            // Photo and Video Teams
            ['name' => 'Photo and Video Team A', 'description' => 'Photo and Video team A description', 'id' => 13],
            ['name' => 'Photo and Video Team B', 'description' => 'Photo and Video team B description', 'id' => 14],
            ['name' => 'Photo and Video Team C', 'description' => 'Photo and Video team C description', 'id' => 15],
            ['name' => 'Photo and Video Team D', 'description' => 'Photo and Video team D description', 'id' => 16],
            ['name' => 'Photo and Video Team E', 'description' => 'Photo and Video team E description', 'id' => 17],
            ['name' => 'Photo and Video Team F', 'description' => 'Photo and Video team F description', 'id' => 18],

            ['name' => 'Photo and Video Management', 'description' => 'Photo and Video Management', 'id' => 39],
            
            // Designing Teams
            ['name' => 'Designing Team A', 'description' => 'Designing team A description', 'id' => 19],
            ['name' => 'Designing Team B', 'description' => 'Designing team B description', 'id' => 20],
            ['name' => 'Designing Team C', 'description' => 'Designing team C description', 'id' => 21],
            ['name' => 'Designing Team D', 'description' => 'Designing team D description', 'id' => 22],
            ['name' => 'Designing Team E', 'description' => 'Designing team E description', 'id' => 23],
            ['name' => 'Designing Team F', 'description' => 'Designing team F description', 'id' => 24],

            ['name' => 'Designing Management', 'description' => 'Designing Management', 'id' => 40],
            
            // Entertainment Teams
            ['name' => 'Entertainment Team A', 'description' => 'Entertainment team A description', 'id' => 25],
            ['name' => 'Entertainment Team B', 'description' => 'Entertainment team B description', 'id' => 26],
            ['name' => 'Entertainment Team C', 'description' => 'Entertainment team C description', 'id' => 27],
            ['name' => 'Entertainment Team D', 'description' => 'Entertainment team D description', 'id' => 28],
            ['name' => 'Entertainment Team E', 'description' => 'Entertainment team E description', 'id' => 29],
            ['name' => 'Entertainment Team F', 'description' => 'Entertainment team F description', 'id' => 30],

            ['name' => 'Entertainment Management', 'description' => 'Entertainment Management', 'id' => 41],
            
            // Coordination Teams
            ['name' => 'Coordination Team A', 'description' => 'Coordination team A description', 'id' => 31],
            ['name' => 'Coordination Team B', 'description' => 'Coordination team B description', 'id' => 32],
            ['name' => 'Coordination Team C', 'description' => 'Coordination team C description', 'id' => 33],
            ['name' => 'Coordination Team D', 'description' => 'Coordination team D description', 'id' => 34],
            ['name' => 'Coordination Team E', 'description' => 'Coordination team E description', 'id' => 35],
            ['name' => 'Coordination Team F', 'description' => 'Coordination team F description', 'id' => 36],

            ['name' => 'Coordination Management', 'description' => 'Coordination Management', 'id' => 42],
        ]);

        // Pivot Table Seed for Departments and Teams
        DB::table('departments_has_teams')->insert([
            // Catering Department
            ['department_id' => 1, 'team_id' => 1],
            ['department_id' => 1, 'team_id' => 2],
            ['department_id' => 1, 'team_id' => 3],
            ['department_id' => 1, 'team_id' => 4],
            ['department_id' => 1, 'team_id' => 5],
            ['department_id' => 1, 'team_id' => 6],

            ['department_id' => 1, 'team_id' => 37],
            
            // Hair and Makeup Department
            ['department_id' => 2, 'team_id' => 7],
            ['department_id' => 2, 'team_id' => 8],
            ['department_id' => 2, 'team_id' => 9],
            ['department_id' => 2, 'team_id' => 10],
            ['department_id' => 2, 'team_id' => 11],
            ['department_id' => 2, 'team_id' => 12],

            ['department_id' => 2, 'team_id' => 38],
            
            // Photo and Video Department
            ['department_id' => 3, 'team_id' => 13],
            ['department_id' => 3, 'team_id' => 14],
            ['department_id' => 3, 'team_id' => 15],
            ['department_id' => 3, 'team_id' => 16],
            ['department_id' => 3, 'team_id' => 17],
            ['department_id' => 3, 'team_id' => 18],

            ['department_id' => 3, 'team_id' => 39],
            
            // Designing Department
            ['department_id' => 4, 'team_id' => 19],
            ['department_id' => 4, 'team_id' => 20],
            ['department_id' => 4, 'team_id' => 21],
            ['department_id' => 4, 'team_id' => 22],
            ['department_id' => 4, 'team_id' => 23],
            ['department_id' => 4, 'team_id' => 24],

            ['department_id' => 4, 'team_id' => 40],
            
            // Entertainment Department
            ['department_id' => 5, 'team_id' => 25],
            ['department_id' => 5, 'team_id' => 26],
            ['department_id' => 5, 'team_id' => 27],
            ['department_id' => 5, 'team_id' => 28],
            ['department_id' => 5, 'team_id' => 29],
            ['department_id' => 5, 'team_id' => 30],

            ['department_id' => 5, 'team_id' => 41],
            
            // Coordination Department
            ['department_id' => 6, 'team_id' => 31],
            ['department_id' => 6, 'team_id' => 32],
            ['department_id' => 6, 'team_id' => 33],
            ['department_id' => 6, 'team_id' => 34],
            ['department_id' => 6, 'team_id' => 35],
            ['department_id' => 6, 'team_id' => 36],

            ['department_id' => 6, 'team_id' => 42],
        ]);

        DB::table('task_category')->insert([
            ['name' => '1 Year to 6 Months before'],
            ['name' => '9 Months to 6 Months before'],
            ['name' => '3 Months to 1 Month before'],
            ['name' => '6 Months to 3 Months before'],
            ['name' => '3 Months before'],
            ['name' => '3 Months to 1 Month before'],
            ['name' => '1 Month before'],
            ['name' => '1 Week before and Wedding Day'],
            ['name' => 'Wedding Day'],
            ['name' => '6 Months after Wedding Day'],

        ]);


        
    }
}
