<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class ProgressLoader extends Component
{
    public $isVisible = true;
    public $progress = 0;
    public $status = 'idle';
    public $message = '';
    public $projectId = null;
    public $userId = null;
    public $isCompleted = false;
    public $hasError = false;
    public $channel = null;

    public function mount()
    {
        $this->projectId = $this->getProjectId();
        $this->userId = Auth::id();
        
        if ($this->projectId) {
            $this->channel = "project.progress.{$this->projectId}";
            $this->isVisible = true;
            
            Log::info('ProgressLoader mounted', [
                'projectId' => $this->projectId,
                'userId' => $this->userId,
                'channel' => $this->channel,
                'listener' => "echo:{$this->channel},ProgressUpdated"
            ]);
        }
    }

    public function getListeners()
    {
        if (!$this->projectId) {
            return [];
        }
        
        $listeners = [
            "echo:{$this->channel},ProgressUpdated" => 'handleBroadcastUpdate',
            'progress-updated' => 'updateProgress',
            'hide-progress' => 'hideLoader',
            'show-progress' => 'showLoader',
            'reset-progress' => 'resetProgress'
        ];

        Log::info('ProgressLoader listeners configured', [
            'projectId' => $this->projectId,
            'channel' => $this->channel,
            'listeners' => array_keys($listeners)
        ]);

        return $listeners;
    }

    public function handleBroadcastUpdate($event)
    {
        Log::info('Received broadcast update', [
            'event' => $event,
            'projectId' => $this->projectId,
            'channel' => $this->channel
        ]);
        
        if (!isset($event['progress'])) {
            Log::warning('Broadcast update missing progress', ['event' => $event]);
            return;
        }

        $this->updateProgress([
            'progress' => $event['progress'],
            'status' => $event['status'] ?? 'Processing',
            'message' => $event['message'] ?? ''
        ]);
    }

    protected function updateProgress($data)
    {
        Log::info('Updating progress', ['data' => $data]);
        
        $progress = $data['progress'] ?? 0;
        $status = $data['status'] ?? 'idle';
        $message = $data['message'] ?? '';

        if (!$this->isVisible && $progress > 0) {
            $this->isVisible = true;
        }

        $this->status = $status;
        $this->message = $message;
        $this->hasError = false;
        $this->isCompleted = false;

        if ($progress === -1) {
            $this->progress = null;
        } elseif ($progress === -2) {
            $this->hasError = true;
            $this->progress = 0;
            $this->status = 'error';
        } else {
            $this->progress = max(0, min(100, (int) $progress));
        }

        if ($progress >= 100 && !$this->hasError) {
            $this->isCompleted = true;
            $this->progress = 100;
            $this->status = 'completed';
            $this->dispatch('auto-hide-progress');
        }

        Log::info('Progress updated', [
            'progress' => $this->progress,
            'status' => $this->status,
            'message' => $this->message,
            'isCompleted' => $this->isCompleted,
            'hasError' => $this->hasError
        ]);
    }

    public function hideLoader()
    {
        $this->isVisible = false;
    }

    public function showLoader()
    {
        $this->isVisible = true;
    }

    public function resetProgress()
    {
        $this->progress = 0;
        $this->status = 'idle';
        $this->message = '';
        $this->hasError = false;
        $this->isCompleted = false;
        $this->isVisible = false;
    }

    public function render()
    {
        Log::info('Rendering ProgressLoader', [
            'projectId' => $this->projectId,
            'progress' => $this->progress,
            'status' => $this->status,
            'message' => $this->message,
            'isVisible' => $this->isVisible
        ]);

        return view('livewire.progress-loader');
    }

    protected function getProjectId()
    {
        // First check if we're in a Filament context
        if (class_exists('Filament\Facades\Filament')) {
            $currentRoute = request()->route();
            if ($currentRoute) {
                // Check for project ID in route parameters
                if ($projectId = $currentRoute->parameter('project')) {
                    return $projectId;
                }
                // Check for record ID in route parameters
                if ($recordId = $currentRoute->parameter('record')) {
                    return $recordId;
                }
            }
        }

        // Check query parameters
        if ($projectId = request()->query('project_id')) {
            return $projectId;
        }

        // Check route parameters
        if ($projectId = request()->route('project')) {
            return $projectId;
        }

        return null;
    }
}