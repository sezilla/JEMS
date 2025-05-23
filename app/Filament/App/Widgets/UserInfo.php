<?php

namespace App\Filament\App\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class UserInfo extends Widget
{
    protected static string $view = 'filament.app.widgets.dashboard-info';

    public User $user;

    public function getViewData(): array
    {
        return [
            'user' => Auth::user(),
        ];
    }
}
