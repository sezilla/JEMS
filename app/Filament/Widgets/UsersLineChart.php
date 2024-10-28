<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User; // Make sure to import the User model
use Carbon\Carbon;

class UsersLineChart extends ChartWidget
{
    protected static ?string $heading = 'Users Over Time';

    protected function getData(): array
    {
        // Example: Count users registered per day for the last 30 days
        $usersPerDay = User::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('date')
            ->get();

        // Prepare labels and values for the chart
        $labels = $usersPerDay->pluck('date')->toArray(); // Dates
        $values = $usersPerDay->pluck('count')->toArray(); // User counts

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Users Registered',
                    'data' => $values,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
