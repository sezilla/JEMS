<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTeamUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user whose team was updated.
     */
    public $user;

    /**
     * The old team ID before update.
     */
    public $oldTeamId;

    /**
     * Create a new event instance.
     */
    public function __construct($user, $oldTeamId)
    {
        $this->user = $user;
        $this->oldTeamId = $oldTeamId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
