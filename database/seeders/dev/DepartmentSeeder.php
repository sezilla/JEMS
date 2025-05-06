<?php

namespace Database\Seeders\dev;

use Illuminate\Database\Seeder;
use App\Models\Department;
use Illuminate\Support\Facades\Config;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Config::get('seeder.department');

        foreach ($departments as $key => $data) {
            Department::updateOrCreate(

                ['slug' => $key],
                [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'description' => $data['description'],
                ]
            );
        }
    }
}
