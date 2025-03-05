<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Chat extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left';

    protected static string $view = 'filament.app.pages.chats';

    public function getTitle(): string
    {
        return '';
    }
}
