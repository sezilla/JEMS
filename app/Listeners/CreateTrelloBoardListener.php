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
use App\Services\ProjectProgressService;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateTrelloBoardListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $projectService;
    protected $progressService;

    public function __construct(ProjectService $projectService, ProjectProgressService $progressService)
    {
        $this->projectService = $projectService;
        $this->progressService = $progressService;
    }

    public function handle(ProjectCreatedEvent $event): void
    {
        $project = $event->project;

        $this->progressService->updateProgress(
            projectId: $project->id,
            status: 'in_progress',
            message: 'Setting up your project board...',
            progress: 30
        );

        try {
            $this->projectService->createTrelloBoardForProject($project);

            $this->progressService->updateProgress(
                projectId: $project->id,
                status: 'in_progress',
                message: 'Setting up board structure...',
                progress: 45
            );

            Notification::make()
                ->success()
                ->title('Trello Board Created')
                ->body('The Trello board has been created for your project.')
                ->sendToDatabase($project->user);

            $this->progressService->updateProgress(
                projectId: $project->id,
                status: 'completed',
                message: 'Project board created successfully',
                progress: 50
            );

            TrelloBoardCreatedEvent::dispatch($project);
        } catch (\Exception $e) {
            Log::error('Error creating Trello board: ' . $e->getMessage(), [
                'project_id' => $project->id ?? null,
                'exception' => $e,
            ]);

            // Send error progress update
            $this->progressService->updateProgress(
                projectId: $project->id,
                status: 'error',
                message: 'Failed to create Trello board: ' . $e->getMessage(),
                progress: 30,
                isCompleted: false,
                hasError: true
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
