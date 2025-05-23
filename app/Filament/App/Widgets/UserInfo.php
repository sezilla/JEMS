<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\Widget;

class UserInfo extends Widget
{
    protected static string $view = 'filament.app.widgets.dashboard-info';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';
}
