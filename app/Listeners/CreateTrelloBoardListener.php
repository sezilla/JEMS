<?php

namespace App\Listeners;

use App\Services\ProjectService;
use App\Events\ProjectCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Notifications\Notification;
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

        $this->projectService->createTrelloBoardForProject($project);

        Notification::make()
            ->title('Trello Board Created')
            ->body('A new Trello board has been created for your project.')
            ->sendToDatabase($project->user_id);
    }

}
