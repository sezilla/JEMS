<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\MessageSent;

class Message extends Model
{
    protected $fillable = [
        'body',
        'user_id', 
        'conversation_id'
        ];

    protected static function booted()
    {
        // Trigger an event whenever a message is created
        static::created(function ($message) {
            broadcast(new MessageSent($message))->toOthers();
        });
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

