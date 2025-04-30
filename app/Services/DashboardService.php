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
    protected $trelloService;

    public function __construct(
        Project $project,
        UserTask $userTask,
        TrelloService $TrelloService
    ) {
        $this->project = $project;
        $this->userTask = $userTask;
        $this->trelloService = $TrelloService;
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

    public function getProjectTasksCount($projectId)
    {
        $project = $this->project->find($projectId);
        $response = $this->trelloService->getCheckItemCount($project->trello_board_id);

        return $response['checkItemCount'] ?? 0;
    }

//     public function getTaskStatusBreakdown($projectId = null)
// {
//     $query = Auth::user()->tasks(); // returns query builder

//     if ($projectId) {
//         $query->where('project_id', $projectId);
//     }

//     $tasks = $query->get();

//     $ongoing = $tasks->where('status', 'incomplete')->count();
//     $finished = $tasks->where('status', 'complete')->count();

//     // Optional: Count assigned tasks (ongoing only)
//     $assignedOngoing = $tasks->where('status', 'incomplete')->where('assigned_to', Auth::id())->count();

//     return [
//         'ongoing' => $ongoing,
//         'finished' => $finished,
//         'assigned' => $assignedOngoing,
//         'total' => $ongoing + $finished,
//     ];
// }

    
    
}
