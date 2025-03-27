<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ProjectChart extends ChartWidget
{
    protected static ?string $heading = 'Projects Overview';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Projects per Month',
                    'data' => [5, 10, 15, 20, 25, 30], // Static data for now
                    'backgroundColor' => ['#4F46E5', '#EC4899', '#10B981', '#F59E0B', '#EF4444', '#3B82F6'],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        ];
    }

/*************  ✨ Codeium Command ⭐  *************/
    /**
     * Return the type of chart to render.
     *
     * @return string
     */
/******  140655c1-717b-4822-98f7-beb980c85dfb  *******/
    protected function getType(): string
    {
        return 'bar'; // Change to 'line' or 'pie' if needed
    }
}
