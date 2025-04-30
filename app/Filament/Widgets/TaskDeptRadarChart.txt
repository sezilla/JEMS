<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class TaskDeptRadarChart extends ChartWidget
{
    protected static ?string $heading = 'Task Count by Department per Project';

    protected function getData(): array
    {
        $labels = [
            'Coordination',
            'Catering',
            'Hair & Makeup',
            'Photo & Video',
            'Designing',
            'Entertainment',
        ];

        $projects = Project::with('package.tasks.department')->latest()->take(6)->get();

        $presetColors = [
            ['bg' => 'rgba(255, 99, 132, 0.2)',  'border' => 'rgba(255, 99, 132, 1)'],
            ['bg' => 'rgba(54, 162, 235, 0.2)',  'border' => 'rgba(54, 162, 235, 1)'],
            ['bg' => 'rgba(255, 206, 86, 0.2)',  'border' => 'rgba(255, 206, 86, 1)'],
            ['bg' => 'rgba(75, 192, 192, 0.2)',  'border' => 'rgba(75, 192, 192, 1)'],
            ['bg' => 'rgba(153, 102, 255, 0.2)', 'border' => 'rgba(153, 102, 255, 1)'],
            ['bg' => 'rgba(255, 159, 64, 0.2)',  'border' => 'rgba(255, 159, 64, 1)'],
        ];

        $datasets = [];

        foreach ($projects as $index => $project) {
            $taskCounts = array_fill_keys($labels, 0);

            foreach ($project->package?->tasks ?? [] as $task) {
                $deptName = $task->department->name ?? null;

                if (in_array($deptName, $labels)) {
                    $taskCounts[$deptName]++;
                }
            }

            $color = $presetColors[$index % count($presetColors)];

            $datasets[] = [
                'label' => $project->name,
                'data' => array_values($taskCounts),
                'backgroundColor' => $color['bg'],
                'borderColor' => $color['border'],
                'borderWidth' => 1,
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'radar';
    }
}
