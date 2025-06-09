<?php

namespace App\Listeners;

use Laravel\Prompts\Progress;
use App\Events\ProgressUpdated;
use App\Services\ProjectService;
use App\Services\ProgressService;
use App\Traits\BroadcastsProgress;
use App\Events\AssignTaskSchedules;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateTaskSchedulesListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $projectService;
    protected $progressService;

    public function __construct(ProjectService $projectService, ProgressService $progressService)
    {
        $this->projectService = $projectService;
        $this->progressService = $progressService;
    }

    public function handle(AssignTaskSchedules $event): void
    {
        $project = $event->project;

        $this->progressService->updateProgress(
            $project->id,
            50,
            'Assigning Schedules',
            'Assigning task schedules to project tasks',
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
                $project->id,
                75,
                'Completed',
                'Task schedules assigned successfully',
            );
        } catch (\Exception $e) {
            Log::error('Error assigning task schedules: ' . $e->getMessage(), [
                'project_id' => $project->id ?? null,
                'exception' => $e,
            ]);

            // Send error progress update
            $this->progressService->updateProgress(
                $project->id,
                -2,
                'Error',
                'Failed to assign task schedules: ' . $e->getMessage(),
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
