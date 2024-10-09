<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Team;
use App\Models\Department;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Fetch counts from the database
        $userCount = User::count();
        $teamCount = Team::count();
        $departmentCount = Department::count();

        return [
            Stat::make('Users', $userCount)
                ->description('Total number of Users')
                ->descriptionIcon('heroicon-o-users') // Add heroicon
                ->color('primary'), // Customize the color

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
