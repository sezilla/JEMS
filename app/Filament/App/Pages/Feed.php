<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use App\Models\Post;

class Feed extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.feed';

    public $posts;

    public function mount()
    {
        // Load posts with comments, replies, and reactions
        $this->posts = Post::with(['user', 'comments.user', 'comments.replies.user', 'comments.reactions', 'reactions'])->get();
    }

    public function headerActions()
    {
        return [
            
        ];
    }
}
