<?php

namespace App\Listeners;

use App\Events\ProgressUpdated;
use App\Traits\UpdatesProgress;
use App\Services\ProjectService;
use App\Services\ProgressService;
use App\Traits\BroadcastsProgress;
use Illuminate\Support\Facades\Log;
use App\Events\DueDateAssignedEvent;
use App\Services\ProjectProgressService;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignUserToTasksListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $projectService;
    protected $progressService;

    public function __construct(ProjectService $projectService, ProjectProgressService $progressService)
    {
        $this->projectService = $projectService;
        $this->progressService = $progressService;
    }

    public function handle(DueDateAssignedEvent $event): void
    {
        $project = $event->project;
        $user = $event->project->user;

        $this->progressService->updateProgress(
            projectId: $project->id,
            status: 'in_progress',
            message: 'Assigning tasks to users for event',
            progress: 85
        );

        try {
            $this->projectService->allocateUser($project);
            Log::info('User assigned to tasks for project: ' . $project->id);

            Notification::make()
                ->success()
                ->title('Tasks Assigned to Users')
                ->body('Successfully assigned Users to Tasks for project: ' . $project->name)
                ->sendToDatabase($user);

            // Mark as completed
            $this->progressService->updateProgress(
                projectId: $project->id,
                status: 'completed',
                message: 'All tasks have been successfully assigned to users.',
                progress: 100,
                isCompleted: true
            );
        } catch (\Exception $e) {
            Log::error('Error assigning user to tasks for project: ' . $project->id . '. Error: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'exception' => $e,
            ]);

            $this->progressService->updateProgress(
                projectId: $project->id,
                status: 'error',
                message: 'Failed to assign tasks: ' . $e->getMessage(),
                progress: 85,
                isCompleted: false,
                hasError: true
            );

            Notification::make()
                ->danger()
                ->title('Task Assignment Failed')
                ->body('An error occurred while assigning tasks for project: ' . $project->name)
                ->sendToDatabase($user);

            $this->fail($e);
        }
    }
}
