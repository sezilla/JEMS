<?php

namespace Database\Seeders\dev;

use Illuminate\Database\Seeder;
use Database\Seeders\packageTasks\Ruby;
use Database\Seeders\packageTasks\Emerald;
use Database\Seeders\packageTasks\Garnet;
use Database\Seeders\packageTasks\Infinity;
use Database\Seeders\packageTasks\Sapphire;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PackageTask extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            Ruby::class,
            Garnet::class,
            Emerald::class,
            Infinity::class,
            Sapphire::class,
        ]);
    }
}
