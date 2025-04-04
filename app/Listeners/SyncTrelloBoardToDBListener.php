<?php

namespace App\Listeners;

use App\Services\ProjectService;
use App\Events\SyncTrelloBoardToDB;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncTrelloBoardToDBListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected ProjectService $projectService;
    protected string $boardId;
    /**
     * Create the event listener.
     */
    public function __construct(ProjectService $projectService, string $boardId)
    {
        $this->projectService = $projectService;
        $this->boardId = $boardId;
    }

    /**
     * Handle the event.
     */
    public function handle(SyncTrelloBoardToDB $event): void
    {
        $this->projectService->syncTrelloToDatabase($event->boardId);
    }
}
