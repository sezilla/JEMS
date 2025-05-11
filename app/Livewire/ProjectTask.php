<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Models\UserTask;
use App\Services\TrelloTask;
use App\Models\ChecklistUser;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class ProjectTask extends Component
{
    public $project;
    public $cards = [];
    public $error = null;

    protected $trelloTask;

    public bool $loading = true;

    public $currentCard = null;
    public $currentCardName = null;
    public $currentCardDueDate = null;
    public $currentCardDescription = null;

    public $currentChecklist = null;
    public $currentChecklistName = null;

    public $currentCheckItem = null;
    public $assignedUser = null;
    public $checkItemDueDate = null;
    public $checkItemStatus = null;
    public $currentCheckItemName = null;

    public $currentTask = null;

    public $cardDueDate = '';
    public $cardDescription = '';
    public $editingCardId = null;

    public $users = [];

    public function boot(TrelloTask $trelloTask)
    {
        $this->trelloTask = $trelloTask;
    }

    public function mount($project)
    {
        try {
            $this->project = $project;

            if (!$this->project) {
                throw new \Exception('Project not found');
            }

            // Load users from project teams
            $teams = $this->project->teams()->with('users')->get();
            $this->users = $teams->pluck('users')->flatten()->unique('id');
            foreach ($this->users as $user) {
                $user->load('teams');
            }

            $checklistUser = ChecklistUser::where('project_id', $project->id)->first();

            Log::info('Raw ChecklistUser data:', [
                'project_id' => $project->id,
                'checklist_user_exists' => $checklistUser ? 'Yes' : 'No',
                'raw_user_checklist' => $checklistUser ? $checklistUser->user_checklist : null
            ]);

            if ($checklistUser) {
                if (is_string($checklistUser->user_checklist)) {
                    $this->cards = json_decode($checklistUser->user_checklist, true) ?: [];
                } else {
                    $this->cards = is_array($checklistUser->user_checklist) ? $checklistUser->user_checklist : [];
                }

                Log::info('Decoded cards data:', [
                    'cards' => $this->cards,
                    'cards_type' => gettype($this->cards),
                    'cards_count' => is_array($this->cards) ? count($this->cards) : 'not an array'
                ]);
            } else {
                $this->cards = [];
            }
        } catch (\Exception $e) {
            Log::error('Error in ProjectTask mount: ' . $e->getMessage());
            $this->error = $e->getMessage();
            $this->cards = [];
        }

        $this->loading = false;
    }

    protected function showNotification($success, $successTitle, $successBody, $failTitle, $failBody)
    {
        $notification = Notification::make()
            ->title($success ? $successTitle : $failTitle)
            ->body($success ? $successBody : $failBody);

        ($success ? $notification->success() : $notification->danger())->send();

        return $success;
    }

    public function setCurrentCard($card)
    {
        if (!is_array($card)) {
            throw new \InvalidArgumentException('Card must be an array');
        }

        Log::info('Setting current card', ['card' => $card]);

        $this->currentCard = $card['card_id'] ?? null;
        $this->currentCardName = $card['card_name'] ?? null;
        $this->currentCardDueDate = $card['card_due_date'] ?? null;
        $this->currentCardDescription = $card['card_description'] ?? null;

        Log::info('Current card set', [
            'currentCard' => $this->currentCard,
            'currentCardName' => $this->currentCardName,
            'currentCardDueDate' => $this->currentCardDueDate,
            'currentCardDescription' => $this->currentCardDescription
        ]);
    }

    public function setCurrentChecklist($checklist)
    {
        if (!is_array($checklist)) {
            throw new \InvalidArgumentException('Checklist must be an array');
        }

        $this->currentChecklist = $checklist['checklist_id'] ?? null;
        $this->currentChecklistName = $checklist['checklist_name'] ?? null;
    }

    public function setCurrentTask($item)
    {
        if (!is_array($item)) {
            throw new \InvalidArgumentException('Task item must be an array');
        }

        $this->checkItemStatus = $item['status'] ?? 'incomplete';
        $this->assignedUser = $item['user_id'] ?? null;
        $this->checkItemDueDate = $item['due_date'] ?? null;
        $this->currentCheckItem = $item['check_item_id'] ?? null;
        $this->currentCheckItemName = $item['check_item_name'] ?? null;
    }

    public function openCardModal($cardId = null)
    {
        if (!$cardId) {
            return;
        }

        foreach ($this->cards as $card) {
            if (($card['card_id'] ?? null) == $cardId) {
                $this->editingCardId = $cardId;
                $this->cardDueDate = $card['card_due_date'] ?? '';
                $this->cardDescription = $card['card_description'] ?? '';
                break;
            }
        }
    }

    public function saveCard($cardId)
    {
        if (!$cardId) {
            Log::error('Card ID is missing in saveCard');
            return $this->showNotification(
                false,
                'Card Not Found',
                'Card not found.',
                'Failed to update card',
                'An error occurred while trying to update the card.'
            );
        }

        try {
            Log::info('Attempting to save card', [
                'cardId' => $cardId,
                'cardDueDate' => $this->cardDueDate,
                'cardDescription' => $this->cardDescription
            ]);

            $checklistUser = ChecklistUser::where('project_id', $this->project->id)->first();

            if (!$checklistUser) {
                Log::error('ChecklistUser not found', ['project_id' => $this->project->id]);
                throw new \Exception('Checklist not found');
            }

            Log::info('Current checklist data', [
                'user_checklist' => $checklistUser->user_checklist
            ]);

            $checklistUser->user_checklist = array_map(function ($card) use ($cardId) {
                Log::info('Processing card', [
                    'card_id' => $card['card_id'] ?? null,
                    'target_card_id' => $cardId,
                    'matches' => ($card['card_id'] ?? null) == $cardId
                ]);

                if (($card['card_id'] ?? null) == $cardId) {
                    $card['card_due_date'] = $this->cardDueDate;
                    $card['card_description'] = $this->cardDescription;
                }
                return $card;
            }, $checklistUser->user_checklist);

            try {
                $this->trelloTask->updateCard($cardId, [
                    'due' => $this->cardDueDate,
                    'desc' => $this->cardDescription
                ]);
            } catch (\Exception $e) {
                Log::error('Error updating card', [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                return $this->showNotification(
                    false,
                    'Failed to update card',
                    'An error occurred while trying to update the card.',
                    'Failed to update card',
                    'An error occurred while trying to update the card.'
                );
            }

            Log::info('Updated checklist data', [
                'user_checklist' => $checklistUser->user_checklist
            ]);

            $checklistUser->save();

            $this->cards = $checklistUser->user_checklist;

            $this->reset(['cardDueDate', 'cardDescription', 'editingCardId']);

            return $this->showNotification(
                true,
                'Card Updated',
                'Card updated successfully.',
                'Failed to update card',
                'An error occurred while trying to update the card.'
            );
        } catch (\Exception $e) {
            Log::error('Error saving card: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update card: ' . $e->getMessage()
            ]);
        }
    }

    public function openChecklistModal($cardId, $checklistId)
    {
        if (!$cardId || !$checklistId) {
            return;
        }

        $this->currentCard = $cardId;
        $this->currentChecklist = $checklistId;

        foreach ($this->cards as $card) {
            if (($card['card_id'] ?? null) == $cardId) {
                foreach ($card['checklists'] ?? [] as $checklist) {
                    if (($checklist['checklist_id'] ?? null) == $checklistId) {
                        $this->currentChecklistName = $checklist['checklist_name'] ?? null;
                        break 2;
                    }
                }
            }
        }

        Log::info('Opened checklist modal', [
            'currentCard' => $this->currentCard,
            'currentChecklist' => $this->currentChecklist,
            'currentChecklistName' => $this->currentChecklistName
        ]);
    }

    public function createTask($checklistId)
    {
        if (!$checklistId) {
            Log::error('Checklist ID is missing in createTask');
            return $this->showNotification(
                false,
                'Checklist Not Found',
                'Checklist not found.',
                'Failed to create task',
                'An error occurred while trying to create the task.'
            );
        }

        if (!$this->currentCheckItemName) {
            return $this->showNotification(
                false,
                'Task Name Required',
                'Please enter a task name.',
                'Failed to create task',
                'Task name is required.'
            );
        }

        if (!$this->currentCard) {
            Log::error('Missing card data', [
                'currentCard' => $this->currentCard
            ]);
            return $this->showNotification(
                false,
                'Missing Data',
                'Card information is missing.',
                'Failed to create task',
                'An error occurred while trying to create the task.'
            );
        }

        Log::info('Creating task', [
            'currentCard' => $this->currentCard,
            'checklistId' => $checklistId,
            'currentChecklistName' => $this->currentChecklistName,
            'taskName' => $this->currentCheckItemName,
            'dueDate' => $this->checkItemDueDate,
            'assignedUser' => $this->assignedUser
        ]);

        $response = $this->trelloTask->createCheckItem(
            $checklistId,
            $this->currentCheckItemName,
            $this->checkItemDueDate
        );

        if (!$response || !isset($response['id'])) {
            Log::error('Failed to create Trello check item', [
                'response' => $response,
                'checklistId' => $checklistId,
                'taskName' => $this->currentCheckItemName
            ]);
            return $this->showNotification(
                false,
                'Failed to create task',
                'An error occurred while trying to create the task in Trello.',
                'Failed to create task',
                'An error occurred while trying to create the task.'
            );
        }

        $newCheckItemId = $response['id'];

        $checklistUser = ChecklistUser::where('project_id', $this->project->id)->first();
        if (!$checklistUser) {
            $checklistUser = new ChecklistUser([
                'project_id' => $this->project->id,
                'user_checklist' => [],
            ]);
            $this->project->checklist()->save($checklistUser);
        }

        $checklistUser->user_checklist = array_map(function ($card) use ($newCheckItemId, $checklistId) {
            if (($card['card_id'] ?? null) == $this->currentCard) {
                $card['checklists'] = array_map(function ($checklist) use ($newCheckItemId, $checklistId) {
                    if (($checklist['checklist_id'] ?? null) == $checklistId) {
                        $checklist['check_items'][] = [
                            'check_item_id' => $newCheckItemId,
                            'check_item_name' => $this->currentCheckItemName,
                            'due_date' => $this->checkItemDueDate,
                            'user_id' => $this->assignedUser,
                            'status' => 'incomplete'
                        ];
                    }
                    return $checklist;
                }, $card['checklists'] ?? []);
            }
            return $card;
        }, $checklistUser->user_checklist);

        $checklistUser->save();

        UserTask::create([
            'project_id' => $this->project->id,
            'user_id' => $this->assignedUser,
            'check_item_id' => $newCheckItemId,
            'card_id' => $this->currentCard,
            'due_date' => $this->checkItemDueDate,
            'task_name' => $this->currentCheckItemName,
            'status' => 'incomplete'
        ]);

        $this->cards = $checklistUser->user_checklist;

        $this->reset(['currentCheckItem', 'checkItemDueDate', 'assignedUser', 'currentCheckItemName']);

        return $this->showNotification(
            true,
            'Task Created',
            'Task created successfully.',
            'Failed to create task',
            'An error occurred while trying to create the task.'
        );
    }

    public function openTaskModal($cardId, $checklistId, $taskId)
    {
        if (!$cardId || !$checklistId || !$taskId) {
            return;
        }

        $this->currentCard = $cardId;
        $this->currentChecklist = $checklistId;
        $this->currentTask = $taskId;

        foreach ($this->cards as $card) {
            if (($card['card_id'] ?? null) == $cardId) {
                foreach ($card['checklists'] ?? [] as $checklist) {
                    if (($checklist['checklist_id'] ?? null) == $checklistId) {
                        foreach ($checklist['check_items'] ?? [] as $item) {
                            if (($item['check_item_id'] ?? null) == $taskId) {
                                $this->currentTask = $item;
                                break 3;
                            }
                        }
                    }
                }
            }
        }
    }

    public function updateTask($cardId, $checklistId, $taskId)
    {
        if (!$cardId || !$checklistId || !$taskId) {
            Log::error('Missing required IDs in updateTask', [
                'cardId' => $cardId,
                'checklistId' => $checklistId,
                'taskId' => $taskId
            ]);
            return $this->showNotification(
                false,
                'Missing Data',
                'Required task information is missing.',
                'Failed to update task',
                'An error occurred while trying to update the task.'
            );
        }

        if (!$this->currentCheckItemName) {
            return $this->showNotification(
                false,
                'Task Name Required',
                'Please enter a task name.',
                'Failed to update task',
                'Task name is required.'
            );
        }

        try {
            Log::info('Updating task', [
                'cardId' => $cardId,
                'checklistId' => $checklistId,
                'taskId' => $taskId,
                'taskName' => $this->currentCheckItemName,
                'dueDate' => $this->checkItemDueDate,
                'assignedUser' => $this->assignedUser
            ]);

            // Update task in Trello
            $response = $this->trelloTask->updateCheckItemDetails(
                $cardId,
                $taskId,
                $this->currentCheckItemName,
                $this->checkItemDueDate,
                $this->checkItemStatus
            );

            if (!$response) {
                Log::error('Failed to update Trello check item', [
                    'response' => $response,
                    'taskId' => $taskId,
                    'taskName' => $this->currentCheckItemName
                ]);
                return $this->showNotification(
                    false,
                    'Failed to update task',
                    'An error occurred while trying to update the task in Trello.',
                    'Failed to update task',
                    'An error occurred while trying to update the task.'
                );
            }

            // Update task in local database
            $checklistUser = ChecklistUser::where('project_id', $this->project->id)->first();
            if (!$checklistUser) {
                throw new \Exception('Checklist not found');
            }

            $checklistUser->user_checklist = array_map(function ($card) use ($cardId, $checklistId, $taskId) {
                if (($card['card_id'] ?? null) == $cardId) {
                    $card['checklists'] = array_map(function ($checklist) use ($checklistId, $taskId) {
                        if (($checklist['checklist_id'] ?? null) == $checklistId) {
                            $checklist['check_items'] = array_map(function ($item) use ($taskId) {
                                if (($item['check_item_id'] ?? null) == $taskId) {
                                    $item['check_item_name'] = $this->currentCheckItemName;
                                    $item['due_date'] = $this->checkItemDueDate;
                                    $item['user_id'] = $this->assignedUser;
                                    $item['status'] = $this->checkItemStatus ?? 'incomplete';
                                }
                                return $item;
                            }, $checklist['check_items'] ?? []);
                        }
                        return $checklist;
                    }, $card['checklists'] ?? []);
                }
                return $card;
            }, $checklistUser->user_checklist);

            $checklistUser->save();

            $userTask = UserTask::where('check_item_id', $taskId)->first();
            if ($userTask) {
                $userTask->update([
                    'user_id' => $this->assignedUser,
                    'task_name' => $this->currentCheckItemName,
                    'status' => $this->checkItemStatus ?? 'incomplete'
                ]);
            } else {
                UserTask::create([
                    'project_id' => $this->project->id,
                    'user_id' => $this->assignedUser,
                    'check_item_id' => $taskId,
                    'card_id' => $cardId,
                    'due_date' => $this->checkItemDueDate,
                    'task_name' => $this->currentCheckItemName,
                    'status' => $this->checkItemStatus ?? 'incomplete'
                ]);
            }

            $this->cards = $checklistUser->user_checklist;

            $this->reset(['currentCheckItem', 'checkItemDueDate', 'assignedUser', 'currentCheckItemName', 'checkItemStatus']);

            return $this->showNotification(
                true,
                'Task Updated',
                'Task updated successfully.',
                'Failed to update task',
                'An error occurred while trying to update the task.'
            );
        } catch (\Exception $e) {
            Log::error('Error updating task: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->showNotification(
                false,
                'Failed to update task',
                'An error occurred while trying to update the task.',
                'Failed to update task',
                'An error occurred while trying to update the task.'
            );
        }
    }

    public function updateTaskDueDate($cardId, $checklistId, $taskId)
    {
        if (!$cardId || !$checklistId || !$taskId) {
            Log::error('Missing required IDs in updateTaskDueDate', [
                'cardId' => $cardId,
                'checklistId' => $checklistId,
                'taskId' => $taskId
            ]);
            return $this->showNotification(
                false,
                'Missing Data',
                'Required task information is missing.',
                'Failed to update task due date',
                'An error occurred while trying to update the task due date.'
            );
        }

        try {
            Log::info('Updating task due date', [
                'cardId' => $cardId,
                'checklistId' => $checklistId,
                'taskId' => $taskId,
                'dueDate' => $this->checkItemDueDate
            ]);

            // Update task in Trello
            $response = $this->trelloTask->updateCheckItemDetails(
                $cardId,
                $taskId,
                null,
                $this->checkItemDueDate,
                null
            );

            if (!$response) {
                Log::error('Failed to update Trello check item due date', [
                    'response' => $response,
                    'taskId' => $taskId,
                    'dueDate' => $this->checkItemDueDate
                ]);
                return $this->showNotification(
                    false,
                    'Failed to update task due date',
                    'An error occurred while trying to update the task due date in Trello.',
                    'Failed to update task due date',
                    'An error occurred while trying to update the task due date.'
                );
            }

            $checklistUser = ChecklistUser::where('project_id', $this->project->id)->first();
            if (!$checklistUser) {
                throw new \Exception('Checklist not found');
            }

            $checklistUser->user_checklist = array_map(function ($card) use ($cardId, $checklistId, $taskId) {
                if (($card['card_id'] ?? null) == $cardId) {
                    $card['checklists'] = array_map(function ($checklist) use ($checklistId, $taskId) {
                        if (($checklist['checklist_id'] ?? null) == $checklistId) {
                            $checklist['check_items'] = array_map(function ($item) use ($taskId) {
                                if (($item['check_item_id'] ?? null) == $taskId) {
                                    $item['due_date'] = $this->checkItemDueDate;
                                }
                                return $item;
                            }, $checklist['check_items'] ?? []);
                        }
                        return $checklist;
                    }, $card['checklists'] ?? []);
                }
                return $card;
            }, $checklistUser->user_checklist);

            $checklistUser->save();

            $userTask = UserTask::where('check_item_id', $taskId)->first();
            if ($userTask) {
                $userTask->update([
                    'due_date' => $this->checkItemDueDate
                ]);
            } else {
                UserTask::create([
                    'project_id' => $this->project->id,
                    'user_id' => $this->assignedUser,
                    'check_item_id' => $taskId,
                    'card_id' => $cardId,
                    'due_date' => $this->checkItemDueDate,
                    'task_name' => $this->currentCheckItemName ?? '',
                    'status' => 'incomplete'
                ]);
            }

            $this->cards = $checklistUser->user_checklist;

            $this->reset(['checkItemDueDate']);

            return $this->showNotification(
                true,
                'Task Due Date Updated',
                'Task due date updated successfully.',
                'Failed to update task due date',
                'An error occurred while trying to update the task due date.'
            );
        } catch (\Exception $e) {
            Log::error('Error updating task due date: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->showNotification(
                false,
                'Failed to update task due date',
                'An error occurred while trying to update the task due date.',
                'Failed to update task due date',
                'An error occurred while trying to update the task due date.'
            );
        }
    }

    public function updateTaskUser($cardId, $checklistId, $taskId)
    {
        if (!$cardId || !$checklistId || !$taskId) {
            Log::error('Missing required IDs in updateTaskUser', [
                'cardId' => $cardId,
                'checklistId' => $checklistId,
                'taskId' => $taskId
            ]);
            return $this->showNotification(
                false,
                'Missing Data',
                'Required task information is missing.',
                'Failed to update task assignee',
                'An error occurred while trying to update the task assignee.'
            );
        }

        try {
            Log::info('Updating task assignee', [
                'cardId' => $cardId,
                'checklistId' => $checklistId,
                'taskId' => $taskId,
                'assignedUser' => $this->assignedUser
            ]);

            $checklistUser = ChecklistUser::where('project_id', $this->project->id)->first();
            if (!$checklistUser) {
                throw new \Exception('Checklist not found');
            }

            $checklistUser->user_checklist = array_map(function ($card) use ($cardId, $checklistId, $taskId) {
                if (($card['card_id'] ?? null) == $cardId) {
                    $card['checklists'] = array_map(function ($checklist) use ($checklistId, $taskId) {
                        if (($checklist['checklist_id'] ?? null) == $checklistId) {
                            $checklist['check_items'] = array_map(function ($item) use ($taskId) {
                                if (($item['check_item_id'] ?? null) == $taskId) {
                                    $item['user_id'] = $this->assignedUser;
                                }
                                return $item;
                            }, $checklist['check_items'] ?? []);
                        }
                        return $checklist;
                    }, $card['checklists'] ?? []);
                }
                return $card;
            }, $checklistUser->user_checklist);

            $checklistUser->save();

            $userTask = UserTask::where('check_item_id', $taskId)->first();
            if ($userTask) {
                $userTask->update([
                    'user_id' => $this->assignedUser
                ]);
            } elseif ($this->assignedUser) {
                UserTask::create([
                    'project_id' => $this->project->id,
                    'user_id' => $this->assignedUser,
                    'check_item_id' => $taskId,
                    'card_id' => $cardId,
                    'due_date' => $this->checkItemDueDate,
                    'task_name' => $this->currentCheckItemName ?? '',
                    'status' => 'incomplete'
                ]);
            }

            $this->cards = $checklistUser->user_checklist;

            $this->reset(['assignedUser']);

            return $this->showNotification(
                true,
                'Task Assignee Updated',
                'Task assignee updated successfully.',
                'Failed to update task assignee',
                'An error occurred while trying to update the task assignee.'
            );
        } catch (\Exception $e) {
            Log::error('Error updating task assignee: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->showNotification(
                false,
                'Failed to update task assignee',
                'An error occurred while trying to update the task assignee.',
                'Failed to update task assignee',
                'An error occurred while trying to update the task assignee.'
            );
        }
    }

    public function openStateModal($cardId, $checklistId, $taskId)
    {
        if (!$cardId || !$checklistId || !$taskId) {
            return;
        }

        $this->currentCard = $cardId;
        $this->currentChecklist = $checklistId;
        $this->currentTask = $taskId;
    }

    public function confirmAction()
    {
        if (!$this->currentCard || !$this->currentChecklist || !$this->currentTask) {
            return $this->showNotification(
                false,
                'Missing Data',
                'Required task information is missing.',
                'Failed to update task status',
                'An error occurred while trying to update the task status.'
            );
        }

        try {
            $currentStatus = null;
            $newStatus = null;
            foreach ($this->cards as $card) {
                if (($card['card_id'] ?? null) == $this->currentCard) {
                    foreach ($card['checklists'] ?? [] as $checklist) {
                        if (($checklist['checklist_id'] ?? null) == $this->currentChecklist) {
                            foreach ($checklist['check_items'] ?? [] as $item) {
                                if (($item['check_item_id'] ?? null) == $this->currentTask) {
                                    $currentStatus = strtolower($item['status'] ?? 'incomplete');
                                    $newStatus = $currentStatus === 'pending' ? 'incomplete' : 'pending';
                                    break 3;
                                }
                            }
                        }
                    }
                }
            }

            if (!$currentStatus) {
                throw new \Exception('Could not find current task status');
            }

            $response = $this->trelloTask->setCheckItemState(
                $this->currentCard,
                $this->currentTask,
                'incomplete'
            );

            if (!$response) {
                throw new \Exception('Failed to update task status in Trello');
            }

            $checklistUser = ChecklistUser::where('project_id', $this->project->id)->first();
            if (!$checklistUser) {
                throw new \Exception('Checklist not found');
            }

            $checklistUser->user_checklist = array_map(function ($card) use ($newStatus) {
                if (($card['card_id'] ?? null) == $this->currentCard) {
                    $card['checklists'] = array_map(function ($checklist) use ($newStatus) {
                        if (($checklist['checklist_id'] ?? null) == $this->currentChecklist) {
                            $checklist['check_items'] = array_map(function ($item) use ($newStatus) {
                                if (($item['check_item_id'] ?? null) == $this->currentTask) {
                                    $item['status'] = $newStatus;
                                }
                                return $item;
                            }, $checklist['check_items'] ?? []);
                        }
                        return $checklist;
                    }, $card['checklists'] ?? []);
                }
                return $card;
            }, $checklistUser->user_checklist);

            $checklistUser->save();

            $userTask = UserTask::where('check_item_id', $this->currentTask)->first();
            if ($userTask) {
                $userTask->update([
                    'status' => $newStatus
                ]);
            }

            $coordinators = collect([
                $this->project->head_coordinator,
                $this->project->head_coor_assistant,
                $this->project->groom_coordinator,
                $this->project->bride_coordinator,
                $this->project->groom_coor_assistant,
                $this->project->bride_coor_assistant,
            ]);

            $coordinatorTeams = $this->project->coordinationTeam()->get();

            $coordinationUserIds = $coordinatorTeams
                ->flatMap(function ($team) {
                    return $team->users->pluck('id');
                });

            $coordinators = $coordinators->merge($coordinationUserIds)
                ->filter()
                ->unique()
                ->values();

            foreach ($coordinators as $coordinator) {
                Notification::make()
                    ->title('Task Status Updated')
                    ->body('A Task from "' . $this->project->name . '" has been submitted as Completed and is pending for approval.')
                    ->success()
                    ->sendToDatabase($coordinator);
            }

            $this->cards = $checklistUser->user_checklist;

            $this->reset(['currentCard', 'currentChecklist', 'currentTask']);

            return $this->showNotification(
                true,
                'Task Status Updated',
                'Task status has been updated successfully.',
                'Failed to update task status',
                'An error occurred while trying to update the task status.'
            );
        } catch (\Exception $e) {
            Log::error('Error updating task status: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->showNotification(
                false,
                'Failed to update task status',
                'An error occurred while trying to update the task status.',
                'Failed to update task status',
                'An error occurred while trying to update the task status.'
            );
        }
    }
}
