<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\PostCreated;

class Post extends Model
{
    use HasFactory;

    protected $dispatchesEvents = [
        'created' => PostCreated::class,
    ];
    

    protected $fillable = [
        'title',
        'body',
        'user_id',
        'attachment',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (Auth::check()) {
                Log::info('User is authenticated: ' . Auth::id());
                $post->user_id = Auth::id();
            } else {
                Log::info('No user is authenticated');
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function projects()
    {
        return $this->belongsTo(Project::class);
    }
}
