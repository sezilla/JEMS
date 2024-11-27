<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Data\EventData;   
use App\Filament\Resources\ProjectResource;
use App\Models\Project;

class ProjectCalendar extends FullCalendarWidget
{
    public function fetchEvents(array $fetchInfo): array
    {
        return Project::query()
            ->where(function ($query) use ($fetchInfo) {
                $query->where('start', '<=', $fetchInfo['end'])
                      ->where('end', '>=', $fetchInfo['start']);
            })
            ->get()
            ->map(
                fn (Project $event) => EventData::make()
                    ->id($event->id)
                    ->title($event->name)
                    ->start($event->start)
                    ->backgroundColor($event->theme_color)
                    ->borderColor($event->theme_color)
                    ->end($event->end)
                    ->url(
                        url: ProjectResource::getUrl(name: 'view', parameters: ['record' => $event]),
                        shouldOpenUrlInNewTab: false
                    )
            )
            ->toArray();
    }

    public function eventDidMount(): string
    {
        return <<<JS
            function({ event, timeText, isStart, isEnd, isMirror, isPast, isFuture, isToday, el, view }) {
                el.setAttribute("x-tooltip", "tooltip");
                el.setAttribute("x-data", "{ tooltip: '"+event.title+"' }");
            }
        JS;
    }
}
