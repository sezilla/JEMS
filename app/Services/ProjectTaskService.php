<?php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\UserTask;
use App\Services\TrelloTask;
use Filament\Notifications\Notification;

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

    public function changeStatus($checkItemId, $cardId, $status)
    {
        $this->trelloTask->setCheckItemState($cardId, $checkItemId, $status);
    }

    public function createTask($projectId, $department, $task, $dueDate)
    {
        $cardId = $this->userTask->where('project_id', $projectId)->where('card_name', $department)->first()->card_id;

        $checklists = $this->trelloTask->getChecklist($cardId);
        $newTasksChecklist = null;

        foreach ($checklists as $checklist) {
            if ($checklist['name'] === 'New Tasks') {
                $newTasksChecklist = $checklist;
                break;
            }
        }

        if (!$newTasksChecklist) {
            $newTasksChecklist = $this->trelloTask->createChecklist($cardId, 'New Tasks');
        }

        $checkItem = $this->trelloTask->createCheckItem($newTasksChecklist['id'], $task, $dueDate);

        $this->userTask->where('project_id', $projectId)
            ->where('card_name', $department)
            ->update([
                'card_id' => $cardId,
                'check_item_id' => $checkItem['id']
            ]);

        return $checkItem;
    }

    public function deleteTask($cardId, $taskId)
    {
        $this->trelloTask->deleteCheckItemByCardId($cardId, $taskId);
    }

    public function checkTaskIfDueTomorrow()
    {
        $tomorrow = now()->addDay()->format('Y-m-d');

        $tasks = $this->userTask->whereDate('due_date', $tomorrow)->get();

        foreach ($tasks as $task) {
            Notification::make()
                ->title('Task Due Tomorrow')
                ->body('Your task: "' . $task->task_name . '" is due tomorrow')
                ->info()
                ->sendToDatabase(User::find($task->user_id));
        }
    }
}
