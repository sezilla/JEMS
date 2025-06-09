<?php

namespace App\Listeners;

use App\Events\ProgressUpdated;
use App\Services\ProjectService;
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

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function handle(AssignTaskSchedules $event): void
    {
        $project = $event->project;

        event(new ProgressUpdated(
            50,
            'Assigning Schedules',
            'Assigning task schedules to project tasks',
            $project->id,
            $project->user_id
        ));

        try {
            $this->projectService->assignTaskSchedules($project);

            Notification::make()
                ->success()
                ->title('Task Schedules Assigned')
                ->body('Task schedules have been successfully assigned to your project.')
                ->sendToDatabase($project->user);

            // Mark as completed
            event(new ProgressUpdated(
                75,
                'Completed',
                'Task schedules assigned successfully',
                $project->id,
                $project->user_id
            ));
        } catch (\Exception $e) {
            Log::error('Error assigning task schedules: ' . $e->getMessage(), [
                'project_id' => $project->id ?? null,
                'exception' => $e,
            ]);

            // Send error progress update
            event(new ProgressUpdated(
                -2,
                'Error',
                'Failed to assign task schedules: ' . $e->getMessage(),
                $project->id,
                $project->user_id
            ));

            Notification::make()
                ->danger()
                ->title('Error Assigning Task Schedules')
                ->body('An error occurred while assigning task schedules: ' . $e->getMessage())
                ->sendToDatabase($project->user);

            $this->fail($e);
        }
    }
}
