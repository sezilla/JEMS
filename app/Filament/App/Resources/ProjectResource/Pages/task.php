<?php

namespace App\Filament\App\Resources\ProjectResource\Pages;

use App\Models\Project;
use App\Models\Department;
use App\Services\TrelloTask;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Filament\App\Resources\ProjectResource;

class Task extends Page
{
    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.app.resources.project-resource.pages.task';

    // protected static ?string $title = 'Task Page';

    public ?array $trelloCards = null;
    public ?array $tableData = [];
    public ?array $selectedTask = null;
    public $dueDate;
    public $currentTask = [];
    public $checklistId;
    public $checkItemId;

    public function mount($record)
    {
        $project = Project::find($record);
        $boardId = $project->trello_board_id;

        if ($boardId) {
            $this->fetchTrelloCards($boardId);
            $this->tableData = $this->setTableData();
        }
    }

    public function fetchTrelloCards($boardId)
    {
        $trelloService = app(TrelloTask::class);
        $listId = $trelloService->getBoardDepartmentsListId($boardId);

        if (!$listId) {
            Log::error("Departments list not found for board: " . $boardId);
            return [];
        }

        $cards = $trelloService->getListCards($listId);
        if (!is_array($cards)) {
            Log::error("No cards found for list ID: " . $listId);
            return [];
        }

        foreach ($cards as &$card) {
            $card['checklists'] = $trelloService->getCardChecklists($card['id']);
            if (is_array($card['checklists'])) {
                foreach ($card['checklists'] as &$checklist) {
                    $checklist['items'] = $trelloService->getChecklistItems($checklist['id']);
                }
            }
        }

        $this->trelloCards = $cards;
    }

    public function setTableData()
    {
        $tableData = [];
        if (!$this->trelloCards) return $tableData;

        $user = Auth::user();
        $userDepartment = Department::forUser($user)->first();
        if (!$userDepartment) return $tableData;

        foreach ($this->trelloCards as $card) {
            if ($card['name'] !== $userDepartment->name) continue;

            foreach ($card['checklists'] as $checklist) {
                foreach ($checklist['items'] as $item) {
                    $tableData[] = [
                        'card_id'      => $card['id'],
                        'checklist_id' => $checklist['id'],
                        'item_id'      => $item['id'],
                        'department'   => $card['name'],
                        'due_date'     => $card['due'] ?? null,
                        'checklist'    => $checklist['name'],
                        'task'         => $item['name'],
                        'task_status'  => $item['state'] === 'complete' ? 'complete' : 'incomplete',
                    ];
                }
            }
        }

        return $tableData;
    }

    public function setCurrentTask($item)
    {
        $this->checklistId = $item['checklist_id'];
        $this->checkItemId = $item['item_id'];
        $this->currentTask = $item;
        $this->dueDate = $item['due'] ?? null;
    }


    public function saveDueDate()
    {
        if (!isset($this->currentTask['checklist_id'], $this->currentTask['item_id'])) {
            Notification::make()
                ->title('Missing Data')
                ->body('Missing checklist or item data.')
                ->danger()
                ->send();

            return;
        }

        $response = $this->setCheckItemDue($this->currentTask, $this->dueDate);

        if ($response && isset($response['id'])) {
            Notification::make()
                ->title('Due Date Set')
                ->body('Due date set successfully.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to Set Due Date')
                ->body('An error occurred while trying to update the due date.')
                ->danger()
                ->send();
        }

        // Refresh Trello cards and table
        $project = Project::find($this->currentTask['card_id']);
        $boardId = $project?->trello_board_id;
        if ($boardId) {
            $this->fetchTrelloCards($boardId);
            $this->tableData = $this->setTableData();
        }

        Log::info('Due date saved successfully for task.', [
            'task' => $this->currentTask,
            'due_date' => $this->dueDate,
        ]);

        return $response;
    }

    public function setCheckItemDue(array $taskData, string $dueDate)
    {
        if (!isset($taskData['card_id'], $taskData['item_id'])) {
            Log::warning('Missing card_id or item_id in task data.');
            return null;
        }

        $cardId = $taskData['card_id'];
        $checkItemId = $taskData['item_id'];

        $trelloService = app(TrelloTask::class);
        $response = $trelloService->setCheckItemDueDate($cardId, $checkItemId, $dueDate);

        if ($response) {
            Log::info("Due date set for checklist item: " . $checkItemId);
        } else {
            Log::error("Failed to set due date for checklist item: " . $checkItemId);
        }

        return $response;
    }
}
