<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Example project count, adjust as needed
        $projectCount = DB::table('projects')->count();

        // Query counts for each task status
        // $assignedCount = DB::table('tasks')->where('status', 'assigned')->count();
        // $ongoingCount = DB::table('tasks')->where('status', 'ongoing')->count();
        // $finishedCount = DB::table('tasks')->where('status', 'finished')->count();

        // Apply "No task" label if the count is zero
        // $assignedLabel = $assignedCount > 0 ? $assignedCount : 'No task';
        // $ongoingLabel = $ongoingCount > 0 ? $ongoingCount : 'No task';
        // $finishedLabel = $finishedCount > 0 ? $finishedCount : 'No task';

        return [
            Stat::make('Projects', $projectCount)
                ->description('Total number of Projects')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color('info'),

            // Stat::make('Assigned Tasks', $assignedLabel)
            //     ->description('Tasks that are assigned')
            //     ->descriptionIcon('heroicon-o-user-circle')
            //     ->color('primary'),

            // Stat::make('Ongoing Tasks', $ongoingLabel)
            //     ->description('Tasks that are ongoing')
            //     ->descriptionIcon('heroicon-o-refresh')
            //     ->color('warning'),

            // Stat::make('Finished Tasks', $finishedLabel)
            //     ->description('Tasks that are finished')
            //     ->descriptionIcon('heroicon-o-check-circle')
            //     ->color('success'),
        ];
    }
}
