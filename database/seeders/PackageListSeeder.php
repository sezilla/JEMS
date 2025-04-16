<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\dev\PackageTask;
use Database\Seeders\dev\PackageSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PackageListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PackageSeeder::class,
            PackageTask::class,
        ]);
    }
}
