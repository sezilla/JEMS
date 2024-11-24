<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id', 'content'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($comment) {
            if (Auth::check()) {
                Log::info('User is authenticated: ' . Auth::id());
                $comment->user_id = Auth::id();
            } else {
                Log::info('No user is authenticated');
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Singular method name for belongsTo relationship
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
}

