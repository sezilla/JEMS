<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use App\Services\TrelloService;

class TrelloBoards extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string $view = 'filament.app.pages.trello-boards';

    public $boards = [];

    public function mount(TrelloService $trelloService)
    {
        $this->boards = $trelloService->getBoards();
    }
}
