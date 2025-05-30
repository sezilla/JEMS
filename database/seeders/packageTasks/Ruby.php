<?php

namespace Database\Seeders\packageTasks;

use App\Models\Task;
use App\Models\PackageTask;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Ruby extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packageId = config('seeder.package.ruby.id');

        $tasks = [
            //coordination
            'initial-meeting',
            'confirm-availability',
            'task-distribution',
            'memo-distribution',
            'monitor-departments',
            'finalize-event-flow',
            'oversee-final-prep',
            'final-check-ins',
            'day-of-oversight',

            //catering
            'food-planning-meeting',
            'menu-planning',
            'food-tasting',
            'coordinate-venue-logistics',
            'table-and-chair-setup-plan',
            'finalize-setup-plan',
            'buffet-setup',
            'reception-design-setup',
            'service-cleanup',

            //hair and makeup
            'initial-makeup-consultation',
            'theme-meeting-with-couple',
            'bride-airbrush-makeup',
            'entourage-makeup',
            'pre-ceremony-shoot-makeup',

            //design
            'plan-overall-theme',
            'aisle-walkway-setup',
            'coordinate-reception-design',
            'entourage-flowers',
            'garden-forestry-design',
            'setup-led-wall',
            'early-decorative-setup',
            'final-touch-ups',

            //entertainment
            'reception-tone-meeting',
            'plan-reception-activities',
            'check-sound-and-lights',
            'music-preference-discussion',
            'setup-lights-and-sound',
            'deploy-band',
            'coordinate-with-emcee',
            'setup-reception-activities',
        ];

        foreach ($tasks as $task) {
            PackageTask::updateOrCreate([
                'package_id' => $packageId,
                'task_id' => Task::where('slug', $task)->value('id')
            ]);
        }
    }
}
