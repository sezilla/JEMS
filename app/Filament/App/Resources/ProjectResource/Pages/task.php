<?php

namespace App\Filament\App\Resources\ProjectResource\Pages;

use App\Filament\App\Resources\ProjectResource;
use Filament\Resources\Pages\Page;
use App\Models\Project;

class Task extends Page
{
    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.app.resources.project-resource.pages.task';

    public $record; // Declare a property for the record ID

    public function mount($record)
    {
        $this->record = $record; // Assign the record ID to the property
    }

    public function getProject()
    {
        return Project::find($this->record);
    }
}
