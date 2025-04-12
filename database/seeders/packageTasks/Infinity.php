<?php

namespace Database\Seeders\packageTasks;

use App\Models\Task;
use App\Models\PackageTask;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Infinity extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packageId = config('seeder.package.infinity.id');

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
            'prenup-hair-and-makeup',
            'bride-airbrush-makeup',
            'groom-traditional-makeup',
            'entourage-makeup',
            'pre-ceremony-shoot-makeup',

            //photography and videography
            'theme-discussion-for-video-album',
            'prenuptial-pictorial-session',
            'confirm-shot-list',
            'design-photo-frame',
            'setup-photo-booth',
            'setup-drone',
            'setup-projector-screen',
            'same-day-edit-video',
            'same-day-photo-album-edit',
            'raw-photo-transfer',
            'final-file-transfer',

            //design
            'plan-overall-theme',
            'design-invitation',
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
