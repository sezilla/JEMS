<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\dev\DummyUserSeeder;

class FakeUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            DummyUserSeeder::class,
        ]);
    }
}
