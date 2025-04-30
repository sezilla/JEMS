<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\UserTask;
use App\Models\ChecklistUser;
use Barryvdh\DomPDF\Facade\Pdf;
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



    // public function filterReports(?int $status = null, ?string $start = null, ?string $end = null): Collection
    // {
    //     Log::info('Filtering project reports', [
    //         'status' => $status,
    //         'start'  => $start,
    //         'end'    => $end,
    //     ]);

    //     $query = Project::query()
    //         ->with(['package', 'headCoordinator', 'groomCoordinator', 'brideCoordinator', 'teams']);

    //     if ($status !== null) {
    //         $query->where('status', $status);
    //     }

    //     if ($start) {
    //         try {
    //             $from = Carbon::createFromFormat('Y-m', $start)->startOfMonth();
    //             $query->whereDate('end', '>=', $from);
    //         } catch (\Exception $e) {
    //             Log::error('Invalid start date format', ['start' => $start, 'error' => $e->getMessage()]);
    //         }
    //     }

    //     if ($end) {
    //         try {
    //             $until = Carbon::createFromFormat('Y-m', $end)->endOfMonth();
    //             $query->whereDate('end', '<=', $until);
    //         } catch (\Exception $e) {
    //             Log::error('Invalid end date format', ['end' => $end, 'error' => $e->getMessage()]);
    //         }
    //     }

    //     $projects = $query->get();

    //     foreach ($projects as $project) {
    //         $project->statusText = $this->getStatusText($project->status);
    //     }

    //     return $projects;
    // }

    public function getStatusText(int $statusCode): string
    {
        $statuses = [
            10  => 'Active',
            200 => 'Completed',
            100 => 'Archived',
            0   => 'Canceled',
            50  => 'On Hold',
        ];
        
        return $statuses[$statusCode] ?? 'Unknown';
    }
}
