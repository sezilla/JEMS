<?php

namespace Database\Seeders\dev;

use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = Config::get('seeder.package');

        foreach ($packages as $key => $data) {
            Package::updateOrCreate(
                ['name' => $data['name']],
                ['description' => $data['description']]
            );
        }
    }
}
