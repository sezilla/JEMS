<?php

namespace App\Filament\App\Resources\ProjectResource\Pages;

use App\Models\Project;
use Filament\Resources\Pages\Page;
use App\Filament\App\Resources\ProjectResource;
use App\Filament\App\Resources\ProjectResource\Widgets\ProjectTaskTable;

class Task extends Page
{
    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.app.resources.project-resource.pages.task';

    public $project;
    public $showFullDescription = false;
    public $showFullSpecialRequest = false;

    public function mount($record)
    {
        $this->project = Project::find($record);
    }

    public function getFooterWidgets(): array
    {
        return [
            ProjectTaskTable::make([
                'project' => $this->project,
            ]),
        ];
    }

    public function toggleDescription()
    {
        $this->showFullDescription = !$this->showFullDescription;
    }

    public function toggleSpecialRequest()
    {
        $this->showFullSpecialRequest = !$this->showFullSpecialRequest;
    }

    public function getViewData(): array
    {
        return [
            'project' => $this->project,
            'showFullDescription' => $this->showFullDescription,
            'showFullSpecialRequest' => $this->showFullSpecialRequest,
        ];
    }
}
