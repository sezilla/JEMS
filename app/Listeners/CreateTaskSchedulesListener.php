<?php

namespace App\Listeners;

use Laravel\Prompts\Progress;
use App\Events\ProgressUpdated;
use App\Services\ProjectService;
use App\Services\ProgressService;
use App\Traits\BroadcastsProgress;
use App\Events\AssignTaskSchedules;
use Illuminate\Support\Facades\Log;
use App\Services\ProjectProgressService;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateTaskSchedulesListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $projectService;
    protected $progressService;

    public function __construct(ProjectService $projectService, ProjectProgressService $progressService)
    {
        $this->projectService = $projectService;
        $this->progressService = $progressService;
    }

    public function handle(AssignTaskSchedules $event): void
    {
        $project = $event->project;

        $this->progressService->updateProgress(
            projectId: $project->id,
            status: 'in_progress',
            message: 'Assigning task schedules to project tasks',
            progress: 60
        );

        try {
            $this->projectService->assignTaskSchedules($project);

            Notification::make()
                ->success()
                ->title('Task Schedules Assigned')
                ->body('Task schedules have been successfully assigned to your project.')
                ->sendToDatabase($project->user);

            // Mark as completed
            $this->progressService->updateProgress(
                projectId: $project->id,
                status: 'completed',
                message: 'Task schedules assigned successfully',
                progress: 75
            );
        } catch (\Exception $e) {
            Log::error('Error assigning task schedules: ' . $e->getMessage(), [
                'project_id' => $project->id ?? null,
                'exception' => $e,
            ]);

            // Send error progress update
            $this->progressService->updateProgress(
                projectId: $project->id,
                status: 'error',
                message: 'Failed to assign task schedules: ' . $e->getMessage(),
                progress: 60,
                isCompleted: false,
                hasError: true
            );

            Notification::make()
                ->danger()
                ->title('Error Assigning Task Schedules')
                ->body('An error occurred while assigning task schedules: ' . $e->getMessage())
                ->sendToDatabase($project->user);

            $this->fail($e);
        }
    }
}
