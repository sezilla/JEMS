<?php

namespace Database\Seeders\dev;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\departmentTask\Catering;
use Database\Seeders\departmentTask\Designing;
use Database\Seeders\departmentTask\Coordination;
use Database\Seeders\departmentTask\Entertainment;
use Database\Seeders\departmentTask\HairAndMakeup;
use Database\Seeders\departmentTask\PhotoAndVideo;

class DepartmentSkill extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            Catering::class,
            HairAndMakeup::class,
            PhotoAndVideo::class,
            Designing::class,
            Entertainment::class,
            Coordination::class,
        ]);
    }
}
