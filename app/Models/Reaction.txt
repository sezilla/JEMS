<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class Reaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id', 'comment_id', 'type'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($reaction) {
            if (Auth::check()) {
                Log::info('User is authenticated: ' . Auth::id());
                $reaction->user_id = Auth::id();
            } else {
                Log::info('No user is authenticated');
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
