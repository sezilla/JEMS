<?php

namespace App\Listeners;

use App\Events\ProgressUpdated;
use App\Traits\UpdatesProgress;
use App\Services\ProjectService;
use App\Services\ProgressService;
use App\Traits\BroadcastsProgress;
use Illuminate\Support\Facades\Log;
use App\Events\DueDateAssignedEvent;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignUserToTasksListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $projectService;
    protected $progressService;

    public function __construct(ProjectService $projectService, ProgressService $progressService)
    {
        $this->projectService = $projectService;
        $this->progressService = $progressService;
    }

    public function handle(DueDateAssignedEvent $event): void
    {
        $project = $event->project;
        $user = $event->project->user;

        $this->progressService->updateProgress(
            $project->id,
            70,
            'Assigning Tasks to Users 4/4',
            'Assigning task to users for event'
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
                $project->id,
                100,
                'Completed',
                'All tasks have been successfully assigned to users.'
            );
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

            $this->fail($e);
        }
    }
}
