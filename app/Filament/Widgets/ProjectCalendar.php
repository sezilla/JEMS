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
            ->where('start', '>=', $fetchInfo['start'])
            ->where('end', '<=', $fetchInfo['end'])
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
                        shouldOpenUrlInNewTab: true
                    )
            )
            ->toArray();
    }
}
