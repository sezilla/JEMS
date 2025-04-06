<?php

namespace App\Listeners;

use App\Services\ProjectService;
use App\Events\SyncTrelloBoardToDB;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncTrelloBoardToDBListener implements ShouldQueue
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
    public function handle(SyncTrelloBoardToDB $event): void
    {
        $project = $event->project;

        $this->projectService->syncTrelloToDatabase($project);
    }
}
