<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PublicChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProgressUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $progress;
    public $status;
    public $message;
    public $projectId;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(
        int $progress,
        string $status,
        string $message = '',
        $projectId = null,
        $userId = null
    ) {
        $this->progress = $progress;
        $this->status = $status;
        $this->message = $message;
        $this->projectId = $projectId;
        $this->userId = $userId;

        Log::info('ProgressUpdated event created', [
            'progress' => $this->progress,
            'status' => $this->status,
            'projectId' => $this->projectId,
            'userId' => $this->userId
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        $channel = "project.progress.{$this->projectId}";
        Log::info('Broadcasting on public channel', ['channel' => $channel]);
        return new Channel($channel);
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'ProgressUpdated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $data = [
            'progress' => $this->progress,
            'status' => $this->status,
            'message' => $this->message,
            'projectId' => $this->projectId,
            'userId' => $this->userId,
        ];

        Log::info('Broadcasting data', $data);
        return $data;
    }

    /**
     * Determine if this event should be broadcast immediately.
     */
    public function shouldBroadcast(): bool
    {
        return true;
    }
}