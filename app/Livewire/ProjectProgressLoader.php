<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ProjectProgressService;

class ProjectProgressLoader extends Component
{
    public $project;
    public $status = 'not_started';
    public $message = 'Initializing...';
    public $progress = 0;
    public $isCompleted = false;
    public $hasError = false;

    protected $listeners = [
        'refreshProgress' => 'loadProgress',
        'echo:project-progress.{project.id},ProjectProgressUpdate' => 'handleProgressUpdate',
    ];

    public function mount($project)
    {
        $this->project = $project;
        $this->loadProgress();
    }

    public function loadProgress()
    {
        $data = app(ProjectProgressService::class)
            ->getProjectProgress($this->project);

        $this->updateProperties($data);
    }

    public function handleProgressUpdate($event)
    {
        // Handle the real-time update when event is broadcast
        $this->loadProgress();
    }

    private function updateProperties($data)
    {
        $this->status = $data['status'];
        $this->message = $data['message'];
        $this->progress = $data['progress'];
        $this->isCompleted = $data['is_completed'];
        $this->hasError = $data['has_error'];
    }

    public function render()
    {
        return view('livewire.project-progress-loader');
    }
}
