<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\ProjectResource;
use App\Models\UserTask;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class UserTaskCalendar extends FullCalendarWidget
{
    public static function canView(): bool
    {
        return request()->routeIs('filament.app.pages.calendar');
    }
    public function fetchEvents(array $fetchInfo): array
    {

        return UserTask::forUser(Auth::user())
            ->whereBetween('due_date', [$fetchInfo['start'], $fetchInfo['end']])
            ->get()
            ->map(
                fn(UserTask $event) => EventData::make()
                    ->id($event->id)
                    ->title($event->task_name ?? 'Untitled Task')
                    ->start(date('Y-m-d', strtotime($event->due_date)))
                    ->backgroundColor($event->project->theme_color ?? 'primary')
                    ->borderColor($event->project->theme_color ?? 'primary')
                    ->end(date('Y-m-d', strtotime($event->due_date)))
                    ->allDay(true)
                    ->url(
                        url: ProjectResource::getUrl(name: 'task', parameters: ['record' => $event->project->id]),
                        shouldOpenUrlInNewTab: false
                    )
            )
            ->toArray();
    }
}
