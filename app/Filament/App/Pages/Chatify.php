<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class Chatify extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.chatify';

    public $id;
    public $messengerColor; 
    public $dark_mode; // Add this line

    public function mount()
    {
        $this->id = auth()->id();
        $this->messengerColor = config('chatify.messengerColor', '#2184F3');
        $this->dark_mode = config('chatify.dark_mode', false); // Add default value
    }
}
