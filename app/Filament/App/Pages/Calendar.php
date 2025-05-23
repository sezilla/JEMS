<?php

namespace App\Filament\App\Pages;

use App\Models\UserTask;
use Filament\Pages\Page;
use App\Filament\App\Widgets\ProjectCalendar;
use App\Filament\App\Widgets\UserTaskCalendar;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.calendar';

    protected function getHeaderWidgets(): array
    {
        return [
            UserTaskCalendar::class,
            ProjectCalendar::class,
        ];
    }
}
