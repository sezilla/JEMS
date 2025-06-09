<?php

namespace App\Listeners;

use App\Events\ProgressUpdated;
use App\Traits\UpdatesProgress;
use App\Services\ProjectService;
use App\Services\ProgressService;
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
    protected $progressService;

    public function __construct(ProjectService $projectService, ProgressService $progressService)
    {
        $this->projectService = $projectService;
        $this->progressService = $progressService;
    }

    public function handle(ProjectCreatedEvent $event): void
    {
        $project = $event->project;

        $this->progressService->updateProgress(
            $project->id,
            25,
            'Creating Trello Board',
            'Setting up your project board...'
        );

        try {
            $this->projectService->createTrelloBoardForProject($project);

            $this->progressService->updateProgress(
                $project->id,
                45,
                'Creating Trello Board',
                'Setting up board structure...'
            );

            Notification::make()
                ->success()
                ->title('Trello Board Created')
                ->body('The Trello board has been created for your project.')
                ->sendToDatabase($project->user);

            $this->progressService->updateProgress(
                $project->id,
                50,
                'Trello Board Created',
                'Project board created successfully'
            );

            TrelloBoardCreatedEvent::dispatch($project);
        } catch (\Exception $e) {
            Log::error('Error creating Trello board: ' . $e->getMessage(), [
                'project_id' => $project->id ?? null,
                'exception' => $e,
            ]);

            // Send error progress update
            $this->progressService->updateProgress(
                $project->id,
                -2,
                'Error',
                'Failed to create Trello board: ' . $e->getMessage()
            );

            Notification::make()
                ->danger()
                ->title('Trello Board Creation Failed')
                ->body('An error occurred while creating the Trello board: ' . $e->getMessage())
                ->sendToDatabase($project->user);

            $this->fail($e);
        }
    }
}
