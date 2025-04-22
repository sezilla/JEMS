<?php

namespace App\Listeners;

use App\Services\ProjectService;
use Illuminate\Support\Facades\Log;
use App\Events\DueDateAssignedEvent;
use Filament\Notifications\Notification;
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

        Notification::make()
            ->title('Tasks Assigned')
            ->body('You have been assigned to tasks for project: ' . $project->name)
            ->sendToDatabase($project->user_id);
    }
}
