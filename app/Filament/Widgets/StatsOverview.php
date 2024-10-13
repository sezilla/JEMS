<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Team;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Fetch counts from the database
        $userCount = User::count();
        $teamCount = Team::count();
        $departmentCount = Department::count();

        // Fetch the number of users registered each day for the past 7 days
        $userRegistrationsLast7Days = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('total') // Get the total column as an array
            ->toArray();

        // If there are fewer than 7 days of data, fill in with zeroes
        $userRegistrationsChart = array_pad($userRegistrationsLast7Days, 7, 0);

        return [
            Stat::make('Users', $userCount)
                ->description('Total number of Users')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
                ])
                ->chart($userRegistrationsChart),

            Stat::make('Teams', $teamCount)
                ->description('Total number of Teams')
                ->descriptionIcon('heroicon-o-squares-plus')
                ->color('success'),

            Stat::make('Departments', $departmentCount)
                ->description('Total number of Departments')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('warning'),
        ];
    }
}
