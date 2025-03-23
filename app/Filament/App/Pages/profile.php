<?php

namespace App\Filament\App\Pages;

use App\Models\Project;
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
    public $projects;

    public function mount()
    {
        $user = Auth::user(); // Get the authenticated user

        $this->projects = Project::where('user_id', $user->id)->get();

        $this->avatar_url = $user->avatar_url ? Storage::url($user->avatar_url) : null;
        $this->name = $user->name;
        $this->role = $user->getRoleNames()->first() ?? 'No Role';
        $this->department = $user->departments->pluck('name')->join(', ') ?? 'No Department';
        $this->team = $user->teams->pluck('name')->join(', ') ?? 'No Team';
    }
}
