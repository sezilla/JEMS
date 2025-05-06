<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ProjectService;

class ProjectProgressCards extends Component
{
    public $project;
    public $progress = [];
    public $loading = true;

    public function mount($project)
    {
        $this->project = $project;
    }

    public function loadProgress()
    {
        $this->progress = app(ProjectService::class)->getProjectProgress($this->project);
        $this->loading = false;
    }

    protected $listeners = ['refreshProgress' => 'loadProgress'];

    public function render()
    {
        return view('livewire.project-progress-cards');
    }
}
