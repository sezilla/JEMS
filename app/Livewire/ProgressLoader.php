<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\ProgressService;
use App\Models\ProjectProgress;
use Livewire\Attributes\On;

class ProgressLoader extends Component
{
    public $isVisible = false;
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

            // Load initial progress from database
            $progressRecord = $this->progressService->getProgress($this->projectId);
            if ($progressRecord) {
                $this->progress = $progressRecord->progress;
                $this->status = $progressRecord->status;
                $this->message = $progressRecord->message;
                $this->isCompleted = $progressRecord->is_completed;
                $this->hasError = $progressRecord->has_error;

                // Show if there's active progress
                $this->isVisible = !$progressRecord->is_completed && !$progressRecord->has_error;
            }

            Log::info('ProgressLoader mounted', [
                'projectId' => $this->projectId,
                'userId' => $this->userId,
                'channel' => $this->channel,
                'initialProgress' => $this->progress
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
        ];
    }

    #[On('handleBroadcastUpdate')]
    public function handleBroadcastUpdate($event)
    {
        Log::info('Received broadcast update', [
            'event' => $event,
            'projectId' => $this->projectId,
            'channel' => $this->channel
        ]);

        // Extract data from event
        $progress = $event['progress'] ?? 0;
        $status = $event['status'] ?? 'Processing';
        $message = $event['message'] ?? '';
        $isCompleted = $event['is_completed'] ?? false;
        $hasError = $event['has_error'] ?? false;

        // Update component state
        $this->updateComponentState($progress, $status, $message, $isCompleted, $hasError);
    }

    protected function updateComponentState($progress, $status, $message, $isCompleted, $hasError)
    {
        Log::info('Updating component state', [
            'progress' => $progress,
            'status' => $status,
            'message' => $message,
            'isCompleted' => $isCompleted,
            'hasError' => $hasError
        ]);

        // Show progress bar if there's actual progress and not completed
        if ($progress > 0 && !$isCompleted && !$hasError) {
            $this->isVisible = true;
        }

        $this->status = $status;
        $this->message = $message;
        $this->hasError = $hasError;
        $this->isCompleted = $isCompleted;

        // Handle special progress values
        if ($progress === -1) {
            // Indeterminate progress
            $this->progress = null;
        } elseif ($progress === -2) {
            // Error state
            $this->hasError = true;
            $this->progress = 0;
            $this->status = 'Error';
            $this->isVisible = true;
        } else {
            // Normal progress (0-100)
            $this->progress = max(0, min(100, (int) $progress));
        }

        // Handle completion
        if ($progress >= 100 && !$this->hasError) {
            $this->isCompleted = true;
            $this->progress = 100;
            $this->status = 'Completed';
            $this->isVisible = true;

            // Auto-hide after 3 seconds
            $this->dispatch('auto-hide-progress');
        }

        Log::info('Final component state', [
            'progress' => $this->progress,
            'status' => $this->status,
            'message' => $this->message,
            'isCompleted' => $this->isCompleted,
            'hasError' => $this->hasError,
            'isVisible' => $this->isVisible
        ]);
    }

    #[On('hide-progress')]
    public function hideLoader()
    {
        $this->isVisible = false;
    }

    #[On('show-progress')]
    public function showLoader()
    {
        $this->isVisible = true;
    }

    #[On('reset-progress')]
    public function resetProgress()
    {
        if ($this->projectId) {
            $this->progressService->deleteProgress($this->projectId);
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
