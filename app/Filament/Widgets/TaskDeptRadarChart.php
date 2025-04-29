<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class TaskDeptRadarChart extends ChartWidget
{
    protected static ?string $heading = 'Task Distribution by Department';

    protected function getData(): array
    {
        // 🔁 Fetch latest 6 projects (or whatever logic you prefer)
        $projects = Project::latest()->take(6)->get();

        $datasets = [];

        foreach ($projects as $project) {
            // You’ll need to define how to get these 6 department values from your project
            // For now, here's a placeholder. Replace with actual logic (see notes below)
            $datasets[] = [
                'label' => $project->name, // This replaces "Project 1", etc.
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
