<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\ProjectCalendar;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.calendar';

    protected function getHeaderWidgets(): array
    {
        return [
            ProjectCalendar::class,
        ];
    }
}
