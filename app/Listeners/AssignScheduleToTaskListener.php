<?php

namespace App\Listeners;

use App\Events\TrelloBoardIsFinalEvent;
use App\Services\ProjectService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignScheduleToTaskListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $projectService;

    /**
     * Create the event listener.
     */
    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Handle the event.
     */
    public function handle(TrelloBoardIsFinalEvent $event): void
    {
        $project = $event->project;

        $this->projectService->assignTaskSchedules($project);
    }
}
