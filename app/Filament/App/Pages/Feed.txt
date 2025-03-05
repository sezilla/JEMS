<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Reply;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class Feed extends Page
{

    // use HasPageShield;
    
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
