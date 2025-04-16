<?php

namespace App\Listeners;

use App\Services\ProjectService;
use Illuminate\Support\Facades\Log;
use App\Events\DueDateAssignedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignUserToTasksListener implements ShouldQueue
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
    public function handle(DueDateAssignedEvent $event): void
    {
        $project = $event->project;

        $this->projectService->allocateUserToTask($project);
        Log::info('User assigned to tasks for project: ' . $project->id);

        // Notification::make()
        //     ->title('Task schedules assigned successfully!')
        //     ->success()
        //     ->send();
    }
}
