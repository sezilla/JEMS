<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ProjectStats extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $year = now()->year;

        $completedStatus = config('project.project_status.completed');
        $onHoldStatus = config('project.project_status.on_hold');
        $archivedStatus = config('project.project_status.canceled');

        $completedData = $this->getMonthlyCount($completedStatus, $year);
        $onHoldData = $this->getMonthlyCount($onHoldStatus, $year);
        $archivedData = $this->getMonthlyCount($archivedStatus, $year, onlyTrashed: true);

        $completedCount = array_sum($completedData);
        $onHoldCount = array_sum($onHoldData);
        $canceledCount = array_sum($archivedData);

        return [
            Stat::make('Events Completed', $completedCount)
                ->description("Total events completed in $year")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart($completedData),

            Stat::make('Events On Hold', $onHoldCount)
                ->description("Total events on hold in $year")
                ->descriptionIcon('heroicon-o-pause-circle')
                ->color('warning')
                ->chart($onHoldData),

            Stat::make('Events Canceled', $canceledCount)
                ->description("Total events canceled in $year")
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger')
                ->chart($archivedData),
        ];
    }

    protected function getMonthlyCount(string $status, int $year, bool $onlyTrashed = false): array
    {
        $query = Project::query();

        if ($onlyTrashed) {
            $query->onlyTrashed();
        }

        $counts = $query
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('status', $status)
            ->whereYear('created_at', $year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('count', 'month');

        // Fill missing months with 0
        return collect(range(1, 12))->map(function ($month) use ($counts) {
            return $counts[$month] ?? 0;
        })->values()->toArray();
    }
}
