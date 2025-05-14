<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\ProjectService;
use App\Events\SyncTrelloBoardToDB;
use Illuminate\Support\Facades\Log;
use App\Events\TrelloBoardIsFinalEvent;
use Filament\Notifications\Notification;
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

            $this->projectService->syncChecklist($project);

            TrelloBoardIsFinalEvent::dispatch($project);
        } catch (\Exception $e) {
            Log::error('Error syncing Trello board to database: ' . $e->getMessage(), [
                'project_id' => $project->id ?? null,
                'exception' => $e,
            ]);

            if ($project->user) {
                Notification::make()
                    ->danger()
                    ->title('Trello Board Sync Failed')
                    ->body('An error occurred while syncing the Trello board: ' . $e->getMessage())
                    ->sendToDatabase($project->user);
            }

            // Mark the job as failed but don't stop the queue worker
            $this->fail($e);
        }
    }
}
