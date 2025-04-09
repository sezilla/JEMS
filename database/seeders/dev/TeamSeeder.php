<?php

namespace Database\Seeders\dev;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Team::create([
            'name' => 'Team A',
            'description' => 'Description for Team A',
        ]);
    }
}
