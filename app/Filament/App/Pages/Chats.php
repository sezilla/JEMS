<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;

class Chats extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left';

    protected static string $view = 'filament.app.pages.chats';

    public function getTitle(): string
    {
        return '';
    }
}
