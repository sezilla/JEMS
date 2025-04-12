<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\dev\DummyUserSeeder;
use Database\Seeders\dev\TestProjectSeeder;

class FakeUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            // DummyUserSeeder::class,
            TestProjectSeeder::class,
        ]);
    }
}
