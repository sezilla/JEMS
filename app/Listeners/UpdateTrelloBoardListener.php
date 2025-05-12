<?php

namespace App\Listeners;

use App\Services\ProjectService;
use App\Events\UpdateProjectEvent;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateTrelloBoardListener
{
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
    public function handle(UpdateProjectEvent $event): void
    {
        $project = $event->project;

        if ($project->trello_board_id) {
            $this->projectService->updateTrelloBoard($project);
            Log::info('Trello board updated for project: ' . $project->name);

            Notification::make()
                ->title('Trello board updated for project: ' . $project->name)
                ->success()
                ->send();
        }
    }
}
