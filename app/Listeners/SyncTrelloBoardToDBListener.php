<?php

namespace App\Listeners;

use App\Services\ProjectService;
use App\Events\SyncTrelloBoardToDB;
use Illuminate\Support\Facades\Log;
use App\Events\TrelloBoardIsFinalEvent;
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

        try {
            $this->projectService->syncTrelloToDatabase($project);

            TrelloBoardIsFinalEvent::dispatch($project);
        } catch (\Exception $e) {
            Log::error('Error syncing Trello board to database: ' . $e->getMessage(), [
                'project_id' => $project->id ?? null,
                'exception' => $e,
            ]);
        }
    }
}
