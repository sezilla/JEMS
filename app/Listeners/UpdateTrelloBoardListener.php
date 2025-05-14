<?php

namespace App\Listeners;

use App\Services\ProjectService;
use App\Events\UpdateProjectEvent;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateTrelloBoardListener implements ShouldQueue
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
    public function handle(UpdateProjectEvent $event): void
    {
        $project = $event->project;

        if ($project->trello_board_id) {
            try {
                $this->projectService->updateTrelloBoard($project);
                Log::info('Trello board updated for project: ' . $project->name);

                Notification::make()
                    ->title('Trello board updated for project: ' . $project->name)
                    ->success()
                    ->send();
            } catch (\Exception $e) {
                Log::error('Error updating Trello board: ' . $e->getMessage(), [
                    'project_id' => $project->id ?? null,
                    'exception' => $e,
                ]);

                if ($project->user) {
                    Notification::make()
                        ->danger()
                        ->title('Trello Board Update Failed')
                        ->body('An error occurred while updating the Trello board: ' . $e->getMessage())
                        ->sendToDatabase($project->user);
                }

                // Mark the job as failed but don't stop the queue worker
                $this->fail($e);
            }
        }
    }
}
