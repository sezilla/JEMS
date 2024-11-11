<?php

namespace App\Filament\App\Pages;
use Illuminate\Contracts\Support\Htmlable;

use Filament\Pages\Page;

class Chat extends Page
{

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.chat';
}
