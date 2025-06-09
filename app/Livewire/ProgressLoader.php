<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\ProgressService;
use App\Models\ProjectProgress;
use App\Events\ProjectProgressUpdated;
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

    protected $progressService;

    public function boot(ProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    public function mount()
    {
        $this->projectId = $this->getProjectId();
        $this->userId = Auth::id();
        
        if ($this->projectId) {
            $this->channel = "project.progress.{$this->projectId}";
            $this->isVisible = true;
            
            // Load initial progress from database
            $progressRecord = $this->progressService->getProgress($this->projectId);
            if ($progressRecord) {
                $this->progress = $progressRecord->progress;
                $this->status = $progressRecord->status;
                $this->message = $progressRecord->message;
                $this->isCompleted = $progressRecord->is_completed;
                $this->hasError = $progressRecord->has_error;
            }
            
            Log::info('ProgressLoader mounted', [
                'projectId' => $this->projectId,
                'userId' => $this->userId,
                'channel' => $this->channel
            ]);
        }
    }

    public function getListeners()
    {
        if (!$this->projectId) {
            return [];
        }
        
        return [
            "echo:project.progress.{$this->projectId},ProgressUpdated" => 'handleBroadcastUpdate',
            'progress-updated' => 'updateProgress',
            'hide-progress' => 'hideLoader',
            'show-progress' => 'showLoader',
            'reset-progress' => 'resetProgress'
        ];
    }

    public function handleBroadcastUpdate($event)
    {
        Log::info('Received broadcast update', [
            'event' => $event,
            'projectId' => $this->projectId,
            'channel' => $this->channel
        ]);

        // Update local state directly from broadcast event
        $this->updateProgress([
            'progress' => $event['progress'] ?? 0,
            'status' => $event['status'] ?? 'idle',
            'message' => $event['message'] ?? '',
            'is_completed' => $event['is_completed'] ?? false,
            'has_error' => $event['has_error'] ?? false
        ]);
    }

    protected function updateProgress($data)
    {
        Log::info('Updating progress - Input data', ['data' => $data]);
        
        $progress = $data['progress'] ?? 0;
        $status = $data['status'] ?? 'idle';
        $message = $data['message'] ?? '';
        $isCompleted = $data['is_completed'] ?? false;
        $hasError = $data['has_error'] ?? false;

        Log::info('Parsed progress values', [
            'raw_progress' => $progress,
            'status' => $status,
            'message' => $message,
            'isCompleted' => $isCompleted,
            'hasError' => $hasError
        ]);

        if (!$this->isVisible && $progress > 0) {
            $this->isVisible = true;
        }

        $this->status = $status;
        $this->message = $message;
        $this->hasError = $hasError;
        $this->isCompleted = $isCompleted;

        if ($progress === -1) {
            $this->progress = null;
        } elseif ($progress === -2) {
            $this->hasError = true;
            $this->progress = 0;
            $this->status = 'error';
        } else {
            $this->progress = max(0, min(100, (int) $progress));
        }

        Log::info('Final progress state', [
            'progress' => $this->progress,
            'status' => $this->status,
            'message' => $this->message,
            'isCompleted' => $this->isCompleted,
            'hasError' => $this->hasError,
            'isVisible' => $this->isVisible
        ]);

        if ($progress >= 100 && !$this->hasError) {
            $this->isCompleted = true;
            $this->progress = 100;
            $this->status = 'completed';
            $this->dispatch('auto-hide-progress');
        }

        // Force component re-render
        $this->dispatch('$refresh');
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
        if ($this->projectId) {
            $this->progressService->deleteProgress($this->projectId);
            cache()->forget("project_progress_{$this->projectId}");
        }
        
        $this->progress = 0;
        $this->status = 'idle';
        $this->message = '';
        $this->hasError = false;
        $this->isCompleted = false;
        $this->isVisible = false;
    }

    public function render()
    {
        // Only refresh from database if no recent updates
        if ($this->projectId && !$this->isCompleted && !$this->hasError) {
            $progressRecord = $this->progressService->getProgress($this->projectId);
            if ($progressRecord && $progressRecord->updated_at > now()->subSeconds(5)) {
                Log::info('Loading progress from database', [
                    'progress' => $progressRecord->progress,
                    'status' => $progressRecord->status,
                    'message' => $progressRecord->message
                ]);
                
                $this->progress = $progressRecord->progress;
                $this->status = $progressRecord->status;
                $this->message = $progressRecord->message;
                $this->isCompleted = $progressRecord->is_completed;
                $this->hasError = $progressRecord->has_error;
            }
        }

        Log::info('Rendering ProgressLoader', [
            'projectId' => $this->projectId,
            'progress' => $this->progress,
            'status' => $this->status,
            'message' => $this->message,
            'isVisible' => $this->isVisible,
            'isCompleted' => $this->isCompleted,
            'hasError' => $this->hasError
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