<?php

namespace App\Listeners;

use App\Events\ProgressUpdated;
use App\Traits\UpdatesProgress;
use App\Services\ProjectService;
use App\Traits\BroadcastsProgress;
use App\Events\ProjectCreatedEvent;
use Illuminate\Support\Facades\Log;
use App\Events\TrelloBoardCreatedEvent;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateTrelloBoardListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function handle(ProjectCreatedEvent $event): void
    {
        $project = $event->project;

        event(new ProgressUpdated(
            25,
            'Creating board',
            'Creating Trello board for event',
            $project->id,
            $project->user_id
        ));

        try {
            $this->projectService->createTrelloBoardForProject($project);

            Notification::make()
                ->success()
                ->title('Trello Board Created')
                ->body('The Trello board has been created for your project.')
                ->sendToDatabase($project->user);

            // Mark as completed
            event(new ProgressUpdated(
                50,
                'Completed',
                'Trello board created successfully',
                $project->id,
                $project->user_id
            ));

            TrelloBoardCreatedEvent::dispatch($project);
        } catch (\Exception $e) {
            Log::error('Error creating Trello board: ' . $e->getMessage(), [
                'project_id' => $project->id ?? null,
                'exception' => $e,
            ]);

            // Send error progress update
            event(new ProgressUpdated(
                -2,
                'Error',
                'Failed to create Trello board: ' . $e->getMessage(),
                $project->id,
                $project->user_id
            ));

            Notification::make()
                ->danger()
                ->title('Trello Board Creation Failed')
                ->body('An error occurred while creating the Trello board: ' . $e->getMessage())
                ->sendToDatabase($project->user);

            $this->fail($e);
        }
    }
}
