<?php

namespace App\Filament\App\Resources\ProjectResource\Pages;

use App\Models\User;
use App\Models\Project;
use App\Models\Department;
use App\Services\TrelloTask;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;
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
    public $userCheckItem = [];
    public $userCheckItemModel = null;
    public $checkItemState = 'incomplete';
    public $checkItemName = null;
    public $user_id;
    public $item_id;
    public $project;
    public $users = [];

    public function mount($record)
    {
        $project = Project::find($record);
        $this->users = User::all();
        $this->project = $project;

        $boardId = $project->trello_board_id;

        $userCheckItemModel = $project->checklist;

        if ($userCheckItemModel) {
            $rawCheckItem = $userCheckItemModel->user_checklist;

            if ($rawCheckItem) {
                foreach ($rawCheckItem as $checklistId => $data) {
                    $this->userCheckItem[$checklistId] = $data;

                    $this->checklistId = $checklistId;
                    $this->checkItemId = $data['check_item_id'] ?? null;
                }
            }
        }

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

        //model here

        foreach ($cards as &$card) {
            $card['checklists'] = $trelloService->getCardChecklists($card['id']);
            if (is_array($card['checklists'])) {
                foreach ($card['checklists'] as &$checklist) {
                    $checklist['items'] = $trelloService->getChecklistItems($checklist['id']);
                    foreach ($checklist['items'] as &$item) {
                        $item['user_id'] = null;
                        $item['state'] = $item['state'] ?? 'incomplete';

                        if (isset($this->userCheckItem[$checklist['id']])) {
                            $mapped = $this->userCheckItem[$checklist['id']];

                            if ($item['id'] === $mapped['check_item_id']) {
                                $item['user_id'] = $mapped['user_id'];
                            }
                        }
                    }
                    Log::info('Fetched checklist items for checklist ID: ' . $checklist['id'], [
                        'checklist_id' => $checklist['id'],
                        'items' => $checklist['items'],
                        'tests' => is_array($checklist['items']),
                    ]);
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
                        'user_id'      => $item['user_id'] ?? 1,
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

        $this->currentTask = $item + ['project_id' => $this->project?->id];

        $this->checkItemName = $item['task'] ?? null;
        $this->dueDate = $item['due'] ?? null;
        $this->userCheckItemModel = $item['user_id'] ?? null;
        $this->checkItemState = $item['state'] ?? 'incomplete';
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

    public function updateCheckItemState()
    {
        if (!isset($this->currentTask['checklist_id'], $this->currentTask['item_id'], $this->currentTask['card_id'])) {
            Notification::make()
                ->title('Missing Data')
                ->body('Missing checklist or item data.')
                ->danger()
                ->send();

            return;
        }

        $trelloService = app(TrelloTask::class);
        $response = $trelloService->setCheckItemState(
            $this->currentTask['card_id'],
            $this->currentTask['item_id'],
            'complete'
        );

        if ($response && isset($response['id'])) {
            Notification::make()
                ->title('Checklist Item Completed')
                ->body('Checklist item marked as complete.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to Mark as Complete')
                ->body('An error occurred while trying to mark the checklist item as complete.')
                ->danger()
                ->send();
        }

        $project = Project::find($this->currentTask['card_id']);
        $boardId = $project?->trello_board_id;
        if ($boardId) {
            $this->fetchTrelloCards($boardId);
            $this->tableData = $this->setTableData();
        }

        Log::info('Checklist item marked as complete.', [
            'task' => $this->currentTask,
        ]);

        return $response;
    }

    //not yet working
    public function assignUserToCheckItem()
    {
        if (!isset($this->currentTask['checklist_id'], $this->currentTask['item_id'])) {
            Notification::make()
                ->title('Missing Data')
                ->body('Missing checklist or item data.')
                ->danger()
                ->send();
            return;
        }

        $project = $this->project;
        if (!$project) {
            Notification::make()
                ->title('Project Not Found')
                ->body('The specified project could not be found.')
                ->danger()
                ->send();
            return;
        }

        $userCheckItemModel = $project->checklist;

        $checklistId = $this->currentTask['checklist_id'];
        $itemId = $this->currentTask['item_id'];
        $userId = (int)$this->user_id;

        $currentChecklist = $userCheckItemModel->project->user_checklist ?? [];

        $currentChecklist[$checklistId] = [
            'check_item_id' => $itemId,
            'user_id'       => $userId,
        ];

        $userCheckItemModel->project->user_checklist = $currentChecklist;
        $saved = $userCheckItemModel->save();

        $response = $saved ? $currentChecklist : null;

        if ($response && isset($response['id'])) {
            Notification::make()
                ->title('User Assigned')
                ->body('User successfully assigned to checklist item.')
                ->success()
                ->send();

            if ($userCheckItemModel && $userCheckItemModel->user_checklist) {
                $rawCheckItem = $userCheckItemModel->user_checklist;
                foreach ($rawCheckItem as $checklistId => $data) {
                    $this->userCheckItem[$checklistId] = $data;
                    $this->checklistId = $checklistId;
                    $this->checkItemId = $data['check_item_id'] ?? null;
                }
                Log::info('User checklist updated successfully.', [
                    'checklist_id' => $this->checklistId,
                    'check_item_id' => $this->checkItemId,
                    'user_id' => $this->user_id,
                ]);
            }
        } else {
            Notification::make()
                ->title('Assignment Failed')
                ->body('An error occurred while assigning the user.')
                ->danger()
                ->send();
            Log::error('Failed to assign user to checklist item.', [
                'checklist_id' => $this->currentTask['checklist_id'],
                'item_id' => $this->currentTask['item_id'],
                'user_id' => $this->user_id,
                'response' => $response,
            ]);
        }

        $boardId = $project->trello_board_id;
        if ($boardId) {
            $this->fetchTrelloCards($boardId);
            $this->tableData = $this->setTableData();
        }

        Log::info('User assignment to checklist item attempted.', [
            'task' => $this->currentTask,
            'response' => $response,
        ]);

        return $response;
    }

    public function saveEditTask()
    {
        if (!isset($this->currentTask['checklist_id'], $this->currentTask['item_id'], $this->currentTask['card_id'])) {
            Notification::make()
                ->title('Missing Data')
                ->body('Missing checklist or item data.')
                ->danger()
                ->send();

            return;
        }

        $state = ($this->currentTask['state'] === true || $this->currentTask['state'] === 1 || $this->currentTask['state'] === '1')
            ? 'complete'
            : 'incomplete';

        $trelloService = app(TrelloTask::class);
        $response = $trelloService->updateCheckItemDetails(
            $this->currentTask['card_id'],
            $this->currentTask['item_id'],
            $this->currentTask['name'],
            $this->currentTask['due_date'],
            $state
        );

        if ($response && isset($response['id'])) {
            Notification::make()
                ->title('Checklist Item Completed')
                ->body('Checklist item marked as complete.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to Mark as Complete')
                ->body('An error occurred while trying to mark the checklist item as complete.')
                ->danger()
                ->send();
        }

        $project = Project::find($this->currentTask['card_id']);
        $boardId = $project?->trello_board_id;
        if ($boardId) {
            $this->fetchTrelloCards($boardId);
            $this->tableData = $this->setTableData();
        }

        Log::info('Checklist item marked as complete.', [
            'task' => $this->currentTask,
        ]);

        return $response;
    }

    public function createTask()
    {
        if (!isset($this->currentTask['checklist_id'], $this->currentTask['card_id'])) {
            Notification::make()
                ->title('Missing Data')
                ->body('Missing checklist or card data.')
                ->danger()
                ->send();

            return;
        }

        $trelloService = app(TrelloTask::class);
        $response = $trelloService->createCheckItem(
            $this->currentTask['checklist_id'],
            $this->currentTask['name'],
            $this->currentTask['due_date'] ?? null
        );

        if ($response && isset($response['id'])) {
            Notification::make()
                ->title('Task Created')
                ->body('New task created successfully.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to Create Task')
                ->body('An error occurred while trying to create the task.')
                ->danger()
                ->send();
        }

        $project = Project::find($this->project->id);
        $boardId = $project?->trello_board_id;
        if ($boardId) {
            $this->fetchTrelloCards($boardId);
            $this->tableData = $this->setTableData();
        }

        Log::info('New task created.', [
            'checklist_id' => $this->currentTask['checklist_id'],
            'task_name' => $this->currentTask['name']
        ]);

        return $response;
    }

    public function deleteTask()
    {
        if (!isset($this->currentTask['checklist_id'], $this->currentTask['item_id'])) {
            Notification::make()
                ->title('Missing Data')
                ->body('Missing checklist or item data.')
                ->danger()
                ->send();

            return;
        }

        $trelloService = app(TrelloTask::class);
        $success = $trelloService->deleteCheckItem(
            $this->currentTask['checklist_id'],
            $this->currentTask['item_id']
        );

        if ($success) {
            Notification::make()
                ->title('Task Deleted')
                ->body('Task deleted successfully.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to Delete Task')
                ->body('An error occurred while trying to delete the task.')
                ->danger()
                ->send();
        }

        $project = Project::find($this->project->id);
        $boardId = $project?->trello_board_id;
        if ($boardId) {
            $this->fetchTrelloCards($boardId);
            $this->tableData = $this->setTableData();
        }

        Log::info('Task deleted.', [
            'checklist_id' => $this->currentTask['checklist_id'],
            'item_id' => $this->currentTask['item_id'],
        ]);

        return $success;
    }
}
