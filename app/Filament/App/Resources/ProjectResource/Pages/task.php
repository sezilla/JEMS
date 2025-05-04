<?php

namespace App\Filament\App\Resources\ProjectResource\Pages;

use App\Models\User;
use App\Models\Project;
use App\Models\UserTask;
use App\Models\Department;
use App\Services\TrelloTask;
use App\Models\ChecklistUser;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
// use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Actions\Action;
use App\Filament\App\Resources\ProjectResource;
use App\Filament\App\Resources\ProjectResource\Widgets\ProjectDetails;
use App\Filament\App\Resources\ProjectResource\Widgets\ProjectProgress;
use App\Services\ProjectService;

class Task extends Page
{
    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.app.resources.project-resource.pages.task';

    public $project;

    public function mount($record)
    {
        $this->project = Project::find($record);
    }

    public function getViewData(): array
    {
        return [
            'project' => $this->project,
        ];
    }
}
