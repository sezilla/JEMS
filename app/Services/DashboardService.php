<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\UserTask;
use App\Models\ChecklistUser;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ProjectService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class DashboardService
{
    protected $project;
    protected $userTask;
    protected $trelloService;
    protected $projectService;

    public function __construct(
        Project $project,
        UserTask $userTask,
        TrelloService $TrelloService,
        ProjectService $projectService
    ) {
        $this->project = $project;
        $this->userTask = $userTask;
        $this->trelloService = $TrelloService;
        $this->projectService = $projectService;
    }

    public function getProjectCount()
    {
        $team = Auth::user()->teams; // This is a Team model or null
        return $team ? $team->projects->count() : 0;
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
        // $response = $this->trelloService->getCheckItemCount($project->trello_board_id);
        $response = $this->userTask->where('project_id', $projectId)->where('card_name', '=', Auth::user()->departments->first()?->name)->count();

        return $response['checkItemCount'] ?? 0;
    }

    public function getCardCompletedPercentage($projectId)
    {
        $project = $this->project->find($projectId);
        $percentages = $this->projectService->getProjectProgress($project);

        $html = '';
        foreach ($percentages as $cardName => $percentage) {
            $color = $this->getColorForPercentage($percentage);
            $html .= "<div class='mb-1'><span class='font-semibold'>{$cardName}:</span> <span style='color: {$color};'>{$percentage}%</span></div>";
        }

        return empty($html) ? 'No progress data available' : $html;
    }

    private function getColorForPercentage($percentage)
    {
        if ($percentage < 30) {
            return '#ef4444'; // red
        } elseif ($percentage < 70) {
            return '#f59e0b'; // amber
        } else {
            return '#10b981'; // green
        }
    }

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
