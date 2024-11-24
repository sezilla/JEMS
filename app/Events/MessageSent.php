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
        Log::info('MessageSent Event Dispatched', ['message' => $message]);
    }

    public function broadcastWith()
    {
        Log::info('MessageSent event triggered', ['message' => $this->message]);
        return [
            'message' => $this->message->load('user')->toArray(),
        ];
    }

    public function broadcastOn()
    {
        return new Channel('conversation.' . $this->message->conversation_id);
    }
}
