<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use App\Models\ChecklistUser;
use Illuminate\Support\Facades\Auth;


class DashboardService
{
    protected $project;

    public function __construct(
        Project $project
    ) {
        $this->project = $project;
    }

    public function getProjectCount()
    {
        return Auth::user()->teams->sum(function ($team) {
            return $team->projects->count();
        });
    }

    public function getAssignedTasksCount($userId)
    {
        $count = 0;

        $checklists = ChecklistUser::whereHas('user', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->get();

        foreach ($checklists as $checklist) {
            $count += $checklist->countUserTasks($userId);
        }

        return $count;
    }



    // public function getOngoingTasksCount($userId)
    // {
    //     return Task::where('assigned_user_id', $userId)->where('status', 'ongoing')->count();
    // }

    // public function getFinishedTasksCount($userId)
    // {
    //     return Task::where('assigned_user_id', $userId)->where('status', 'finished')->count();
    // }
}
