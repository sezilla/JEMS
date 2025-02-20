<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\ShouldBroadcast;

class UserTyping implements ShouldBroadcast
{
    public $user;
    public $conversation_id;

    /**
     * Create a new event instance.
     */
    public function __construct($user, $conversation_id)
    {
        $this->user = $user;
        $this->conversation_id = $conversation_id;
    }

    public function broadcastWith()
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->getFilamentAvatarUrl() ?? asset('images/default-avatar.png'),
            ],
        ];
    }

    public function broadcastOn()
    {
        // return new Channel('conversation.' . $this->conversation_id);
        return new PrivateChannel('conversation.' . $this->conversation_id);
    }
}
