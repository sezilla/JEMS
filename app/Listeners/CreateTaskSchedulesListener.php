<?php

namespace App\Listeners;

use App\Services\ProjectService;
use App\Events\AssignTaskSchedules;
use Filament\Notifications\Notification;
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

        try {
            $this->projectService->assignTaskSchedules($project);

            Notification::make()
                ->success()
                ->title('Task Schedules Assigned')
                ->body('Task schedules have been successfully assigned to your project.')
                ->sendToDatabase($project->user);
        } catch (\Exception $e) {
            Notification::make()
                ->error()
                ->title('Error Assigning Task Schedules')
                ->body('An error occurred while assigning task schedules: ' . $e->getMessage())
                ->sendToDatabase($project->user);
        }
    }
}
