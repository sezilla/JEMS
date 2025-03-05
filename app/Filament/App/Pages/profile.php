<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.profile';

    public $avatar_url;
    public $name;
    public $role;
    public $department;
    public $team;

    public function mount()
    {
        $user = Auth::user(); // Get the currently authenticated user

        $this->avatar_url = $user->avatar_url ? Storage::url($user->avatar_url) : null;
        $this->name = $user->name;
        $this->role = $user->getRoleNames()->first() ?? 'No Role';
        $this->department = $user->departments->pluck('name')->join(', ') ?? 'No Department';
        $this->team = $user->teams->pluck('name')->join(', ') ?? 'No Team';
    }
}
