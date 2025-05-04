<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Services\TrelloTask;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectTrelloTasks extends Component
{
    public $project;
    public $trelloCards = [];
    public $users = [];
    public $loading = true;
    public $userCheckItem = [];
    public $currentTask = [];

    public function mount($project)
    {
        $this->project = $project;
        $teams = $this->project->teams()->with('users')->get();
        $this->users = $teams->pluck('users')->flatten()->unique('id');

        if ($this->project->checklist) {
            $this->userCheckItem = $this->project->checklist->user_checklist ?? [];
        }
    }

    public function loadTrelloCards()
    {
        try {
            $trelloService = app(TrelloTask::class);
            $departmentsListId = $trelloService->getBoardDepartmentsListId($this->project->trello_board_id);

            if (!$departmentsListId) {
                Log::error("Departments list not found for board: " . $this->project->trello_board_id);
                $this->loading = false;
                return;
            }

            $user = User::find(Auth::id());
            $userDepartment = Department::forUser($user)->first();

            if (!$user->hasAnyRole(['Coordinator', 'Team Leader']) && $userDepartment) {
                $card = $trelloService->getCardByName($departmentsListId, $userDepartment->name);
                $cards = $card ? [$card] : [];
            } else {
                $cards = $trelloService->getListCards($departmentsListId);
            }

            foreach ($cards as &$card) {
                $card['checklists'] = $trelloService->getCardChecklists($card['id']);

                if (is_array($card['checklists'])) {
                    foreach ($card['checklists'] as &$checklist) {
                        $checklist['items'] = $trelloService->getChecklistItems($checklist['id']);

                        foreach ($checklist['items'] as &$item) {
                            $item['user_id'] = null;
                            $item['state'] = $item['state'] ?? 'incomplete';

                            if (isset($this->userCheckItem[$checklist['id']])) {
                                foreach ($this->userCheckItem[$checklist['id']] as $assignment) {
                                    if ($item['id'] === $assignment['check_item_id']) {
                                        $item['user_id'] = $assignment['user_id'];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $this->trelloCards = $cards;
            $this->loading = false;
        } catch (\Exception $e) {
            Log::error('Error loading Trello cards: ' . $e->getMessage());
            $this->loading = false;
        }
    }

    public function setCurrentTask($item)
    {
        $this->currentTask = $item;
        $this->currentTask['project_id'] = $this->project->id;
    }

    public function updateCheckItemState()
    {
        if (!isset($this->currentTask['card_id'], $this->currentTask['item_id'])) {
            return;
        }

        $trelloService = app(TrelloTask::class);
        $desiredState = $this->currentTask['desired_state'] ?? 'incomplete';

        $response = $trelloService->setCheckItemState(
            $this->currentTask['card_id'],
            $this->currentTask['item_id'],
            $desiredState
        );

        if ($response) {
            $this->loadTrelloCards();
        }
    }

    public function assignUserToCheckItem()
    {
        if (!isset($this->currentTask['checklist_id'], $this->currentTask['item_id'], $this->currentTask['user_id'])) {
            return;
        }

        $checklistUser = $this->project->checklist ?? $this->project->checklist()->create([
            'user_checklist' => []
        ]);

        $userChecklist = $checklistUser->user_checklist ?? [];

        if (!isset($userChecklist[$this->currentTask['checklist_id']])) {
            $userChecklist[$this->currentTask['checklist_id']] = [];
        }

        $userChecklist[$this->currentTask['checklist_id']] = array_filter(
            $userChecklist[$this->currentTask['checklist_id']],
            fn($entry) => $entry['check_item_id'] !== $this->currentTask['item_id']
        );

        $userChecklist[$this->currentTask['checklist_id']][] = [
            'user_id' => $this->currentTask['user_id'],
            'check_item_id' => $this->currentTask['item_id']
        ];

        $checklistUser->user_checklist = $userChecklist;
        $checklistUser->save();

        $this->userCheckItem = $userChecklist;
        $this->loadTrelloCards();
    }

    public function saveDueDate()
    {
        if (!isset($this->currentTask['card_id'], $this->currentTask['item_id'], $this->currentTask['due_date'])) {
            return;
        }

        $trelloService = app(TrelloTask::class);
        $response = $trelloService->setCheckItemDueDate(
            $this->currentTask['card_id'],
            $this->currentTask['item_id'],
            $this->currentTask['due_date']
        );

        if ($response) {
            $this->loadTrelloCards();
        }
    }

    public function render()
    {
        return view('livewire.project-trello-tasks');
    }
}
