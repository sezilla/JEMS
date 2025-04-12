<?php

namespace Database\Seeders\dev;

use App\Models\TaskCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TaskCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taskCategories = Config::get('seeder.taskCategory');

        foreach ($taskCategories as $key => $category) {
            TaskCategory::updateOrCreate(
                ['slug' => $key],
                [
                    'name' => $category['name'],
                    'description' => $category['description']
                ]

            );
        }
    }
}
