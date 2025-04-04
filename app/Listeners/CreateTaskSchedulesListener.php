<?php

namespace App\Listeners;

use App\Events\AssignTaskSchedules;
use App\Services\ProjectService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateTaskSchedulesListener implements ShouldQueue
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
    public function handle(AssignTaskSchedules $event): void
    {
        $project = $event->project;

        $this->projectService->createTaskSchedules($project);
    }
}
