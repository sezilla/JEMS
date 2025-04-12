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
        // catering
        Team::updateOrCreate([
            'name' => 'Catering team A',
            'description' => 'Catering team A description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.catering.id'));
        Team::updateOrCreate([
            'name' => 'Catering team B',
            'description' => 'Catering team B description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.catering.id'));
        Team::updateOrCreate([
            'name' => 'Catering team C',
            'description' => 'Catering team C description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.catering.id'));
        Team::updateOrCreate([
            'name' => 'Catering team D',
            'description' => 'Catering team D description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.catering.id'));
        Team::updateOrCreate([
            'name' => 'Catering team E',
            'description' => 'Catering team E description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.catering.id'));
        Team::updateOrCreate([
            'name' => 'Catering team F',
            'description' => 'Catering team F description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.catering.id'));

        // hair and makeup
        Team::updateOrCreate([
            'name' => 'Hair and Makeup team A',
            'description' => 'Hair and Makeup team A description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.hair_and_makeup.id'));
        Team::updateOrCreate([
            'name' => 'Hair and Makeup team B',
            'description' => 'Hair and Makeup team B description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.hair_and_makeup.id'));
        Team::updateOrCreate([
            'name' => 'Hair and Makeup team C',
            'description' => 'Hair and Makeup team C description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.hair_and_makeup.id'));
        Team::updateOrCreate([
            'name' => 'Hair and Makeup team D',
            'description' => 'Hair and Makeup team D description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.hair_and_makeup.id'));
        Team::updateOrCreate([
            'name' => 'Hair and Makeup team E',
            'description' => 'Hair and Makeup team E description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.hair_and_makeup.id'));
        Team::updateOrCreate([
            'name' => 'Hair and Makeup team F',
            'description' => 'Hair and Makeup team F description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.hair_and_makeup.id'));

        //photo and video
        Team::updateOrCreate([
            'name' => 'Photo and Video team A',
            'description' => 'Photography team A description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.photo_and_video.id'));
        Team::updateOrCreate([
            'name' => 'Photo and Video team B',
            'description' => 'Photography team B description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.photo_and_video.id'));
        Team::updateOrCreate([
            'name' => 'Photo and Video team C',
            'description' => 'Photography team C description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.photo_and_video.id'));
        Team::updateOrCreate([
            'name' => 'Photo and Video team D',
            'description' => 'Photography team D description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.photo_and_video.id'));
        Team::updateOrCreate([
            'name' => 'Photo and Video team E',
            'description' => 'Photography team E description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.photo_and_video.id'));
        Team::updateOrCreate([
            'name' => 'Photo and Video team F',
            'description' => 'Photography team F description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.photo_and_video.id'));

        //designing
        Team::updateOrCreate([
            'name' => 'Designing team A',
            'description' => 'Designing team A description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.designing.id'));
        Team::updateOrCreate([
            'name' => 'Designing team B',
            'description' => 'Designing team B description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.designing.id'));
        Team::updateOrCreate([
            'name' => 'Designing team C',
            'description' => 'Designing team C description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.designing.id'));
        Team::updateOrCreate([
            'name' => 'Designing team D',
            'description' => 'Designing team D description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.designing.id'));
        Team::updateOrCreate([
            'name' => 'Designing team E',
            'description' => 'Designing team E description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.designing.id'));
        Team::updateOrCreate([
            'name' => 'Designing team F',
            'description' => 'Designing team F description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.designing.id'));

        //entertainment
        Team::updateOrCreate([
            'name' => 'Entertainment team A',
            'description' => 'Entertainment team A description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.entertainment.id'));
        Team::updateOrCreate([
            'name' => 'Entertainment team B',
            'description' => 'Entertainment team B description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.entertainment.id'));
        Team::updateOrCreate([
            'name' => 'Entertainment team C',
            'description' => 'Entertainment team C description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.entertainment.id'));
        Team::updateOrCreate([
            'name' => 'Entertainment team D',
            'description' => 'Entertainment team D description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.entertainment.id'));
        Team::updateOrCreate([
            'name' => 'Entertainment team E',
            'description' => 'Entertainment team E description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.entertainment.id'));
        Team::updateOrCreate([
            'name' => 'Entertainment team F',
            'description' => 'Entertainment team F description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.entertainment.id'));

        //coordination
        Team::updateOrCreate([
            'name' => 'Coordination team A',
            'description' => 'Coordination team A description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.coordination.id'));
        Team::updateOrCreate([
            'name' => 'Coordination team B',
            'description' => 'Coordination team B description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.coordination.id'));
        Team::updateOrCreate([
            'name' => 'Coordination team C',
            'description' => 'Coordination team C description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.coordination.id'));
        Team::updateOrCreate([
            'name' => 'Coordination team D',
            'description' => 'Coordination team D description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.coordination.id'));
        Team::updateOrCreate([
            'name' => 'Coordination team E',
            'description' => 'Coordination team E description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.coordination.id'));
        Team::updateOrCreate([
            'name' => 'Coordination team F',
            'description' => 'Coordination team F description',
        ])->departments()->syncWithoutDetaching(config('seeder.department.coordination.id'));
    }
}
