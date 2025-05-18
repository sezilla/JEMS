<?php

namespace App\Jobs;

use App\Services\ProjectTaskService;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckTaskDue implements ShouldQueue
{
    use Queueable;

    protected ProjectTaskService $projectTaskService;

    /**
     * Create a new job instance.
     */
    public function __construct(ProjectTaskService $projectTaskService)
    {
        $this->projectTaskService = $projectTaskService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->projectTaskService->checkTaskIfDueTomorrow();
    }
}
