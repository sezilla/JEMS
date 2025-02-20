<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
        Log::info('MessageSent Event Fired', ['message' => $message]);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'body' => $this->message->body,
            'created_at' => $this->message->created_at->toISOString(),
            'user_id' => $this->message->user->id,
            'user' => [
                'id' => $this->message->user->id,
                'name' => $this->message->user->name,
                'avatar' => $this->message->user->getFilamentAvatarUrl() ?? asset('images/default-avatar.png'),
            ],
        ];
    }    

    public function broadcastOn()
    {
        Log::info('Broadcasting to channel: conversation.' . $this->message->conversation_id);
        // return new Channel('conversation.' . $this->message->conversation_id);
        return new PrivateChannel('conversation.' . $this->message->conversation_id);
    }
}
