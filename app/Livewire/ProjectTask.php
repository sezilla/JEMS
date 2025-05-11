<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;
use App\Models\UserTask;
use App\Services\TrelloTask;
use App\Models\ChecklistUser;

class ProjectTask extends Component
{
    public $project;
    public $card;

    public bool $loading = true;

    public function mount($project)
    {
        $this->project = $project;
        $card = ChecklistUser::where('project_id', $project->id)->first();

        if ($card && is_string($card->user_checklist)) {
            $card->user_checklist = json_decode($card->user_checklist, true);
        }

        $this->card = $card ?: (object) [
            'card_id' => null,
            'card_name' => null,
            'card_due' => null,
            'card_description' => null,
            'user_checklist' => [],
        ];

        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.project-task');
    }
}
