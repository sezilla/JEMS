<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Projects', 10) // Static value
                ->description('Total number of Projects')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color('info'),

            Stat::make('Assigned Tasks', 5) // Static value
                ->description('Tasks that are assigned')
                ->descriptionIcon('heroicon-o-user-circle')
                ->color('primary'),

            Stat::make('Ongoing Tasks', 3) // Static value
                ->description('Tasks that are ongoing')
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('warning'),

            Stat::make('Finished Tasks', 7) // Static value
                ->description('Tasks that are finished')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
