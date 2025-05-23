<?php

namespace App\Filament\App\Widgets;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {
        $user = Auth::user();

        return [
            Stat::make('Projects', app(DashboardService::class)->getProjectCount() ?? 0)
                ->description('Total number of Projects assigned to your team')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color('info'),

            Stat::make('Assigned Tasks', app(DashboardService::class)->getAssignedTasksCount() ?? 0)
                ->description('Tasks that are assigned')
                ->descriptionIcon('heroicon-o-user-circle')
                ->color('primary'),

            Stat::make('Ongoing Tasks', app(DashboardService::class)->getOngoingTasksCount() ?? 0)
                ->description('Tasks that are ongoing')
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('warning'),

            Stat::make('Finished Tasks', app(DashboardService::class)->getFinishedTasksCount() ?? 0)
                ->description('Tasks that are finished')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
