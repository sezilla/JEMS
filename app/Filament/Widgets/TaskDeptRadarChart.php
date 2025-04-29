<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class TaskDeptRadarChart extends ChartWidget
{
    protected static ?string $heading = 'Task Distribution by Department';

    protected function getData(): array
    {

        $projects = Project::latest()->take(6)->get();

        $datasets = [];

        foreach ($projects as $project) {

            $datasets[] = [
                'label' => $project->name,
                'data' => [
                    rand(70, 100), // Coordination
                    rand(70, 100), // Catering
                    rand(70, 100), // Hair & Makeup
                    rand(70, 100), // Photo & Video
                    rand(70, 100), // Designing
                    rand(70, 100), // Entertainment
                ],
                'backgroundColor' => 'rgba(' . rand(0,255) . ',' . rand(0,255) . ',' . rand(0,255) . ', 0.2)',
                'borderColor' => 'rgba(' . rand(0,255) . ',' . rand(0,255) . ',' . rand(0,255) . ', 1)',
                'borderWidth' => 1,
            ];
        }

        return [
            'labels' => [
                'Coordination',
                'Catering',
                'Hair & Makeup',
                'Photo & Video',
                'Designing',
                'Entertainment',
            ],
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'radar';
    }
}
