<?php

namespace App\Listeners;

use App\Events\ProjectCreationFailed;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Filament\Actions\Action;

class ProjectFailed implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProjectCreationFailed $event): void
    {
        $project = $event->project;
        $user = $project->user;

        try {
            Notification::make()
                ->danger()
                ->title('Project Creation Failed')
                ->body('The project "' . $project->name . '" could not be created and has been deleted.')
                ->actions([
                    Action::make('view_projects')
                        ->label('View Projects')
                        ->url(route('filament.admin.resources.projects.index'))
                        ->button()
                ])
                ->sendToDatabase($user);

            Log::info('Project failed and deleted', [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'user_id' => $user->id
            ]);

            $project->forceDelete();
        } catch (\Exception $e) {
            Log::error('Error handling project failure', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
                'exception' => $e
            ]);

            Notification::make()
                ->danger()
                ->title('Error')
                ->body('An error occurred while handling the failed project.')
                ->actions([
                    Action::make('view_projects')
                        ->label('Go back to Projects')
                        ->url(route('filament.admin.resources.projects.index'))
                        ->button()
                ])
                ->sendToDatabase($user);

            // Mark the job as failed but don't stop the queue worker
            $this->fail($e);
        }
    }
}
