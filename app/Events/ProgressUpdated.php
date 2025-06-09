<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProjectProgressUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $projectId;
    public $progress;
    public $status;
    public $message;
    public $is_completed;
    public $has_error;

    /**
     * Create a new event instance.
     */
    public function __construct($projectId, array $data)
    {
        $this->projectId = $projectId;
        $this->progress = $data['progress'] ?? 0;
        $this->status = $data['status'] ?? 'Processing';
        $this->message = $data['message'] ?? '';
        $this->is_completed = $data['is_completed'] ?? false;
        $this->has_error = $data['has_error'] ?? false;

        Log::info('ProjectProgressUpdated event created', [
            'projectId' => $this->projectId,
            'progress' => $this->progress,
            'status' => $this->status,
            'message' => $this->message,
            'is_completed' => $this->is_completed,
            'has_error' => $this->has_error
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channel = "project.progress.{$this->projectId}";
        Log::info('Broadcasting ProjectProgressUpdated on channel', ['channel' => $channel]);
        return [new Channel($channel)];
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
            'projectId' => $this->projectId,
            'progress' => $this->progress,
            'status' => $this->status,
            'message' => $this->message,
            'is_completed' => $this->is_completed,
            'has_error' => $this->has_error,
        ];

        Log::info('Broadcasting ProjectProgressUpdated data', $data);
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
