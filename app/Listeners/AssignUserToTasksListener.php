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
        $user = $event->project->user;

        try {
            $this->projectService->allocateUser($project);
            Log::info('User assigned to tasks for project: ' . $project->id);

            Notification::make()
                ->success()
                ->title('Tasks Assigned to Users')
                ->body('Successfully assigned Users to Tasks for project: ' . $project->name)
                ->sendToDatabase($user);
        } catch (\Exception $e) {
            Log::error('Error assigning user to tasks for project: ' . $project->id . '. Error: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'exception' => $e,
            ]);

            Notification::make()
                ->danger()
                ->title('Task Assignment Failed')
                ->body('An error occurred while assigning tasks for project: ' . $project->name)
                ->sendToDatabase($user);

            // Mark the job as failed but don't stop the queue worker
            $this->fail($e);
        }
    }
}
