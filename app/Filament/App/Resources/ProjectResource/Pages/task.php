<?php

namespace App\Filament\App\Resources\ProjectResource\Pages;

use App\Models\User;
use App\Models\Project;
use App\Models\UserTask;
use App\Models\Department;
use App\Services\TrelloTask;
use Filament\Actions\EditAction;
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
    public $user_id = null;
    public $item_id;
    public $project;
    public $users = [];

    public function getTitle(): string
    {
        return $this->project->name ?? 'Project';
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         EditAction::make(),
    //     ];
    // }

    public function mount($record)
    {
        $this->project = Project::find($record);

        $teams = $this->project->teams()->with('users')->get();
        $this->users = $teams->pluck('users')->flatten()->unique('id');
        foreach ($this->users as $user) {
            $user->load('teams');
        }
        // $this->users = User::all();

        if ($this->project->checklist) {
            $rawCheckItem = $this->project->checklist->user_checklist;

            if ($rawCheckItem) {
                foreach ($rawCheckItem as $checklistId => $data) {
                    $this->userCheckItem[$checklistId] = $data;
                    $this->checklistId = $checklistId;
                    $this->checkItemId = $data['check_item_id'] ?? null;
                }
            }
        }

        if ($this->project->trello_board_id) {
            $this->fetchTrelloCards($this->project->trello_board_id);
            $this->tableData = $this->setTableData();
        }
    }

    public function fetchTrelloCards($boardId)
    {
        $trelloService = app(TrelloTask::class);
        $listId = $trelloService->getBoardDepartmentsListId($boardId);

        if (!$listId) {
            Log::error("Departments list not found for board: " . $boardId);
            $this->trelloCards = [];
            return;
        }

        $user = User::find(Auth::id());
        Log::info("User Roles: " . implode(', ', $user->getRoleNames()->toArray()));

        if (!$user->hasRole(config('filament-shield.coordinator_user.name')) && !$user->hasRole('Coordinator')) {
            $userDepartment = $user->departments()->first();
            if (!$userDepartment) {
                Log::error("User department not found for user ID: " . $user->id);
                $this->trelloCards = [];
                return;
            }

            $cardName = $userDepartment->name;
            $card = $trelloService->getCardByName($listId, $cardName);
            if (!$card) {
                Log::error("No card found for list ID: " . $listId . " and card name: " . $cardName);
                $this->trelloCards = [];
                return;
            }

            $cards = [$card];
        } else {
            $cards = $trelloService->getListCards($listId);
            if (!is_array($cards) || empty($cards)) {
                Log::error("No cards found for list ID: " . $listId);
                $this->trelloCards = [];
                return;
            }
        }

        foreach ($cards as &$card) {
            $card['checklists'] = $trelloService->getCardChecklists($card['id']);

            if (!is_array($card['checklists'])) {
                continue;
            }

            foreach ($card['checklists'] as &$checklist) {
                $checklist['items'] = $trelloService->getChecklistItems($checklist['id']);

                foreach ($checklist['items'] as &$item) {
                    $item['user_id'] = null;
                    $item['state'] = $item['state'] ?? 'incomplete';

                    if (
                        isset($this->userCheckItem[$checklist['id']]) &&
                        is_array($this->userCheckItem[$checklist['id']])
                    ) {
                        foreach ($this->userCheckItem[$checklist['id']] as $assignment) {
                            if (
                                isset($assignment['check_item_id']) &&
                                $item['id'] === $assignment['check_item_id']
                            ) {
                                $item['user_id'] = $assignment['user_id'];
                                break;
                            }
                        }
                    }
                }

                Log::info('Fetched checklist items', [
                    'checklist_id' => $checklist['id'],
                    'item_count' => count($checklist['items']),
                ]);
            }
        }


        $this->trelloCards = $cards;
    }

    public function setTableData()
    {
        $tableData = [];
        if (!$this->trelloCards) return [];

        $user = Auth::user();
        $userDepartment = Department::forUser($user)->first();
        if (!$userDepartment) return [];

        foreach ($this->trelloCards as $card) {
            if ($card['name'] !== $userDepartment->name) continue;

            foreach ($card['checklists'] ?? [] as $checklist) {
                foreach ($checklist['items'] ?? [] as $item) {
                    $tableData[] = [
                        'card_id'      => $card['id'],
                        'checklist_id' => $checklist['id'],
                        'item_id'      => $item['id'],
                        'department'   => $card['name'],
                        'due_date'     => $card['due'] ?? null,
                        'checklist'    => $checklist['name'],
                        'task'         => $item['name'],
                        'task_status'  => $item['state'] === 'complete' ? 'complete' : 'incomplete',
                        'user_id'      => $item['user_id'] ?? null,
                    ];
                }
            }
        }

        return $tableData;
    }

    public function setCurrentTask($item)
    {
        $this->checklistId = $item['checklist_id'] ?? null;
        $this->checkItemId = $item['item_id'] ?? null;
        $this->currentTask = $item + ['project_id' => $this->project?->id];
        $this->checkItemName = $item['task'] ?? null;
        $this->dueDate = $item['due'] ?? null;
        $this->user_id = $item['user_id'] ?? null;
        $this->checkItemState = $item['state'] ?? 'incomplete';

        Log::info('Current task set', [
            'task' => $this->currentTask,
            'user_id' => $this->user_id,
            'currentTask_user_id' => $this->currentTask['user_id'] ?? null
        ]);
    }

    protected function showNotification($success, $successTitle, $successBody, $failTitle, $failBody)
    {
        $notification = Notification::make()
            ->title($success ? $successTitle : $failTitle)
            ->body($success ? $successBody : $failBody);

        ($success ? $notification->success() : $notification->danger())->send();

        return $success;
    }

    protected function refreshData()
    {
        if ($this->project && $this->project->trello_board_id) {
            $this->fetchTrelloCards($this->project->trello_board_id);
            $this->tableData = $this->setTableData();
        }
    }

    public function saveDueDate()
    {
        if (!isset($this->currentTask['checklist_id'], $this->currentTask['item_id'])) {
            return $this->showNotification(
                false,
                '',
                '',
                'Missing Data',
                'Missing checklist or item data.'
            );
        }

        $response = $this->setCheckItemDue($this->currentTask, $this->dueDate);
        $success = $response && isset($response['id']);

        $this->refreshData();

        Log::info('Due date save attempt', [
            'success' => $success,
            'task' => $this->currentTask,
            'due_date' => $this->dueDate,
        ]);

        return $this->showNotification(
            $success,
            'Due Date Set',
            'Due date set successfully.',
            'Failed to Set Due Date',
            'An error occurred while trying to update the due date.'
        );
    }

    public function setCheckItemDue(array $taskData, string $dueDate)
    {
        if (!isset($taskData['card_id'], $taskData['item_id'])) {
            Log::warning('Missing card_id or item_id in task data.');
            return null;
        }

        $trelloService = app(TrelloTask::class);
        $response = $trelloService->setCheckItemDueDate(
            $taskData['card_id'],
            $taskData['item_id'],
            $dueDate
        );

        Log::info($response ? "Due date set for item: " . $taskData['item_id'] :
            "Failed to set due date for item: " . $taskData['item_id']);

        return $response;
    }

    public function updateCheckItemState()
    {
        if (!isset($this->currentTask['checklist_id'], $this->currentTask['item_id'], $this->currentTask['card_id'])) {
            return $this->showNotification(
                false,
                '',
                '',
                'Missing Data',
                'Missing checklist or item data.'
            );
        }

        $trelloService = app(TrelloTask::class);
        $response = $trelloService->setCheckItemState(
            $this->currentTask['card_id'],
            $this->currentTask['item_id'],
            $this->currentTask['desired_state'] ?? 'complete' || 'incomplete'
        );
        $success = $response && isset($response['id']);

        $this->refreshData();

        Log::info('Checklist item state update attempt', [
            'success' => $success,
            'task' => $this->currentTask,
        ]);

        if ($success) {
            UserTask::where('check_item_id', $this->currentTask['item_id'])
                ->update(['status' => $this->currentTask['desired_state'] ?? 'complete']);
        }

        return $this->showNotification(
            $success,
            'Checklist Item Completed',
            'Checklist item marked as complete.',
            'Failed to Mark as Complete',
            'An error occurred while trying to mark the checklist item as complete.'
        );
    }

    public function assignUserToCheckItem()
    {
        if (!isset($this->currentTask['checklist_id'], $this->currentTask['item_id'])) {
            return $this->showNotification(
                false,
                '',
                '',
                'Missing Data',
                'Missing checklist or item data.'
            );
        }

        if (!$this->project) {
            return $this->showNotification(
                false,
                '',
                '',
                'Project Not Found',
                'The specified project could not be found.'
            );
        }

        $userId = null;
        if (isset($this->currentTask['user_id']) && !empty($this->currentTask['user_id'])) {
            $userId = (int) $this->currentTask['user_id'];
        } elseif (!empty($this->user_id)) {
            $userId = (int) $this->user_id;
            $this->currentTask['user_id'] = $userId;
        }

        if ($userId && $this->project) {
            $checklistId = $this->currentTask['checklist_id'];
            $itemId = $this->currentTask['item_id'];

            $checklistUser = $this->project->checklist;
            if (!$checklistUser) {
                $checklistUser = new \App\Models\ChecklistUser([
                    'project_id' => $this->project->id,
                    'user_checklist' => [],
                ]);
                $this->project->checklist()->save($checklistUser);
            }

            $userChecklist = $checklistUser->user_checklist ?? [];

            if (!isset($userChecklist[$checklistId]) || !is_array($userChecklist[$checklistId])) {
                $userChecklist[$checklistId] = [];
            }

            $userChecklist[$checklistId] = array_filter(
                $userChecklist[$checklistId],
                fn($entry) => $entry['check_item_id'] !== $itemId
            );

            $userChecklist[$checklistId][] = [
                'user_id' => $userId,
                'check_item_id' => $itemId,
            ];

            $checklistUser->user_checklist = $userChecklist;
            $checklistUser->save();

            UserTask::where('check_item_id', $itemId)->delete();

            UserTask::updateOrCreate([
                'user_id' => $userId,
                'check_item_id' => $itemId,
            ]);

            $this->userCheckItem = $userChecklist;

            Log::info('User assignment updated', [
                'checklist_id' => $checklistId,
                'item_id'      => $itemId,
                'user_id'      => $userId,
                'project_id'   => $this->project->id,
            ]);
        }

        $this->refreshData();

        Log::info('Task edit attempt', [
            'task' => $this->currentTask,
        ]);

        return $this->showNotification(
            true,
            'Task Updated',
            'Task updated successfully.',
            'Update Failed',
            'An error occurred while updating the task.'
        );
    }

    public function saveEditTask()
    {
        if (!isset($this->currentTask['checklist_id'], $this->currentTask['item_id'], $this->currentTask['card_id'])) {
            return $this->showNotification(
                false,
                '',
                '',
                'Missing Data',
                'Missing checklist or item data.'
            );
        }

        Log::info('Pre-save values', [
            'user_id_property' => $this->user_id,
            'currentTask_user_id' => $this->currentTask['user_id'] ?? null
        ]);

        $state = ($this->currentTask['state'] === true || $this->currentTask['state'] === 1 ||
            $this->currentTask['state'] === '1') ? 'complete' : 'incomplete';

        $trelloService = app(TrelloTask::class);
        $response = $trelloService->updateCheckItemDetails(
            $this->currentTask['card_id'],
            $this->currentTask['item_id'],
            $this->currentTask['name'] ?? null,
            $this->currentTask['due_date'] ?? null,
            $state
        );
        $success = $response && isset($response['id']);

        $userId = null;
        if (isset($this->currentTask['user_id']) && !empty($this->currentTask['user_id'])) {
            $userId = (int) $this->currentTask['user_id'];
        } elseif (!empty($this->user_id)) {
            $userId = (int) $this->user_id;
            $this->currentTask['user_id'] = $userId;
        }

        if ($success && $userId && $this->project) {
            $checklistId = $this->currentTask['checklist_id'];
            $itemId = $this->currentTask['item_id'];

            $checklistUser = $this->project->checklist;
            if (!$checklistUser) {
                $checklistUser = new \App\Models\ChecklistUser([
                    'project_id' => $this->project->id,
                    'user_checklist' => [],
                ]);
                $this->project->checklist()->save($checklistUser);
            }

            $userChecklist = $checklistUser->user_checklist ?? [];

            if (!isset($userChecklist[$checklistId]) || !is_array($userChecklist[$checklistId])) {
                $userChecklist[$checklistId] = [];
            }

            $userChecklist[$checklistId][] = [
                'user_id' => $userId,
                'check_item_id' => $itemId,
            ];

            $checklistUser->user_checklist = $userChecklist;
            $checklistUser->save();

            UserTask::where('check_item_id', $itemId)->delete();

            UserTask::updateOrCreate([
                'user_id' => $userId,
                'check_item_id' => $itemId,
            ], [
                'status' => $state,
            ]);

            $this->userCheckItem = $userChecklist;

            Log::info('User assignment updated in database', [
                'checklist_id' => $checklistId,
                'item_id'      => $itemId,
                'user_id'      => $userId,
                'project_id'   => $this->project->id
            ]);
        }

        $this->refreshData();

        Log::info('Task edit attempt', [
            'success' => $success,
            'task'    => $this->currentTask,
        ]);

        return $this->showNotification(
            $success,
            'Task Updated',
            'Task updated successfully.',
            'Update Failed',
            'An error occurred while updating the task.'
        );
    }

    public function updatedUserId($value)
    {
        if ($this->currentTask) {
            $this->currentTask['user_id'] = $value;
            Log::info('Updated user_id in currentTask', [
                'user_id' => $value,
                'currentTask.user_id' => $this->currentTask['user_id'] ?? null
            ]);
        }
    }

    public function createTask()
    {
        if (!empty($this->user_id)) {
            $this->currentTask['user_id'] = $this->user_id;
        }

        if (!isset($this->currentTask['checklist_id'], $this->currentTask['card_id'])) {
            return $this->showNotification(
                false,
                '',
                '',
                'Missing Data',
                'Missing checklist or card data.'
            );
        }

        $trelloService = app(TrelloTask::class);
        $response = $trelloService->createCheckItem(
            $this->currentTask['checklist_id'],
            $this->currentTask['name'],
            $this->currentTask['due_date'] ?? null
        );

        $success = $response && isset($response['id']);

        if ($success) {
            $this->currentTask['item_id'] = $response['id'];
        }


        $userId = null;
        if (isset($this->currentTask['user_id']) && !empty($this->currentTask['user_id'])) {
            $userId = (int) $this->currentTask['user_id'];
        } elseif (!empty($this->user_id)) {
            $userId = (int) $this->user_id;
            $this->currentTask['user_id'] = $userId;
        }

        if ($userId && $this->project) {
            $checklistId = $this->currentTask['checklist_id'];
            $itemId = $this->currentTask['item_id'];

            $checklistUser = $this->project->checklist;
            if (!$checklistUser) {
                $checklistUser = new \App\Models\ChecklistUser([
                    'project_id' => $this->project->id,
                    'user_checklist' => [],
                ]);
                $this->project->checklist()->save($checklistUser);
            }

            $userChecklist = $checklistUser->user_checklist ?? [];

            if (!isset($userChecklist[$checklistId]) || !is_array($userChecklist[$checklistId])) {
                $userChecklist[$checklistId] = [];
            }

            $userChecklist[$checklistId][] = [
                'user_id' => $userId,
                'check_item_id' => $itemId,
            ];

            $checklistUser->user_checklist = $userChecklist;
            $checklistUser->save();

            UserTask::where('check_item_id', $itemId)->delete();

            UserTask::updateOrCreate([
                'user_id' => $userId,
                'check_item_id' => $itemId,
            ]);

            $this->userCheckItem = $userChecklist;

            Log::info('User assignment updated in database', [
                'checklist_id' => $checklistId,
                'item_id'      => $itemId,
                'user_id'      => $userId,
                'project_id'   => $this->project->id
            ]);
        }

        $this->refreshData();

        Log::info('Task creation attempt', [
            'success' => $success,
            'checklist_id' => $this->currentTask['checklist_id'],
            'task_name' => $this->currentTask['name']
        ]);

        return $this->showNotification(
            $success,
            'Task Created',
            'New task created successfully.',
            'Failed to Create Task',
            'An error occurred while trying to create the task.'
        );
    }

    public function deleteTask()
    {
        if (!isset($this->currentTask['checklist_id'], $this->currentTask['item_id'])) {
            return $this->showNotification(
                false,
                '',
                '',
                'Missing Data',
                'Missing checklist or item data.'
            );
        }

        $trelloService = app(TrelloTask::class);
        $success = $trelloService->deleteCheckItem(
            $this->currentTask['checklist_id'],
            $this->currentTask['item_id']
        );

        $this->refreshData();

        Log::info('Task deletion attempt', [
            'success' => $success,
            'checklist_id' => $this->currentTask['checklist_id'],
            'item_id' => $this->currentTask['item_id'],
        ]);

        return $this->showNotification(
            $success,
            'Task Deleted',
            'Task deleted successfully.',
            'Failed to Delete Task',
            'An error occurred while trying to delete the task.'
        );
    }

    public function setDepartmentDue()
    {
        if (!isset($this->currentTask['card_id'], $this->currentTask['due_date'])) {
            return $this->showNotification(
                false,
                '',
                '',
                'Missing Data',
                'Missing card ID or due date.'
            );
        }

        $cardId = $this->currentTask['card_id'];
        $dueDate = $this->currentTask['due_date'];

        $trelloService = app(TrelloTask::class);
        $response = $trelloService->setCardDue($cardId, $dueDate);
        $success = $response && isset($response['id']);

        $this->refreshData();

        Log::info('Department due date save attempt', [
            'success'  => $success,
            'card_id'  => $cardId,
            'due_date' => $dueDate,
        ]);

        return $this->showNotification(
            $success,
            'Department Due Date Set',
            'Due date set successfully.',
            'Failed to Set Department Due Date',
            'An error occurred while trying to update the due date.'
        );
    }
}
