<?php

namespace App\Filament\App\Resources\ProjectResource\Widgets;

use App\Models\Project;
use App\Services\ProjectService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;

class ProjectProgress extends Widget
{
    protected static string $view = 'filament.app.resources.project-resource.widgets.project-progress';

    public ?Project $record = null;
    public array $progress = [];

    protected function getViewData(): array
    {
        Log::info('ProjectProgress widget getViewData called', [
            'record_id' => $this->record?->id,
            'record_name' => $this->record?->name
        ]);

        if (!$this->record) {
            Log::error('No record found in ProjectProgress widget');
            return ['progress' => []];
        }

        try {
            $projectService = app(ProjectService::class);
            $progress = $projectService->getProjectProgress($this->record);

            Log::info('ProjectProgress data retrieved', [
                'project_id' => $this->record->id,
                'progress' => $progress
            ]);

            return ['progress' => $progress];
        } catch (\Exception $e) {
            Log::error('Error getting project progress', [
                'project_id' => $this->record->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['progress' => []];
        }
    }
}
