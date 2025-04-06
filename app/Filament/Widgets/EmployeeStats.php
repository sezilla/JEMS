<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Department;

class EmployeeStats extends ChartWidget
{
    protected static ?string $heading = 'Employee Per Department';

    protected function getData(): array
    {
        $departments = Department::withCount('users')->get();

        return [
           'datasets' => [
            [
                'label' => 'Number of Employees',
                'data' => $departments->pluck('users_count')->toArray(),
                'backgroundColor' => '#f472b6', // pinkish bar
            ],
        ],
        'labels' => $departments->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
