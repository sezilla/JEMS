<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Data\EventData;
use App\Filament\Resources\ProjectResource;
use App\Models\Project;

class ProjectCalendar extends FullCalendarWidget
{
    protected static ?string $heading = 'Events';
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        // Only show it on custom pages, not the dashboard
        return request()->routeIs('filament.app.pages.calendar');
    }

    public function getConfig(): array
    {
        return [
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'multiMonthYear,dayGridMonth,timeGridDay'
            ],
            'initialView' => 'dayGridMonth',
            'views' => [
                'multiMonthYear' => [
                    'type' => 'multiMonth',
                    'duration' => ['months' => 12],
                    'buttonText' => 'Year',
                    'titleFormat' => ['year' => 'numeric']
                ],
                'dayGridMonth' => [
                    'buttonText' => 'Month',
                    'dayMaxEventRows' => 4
                ],
                'timeGridDay' => [
                    'buttonText' => 'Day'
                ]
            ]
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Project::forUser(Auth::user())
            ->where(function ($query) use ($fetchInfo) {
                $query->where('start', '<=', $fetchInfo['end'])
                    ->where('end', '>=', $fetchInfo['start']);
            })
            ->get()
            ->map(
                fn(Project $event) => EventData::make()
                    ->id($event->id)
                    ->title($event->name)
                    ->start(date('Y-m-d', strtotime($event->start)))
                    ->backgroundColor($event->theme_color)
                    ->borderColor($event->theme_color)
                    ->end(date('Y-m-d', strtotime($event->end)))
                    ->url(
                        url: ProjectResource::getUrl(name: 'task', parameters: ['record' => $event]),
                        shouldOpenUrlInNewTab: false
                    )
            )
            ->toArray();
    }

    public function eventDidMount(): string
    {
        return <<<JS
            function({ event, timeText, isStart, isEnd, isMirror, isPast, isFuture, isToday, el, view }) {
                // Add project-specific styling
                el.classList.add('project-event');
                el.setAttribute("x-tooltip", "tooltip");
                el.setAttribute("x-data", "{ tooltip: 'Project: "+event.title+"' }");
                
                // Add icon for project events
                const icon = document.createElement('span');
                icon.innerHTML = '📅 ';
                icon.style.marginRight = '4px';
                el.querySelector('.fc-event-title').prepend(icon);
            }
        JS;
    }
}