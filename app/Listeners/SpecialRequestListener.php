<?php

namespace App\Listeners;

use App\Models\Project;
use App\Services\ProjectService;
use App\Events\SyncTrelloBoardToDB;
use App\Events\TrelloBoardCreatedEvent;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SpecialRequestListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $projectService;

    /**
     * Create the event listener.
     */
    public function __construct(ProjectService $projectService,)
    {
        $this->projectService = $projectService;
    }

    /**
     * Handle the event.
     */
    public function handle(TrelloBoardCreatedEvent $event): void
    {
        $project = $event->project;

        $user = $event->project->user;

        try {
            if (!empty($project->special_request)) {
                $this->projectService->createSpecialRequest($project);

                Notification::make()
                    ->success()
                    ->title('Special Request Allocated')
                    ->body('A special request has been created and allocated to designated Department.')
                    ->sendToDatabase($user);
            }
        } catch (\Exception $e) {
            Notification::make()
                ->error()
                ->title('Special Request Failed')
                ->body('Failed to create a special request for your project. Please try again later: ' . $e->getMessage())
                ->sendToDatabase($user);
        }
        SyncTrelloBoardToDB::dispatch($project);
    }
}
