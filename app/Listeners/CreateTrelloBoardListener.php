<?php

namespace App\Listeners;

use App\Services\ProjectService;
use App\Events\ProjectCreatedEvent;
use App\Events\TrelloBoardCreatedEvent;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateTrelloBoardListener implements ShouldQueue
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
    public function handle(ProjectCreatedEvent $event): void
    {
        $project = $event->project;

        try {
            $this->projectService->createTrelloBoardForProject($project);

            Notification::make()
                ->success()
                ->title('Trello Board Created')
                ->body('The Trello board has been created for your project.')
                ->sendToDatabase($project->user);

            TrelloBoardCreatedEvent::dispatch($project);
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Trello Board Creation Failed')
                ->body('An error occurred while creating the Trello board: ' . $e->getMessage())
                ->sendToDatabase($project->user);

            $project->forceDelete();
        }
    }
}
