<?php

namespace App\Listeners;

use App\Models\Project;
use App\Services\ProjectService;
use App\Events\TrelloBoardCreatedEvent;
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

        $this->projectService->createSpecialRequest($project);
    }
}
