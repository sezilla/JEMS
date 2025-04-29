<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\UserTask;
use App\Models\ChecklistUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class DashboardService
{
    protected $project;
    protected $userTask;

    public function __construct(
        Project $project,
        UserTask $userTask
    ) {
        $this->project = $project;
        $this->userTask = $userTask;
    }

    public function getProjectCount()
    {
        return Auth::user()->teams->sum(function ($team) {
            return $team->projects->count();
        });
    }

    public function getAssignedTasksCount()
    {
        return Auth::user()->tasks->count();
    }

    public function getOngoingTasksCount()
    {
        return Auth::user()->tasks->where('status', 'incomplete')->count();
    }

    public function getFinishedTasksCount()
    {
        return Auth::user()->tasks->where('status', 'complete')->count();
    }

}
