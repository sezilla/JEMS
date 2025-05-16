<?php

namespace App\Services;

use App\Models\Project;
use App\Models\UserTask;
use App\Services\TrelloTask;

class ProjectTaskService
{
    private Project $project;
    private TrelloTask $trelloTask;
    private UserTask $userTask;

    public function __construct(
        Project $project,
        TrelloTask $trelloTask,
        UserTask $userTask
    ) {
        $this->project = $project;
        $this->trelloTask = $trelloTask;
        $this->userTask = $userTask;
    }
}
