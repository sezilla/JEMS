<?php

namespace App\Filament\App\Resources\ProjectResource\Pages;

use App\Models\Project;
use App\Models\Department;
use App\Services\TrelloTask;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Filament\App\Resources\ProjectResource;

class Task extends Page
{
    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.app.resources.project-resource.pages.task';

    public ?array $trelloCards = null;
    public ?array $tableData = [];
    public ?array $selectedTask = null;
    public bool $showModal = false;

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
                        'card_id'            => $card['id'],
                        'checklist_id'       => $checklist['id'],
                        'item_id'            => $item['id'],
                        'department'         => $card['name'],
                        'due_date'           => $card['due'] ?? null,
                        'checklist'          => $checklist['name'],
                        'task'               => $item['name'],
                        'task_status'        => $item['state'] === 'complete' ? 'complete' : 'incomplete',
                    ];
                }
            }
        }

        return $tableData;
    }

    public function markAsDone($cardId, $checkItemId)
    {
        $trelloTask = app(TrelloTask::class);
        
        foreach ($this->trelloCards as &$card) {
            if ($card['id'] === $cardId) {
                foreach ($card['checklists'] as &$checklist) {
                    foreach ($checklist['items'] as &$item) {
                        if ($item['id'] === $checkItemId) {
                            $newState = $item['state'] === 'complete' ? 'incomplete' : 'complete';
                            $success = $trelloTask->updateChecklistItemState($cardId, $checkItemId, $newState);

                            if ($success) {
                                $item['state'] = $newState;
                                $this->tableData = $this->setTableData();
                            } else {
                                Log::error("Failed to update checklist item state for ID: {$checkItemId}");
                            }
                            return;
                        }
                    }
                }
            }
        }
    }

    public function openModal($task)
    {
        $this->selectedTask = $task;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedTask = null;
    }
}
