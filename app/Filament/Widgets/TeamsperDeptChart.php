<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Team; // Assuming you have a Team model
use App\Models\Department; // Assuming departments are stored in a Department model

class TeamsperDeptChart extends ChartWidget
{
    protected static ?string $heading = 'Teams per Department';

    protected function getData(): array
    {
        // Query to count teams per department
        $teamsPerDept = Team::query()
            ->selectRaw('department_id, COUNT(*) as team_count')
            ->groupBy('department_id')
            ->with('department') // Assuming Team has a 'department' relationship
            ->get();

        // Extract department names and team counts
        $labels = $teamsPerDept->pluck('department.name')->toArray(); // Department names
        $values = $teamsPerDept->pluck('team_count')->toArray(); // Team counts

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Teams per Department',
                    'data' => $values,
                    'backgroundColor' => [ // Optional: add colors for each slice
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
