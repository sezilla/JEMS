<?php

namespace App\Listeners;

use App\Services\ProjectService;
use App\Events\DueDateAssignedEvent;
use App\Events\TrelloBoardIsFinalEvent;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignScheduleToTaskListener implements ShouldQueue
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
    public function handle(TrelloBoardIsFinalEvent $event): void
    {
        $project = $event->project;

        $user = $event->project->user;

        try {
            $this->projectService->assignTaskSchedules($project);

            Notification::make()
                ->success()
                ->title('Task Schedules Assigned')
                ->body('The task schedules for your project have been successfully assigned.')
                ->sendToDatabase($user);

            DueDateAssignedEvent::dispatch($project);
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error Assigning Task Schedules')
                ->body('An error occurred while assigning task schedules: ' . $e->getMessage())
                ->sendToDatabase($user);
        }
    }
}
