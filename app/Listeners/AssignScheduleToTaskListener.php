<?php

namespace App\Listeners;

use App\Events\ProgressUpdated;
use App\Services\ProjectService;
use App\Services\ProgressService;
use Illuminate\Support\Facades\Log;
use App\Events\DueDateAssignedEvent;
use App\Events\TrelloBoardIsFinalEvent;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignScheduleToTaskListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $projectService;
    protected $progressService;

    /**
     * Create the event listener.
     */
    public function __construct(ProjectService $projectService, ProgressService $progressService)
    {
        $this->projectService = $projectService;
        $this->progressService = $progressService;
    }

    /**
     * Handle the event.
     */
    public function handle(TrelloBoardIsFinalEvent $event): void
    {
        $project = $event->project;
        $user = $event->project->user;

        $this->progressService->updateProgress(
            $project->id,
            35,
            'Assigning Schedules 3/4',
            'Assigning task schedules to project tasks',
        );

        try {
            Log::info('Starting assignTaskSchedules');
            $this->projectService->assignTaskSchedules($project);
            Log::info('Finished assignTaskSchedules');

            Log::info('Starting syncChecklist');
            $this->projectService->syncChecklist($project);
            Log::info('Finished syncChecklist');

            Notification::make()
                ->success()
                ->title('Task Schedules Assigned')
                ->body('The task schedules for your project have been successfully assigned.')
                ->sendToDatabase($user);

            $this->progressService->updateProgress(
                $project->id,
                60,
                'Task Schedules Assigned 3/4',
                'Task schedules assigned successfully',
            );

            // Dispatch next event in the workflow
            DueDateAssignedEvent::dispatch($project);
        } catch (\Exception $e) {
            Log::error('Error assigning task schedules: ' . $e->getMessage(), [
                'project_id' => $project->id ?? null,
                'task' => 'assignTaskSchedules or syncChecklist',
                'exception' => $e,
            ]);

            Notification::make()
                ->danger()
                ->title('Error Assigning Task Schedules')
                ->body('An error occurred while assigning task schedules: ' . $e->getMessage())
                ->sendToDatabase($user);

            // Mark the job as failed but don't stop the queue worker
            $this->fail($e);
        }
    }
}
