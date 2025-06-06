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
        $this->loadProgress();
    }

    public function loadProgress()
    {
        $cacheKey = "project_{$this->project->id}_progress";

        $this->progress = cache()->remember($cacheKey, now()->addMinutes(1), function () {
            // Eager load the checklist relationship
            $this->project->load('checklist');
            return app(ProjectService::class)->getProjectProgress($this->project);
        });

        $this->loading = false;
    }

    protected $listeners = [
        'refreshProgress' => 'loadProgress',
        'echo:project.{project.id},ProjectProgressUpdated' => 'loadProgress',
    ];

    public function render()
    {
        return view('livewire.project-progress-cards');
    }
}
