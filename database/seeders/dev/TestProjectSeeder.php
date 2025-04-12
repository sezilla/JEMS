<?php

namespace Database\Seeders\dev;

use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TestProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::updateOrCreate(
            [
                'name' => 'Test Project 1',
                'description' => 'This is a test project.',
                'venue' => 'Test Venue',
                'groom_name' => 'John Doe',
                'bride_name' => 'Jane Doe',
                'theme_color' => '#FF5733',
                'special_request' => 'could we arrange for a vegetarian meal option and ensure seating near the dance floor?',
                'start' => now(),
                'end' => now()->addDays(200),
            ],
            [
                'user_id' => User::role('HR Admin')->inRandomOrder()->first()?->id,
                'package_id' => config('seeder.package.ruby.id'),
                'groom_coordinator' => User::role('Coordinator')->inRandomOrder()->first()?->id,
                'bride_coordinator' => User::role('Coordinator')->inRandomOrder()->first()?->id,
                'head_coordinator' => User::role('Coordinator')->inRandomOrder()->first()?->id,
            ]
        );

        Project::updateOrCreate(
            [
                'name' => 'Test Project 2',
                'description' => 'This is a test project.',
                'venue' => 'Test Venue',
                'groom_name' => fake()->name(),
                'bride_name' => fake()->name(),
                'theme_color' => '#FF5733',
                'special_request' => 'Could we have a live band performance and a photo booth setup?',
                'start' => now(),
                'end' => now()->addDays(200),
            ],
            [
                'user_id' => User::role('HR Admin')->inRandomOrder()->first()?->id,
                'package_id' => config('seeder.package.sapphire.id'),
                'groom_coordinator' => User::role('Coordinator')->inRandomOrder()->first()?->id,
                'bride_coordinator' => User::role('Coordinator')->inRandomOrder()->first()?->id,
                'head_coordinator' => User::role('Coordinator')->inRandomOrder()->first()?->id,
            ]
        );

        Project::updateOrCreate(
            [
                'name' => 'Test Project 3',
                'description' => 'This is a test project.',
                'venue' => 'Test Venue',
                'groom_name' => fake()->name(),
                'bride_name' => fake()->name(),
                'theme_color' => '#FF5733',
                'special_request' => 'Could we have a live band performance and a photo booth setup?',
                'start' => now(),
                'end' => now()->addDays(200),
            ],
            [
                'user_id' => User::role('HR Admin')->inRandomOrder()->first()?->id,
                'package_id' => config('seeder.package.emerald.id'),
                'groom_coordinator' => User::role('Coordinator')->inRandomOrder()->first()?->id,
                'bride_coordinator' => User::role('Coordinator')->inRandomOrder()->first()?->id,
                'head_coordinator' => User::role('Coordinator')->inRandomOrder()->first()?->id,
            ]
        );
    }
}
