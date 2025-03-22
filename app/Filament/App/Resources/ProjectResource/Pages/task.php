<?php

namespace App\Filament\App\Resources\ProjectResource\Pages;

use App\Models\Project;
use App\Services\TrelloTask;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Log;
use App\Filament\App\Resources\ProjectResource;

class Task extends Page
{
    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.app.resources.project-resource.pages.task';

    public ?array $trelloCards = null;
    public ?array $tableData = [];

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

        // Get the Departments list ID for the board
        $listId = $trelloService->getBoardDepartmentsListId($boardId);
        if (!$listId) {
            Log::error("Departments list not found for board: " . $boardId);
            return [];
        }

        // Retrieve all cards from the Departments list
        $cards = $trelloService->getListCards($listId);
        if (!is_array($cards)) {
            Log::error("No cards found for list ID: " . $listId);
            return [];
        }

        // For each card, attach its checklists and checklist items
        foreach ($cards as &$card) {
            $card['checklists'] = $trelloService->getCardChecklists($card['id']);

            // Fetch checklist items for each checklist
            if (is_array($card['checklists'])) {
                foreach ($card['checklists'] as &$checklist) {
                    $checklist['items'] = $trelloService->getChecklistItems($checklist['id']);
                }
            }
        }

        // Store the data for use in the view
        $this->trelloCards = $cards;
    }

    public function setTableData()
    {
        $tableData = [];

        if (!$this->trelloCards) {
            return $tableData;
        }

        foreach ($this->trelloCards as $card) {
            // Card represents the department
            $departmentName = $card['name'];
            $departmentDueDate = $card['due'] ?? null;

            // Loop through each checklist in the department card
            if (!empty($card['checklists'])) {
                foreach ($card['checklists'] as $checklist) {
                    $checklistName = $checklist['name'];

                    // Loop through each checklist item (task)
                    if (!empty($checklist['items'])) {
                        foreach ($checklist['items'] as $item) {
                            $taskName = $item['name'];
                            $taskStatus = isset($item['state']) && strcasecmp($item['state'], 'complete') === 0
                                ? 'complete'
                                : 'incomplete';

                            $tableData[] = [
                                'department'          => $departmentName,
                                'department_due_date' => $departmentDueDate,
                                'checklist'           => $checklistName,
                                'task'                => $taskName,
                                'task_status'         => $taskStatus,
                            ];
                        }
                    }
                }
            }
        }

        return $tableData;
    }
}
