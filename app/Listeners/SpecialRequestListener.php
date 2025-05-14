<?php

namespace App\Listeners;

use App\Models\Project;
use App\Services\ProjectService;
use App\Events\SyncTrelloBoardToDB;
use App\Events\TrelloBoardCreatedEvent;
use Illuminate\Support\Facades\Log;
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

            // Dispatch next event in the chain regardless of whether there was a special request
            SyncTrelloBoardToDB::dispatch($project);
        } catch (\Exception $e) {
            Log::error('Error creating special request: ' . $e->getMessage(), [
                'project_id' => $project->id ?? null,
                'special_request' => $project->special_request ?? null,
                'exception' => $e,
            ]);

            Notification::make()
                ->danger()
                ->title('Special Request Failed')
                ->body('Failed to create a special request for your project. Please try again later: ' . $e->getMessage())
                ->sendToDatabase($user);

            // Still dispatch the next event in the chain to continue the workflow
            SyncTrelloBoardToDB::dispatch($project);

            // Mark this job as failed but continue the queue
            $this->fail($e);
        }
    }
}
