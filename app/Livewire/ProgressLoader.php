<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

    public function mount()
    {
        $this->projectId = $this->getProjectId();
        $this->userId = Auth::id();
        
        if ($this->projectId) {
            $this->channel = "project.progress.{$this->projectId}";
            
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
        
        // Remove Echo listener since it's not working in Filament
        // Use direct JavaScript communication instead
        $listeners = [
            'progress-updated' => 'handleProgressUpdate',
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

    public function handleProgressUpdate($event)
    {
        Log::info('=== PROGRESS UPDATE RECEIVED ===', [
            'event' => $event,
            'projectId' => $this->projectId,
            'component_id' => $this->getId()
        ]);
        
        // Make component visible when progress starts
        if (!$this->isVisible && isset($event['progress']) && $event['progress'] > 0) {
            $this->isVisible = true;
            Log::info('Making component visible');
        }

        // Update progress data
        $this->progress = $event['progress'] ?? 0;
        $this->status = $event['status'] ?? 'Processing';  
        $this->message = $event['message'] ?? '';
        
        // Handle special progress values
        if ($this->progress === -1) {
            // Indeterminate progress
            $this->progress = null;
            $this->hasError = false;
            $this->isCompleted = false;
        } elseif ($this->progress === -2) {
            // Error state
            $this->hasError = true;
            $this->progress = 0;
            $this->status = 'error';
            $this->isCompleted = false;
        } elseif ($this->progress >= 100) {
            // Completed state
            $this->progress = 100;
            $this->isCompleted = true;
            $this->hasError = false;
            $this->status = 'completed';
            
            // Auto-hide after 3 seconds
            $this->dispatch('auto-hide-progress');
        } else {
            // Normal progress
            $this->progress = max(0, min(100, (int) $this->progress));
            $this->hasError = false;
            $this->isCompleted = false;
        }

        Log::info('=== PROGRESS STATE UPDATED ===', [
            'progress' => $this->progress,
            'status' => $this->status,
            'message' => $this->message,
            'isCompleted' => $this->isCompleted,
            'hasError' => $this->hasError,
            'isVisible' => $this->isVisible
        ]);
        
        // Force component refresh to ensure UI updates
        $this->dispatch('refresh');
    }

    // Public method that can be called directly from JavaScript
    public function updateFromBroadcast($progress, $status, $message = '')
    {
        Log::info('=== UPDATE FROM BROADCAST (DIRECT CALL) ===', [
            'progress' => $progress,
            'status' => $status,
            'message' => $message,
            'projectId' => $this->projectId
        ]);
        
        $this->handleProgressUpdate([
            'progress' => $progress,
            'status' => $status,
            'message' => $message
        ]);
    }

    #[On('hide-progress')]
    public function hideLoader()
    {
        $this->isVisible = false;
        Log::info('Progress loader hidden');
    }

    #[On('show-progress')]  
    public function showLoader()
    {
        $this->isVisible = true;
        Log::info('Progress loader shown');
    }

    #[On('reset-progress')]
    public function resetProgress()
    {
        $this->progress = 0;
        $this->status = 'idle';
        $this->message = '';
        $this->hasError = false;
        $this->isCompleted = false;
        $this->isVisible = false;
        
        Log::info('Progress loader reset');
    }

    public function render()
    {
        return view('livewire.progress-loader', [
            'projectId' => $this->projectId,
            'hasAllowedRole' => $this->hasAllowedRole()
        ]);
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

    protected function hasAllowedRole()
    {
        $allowedRoles = ['super admin', 'Hr Admin', 'Department Admin'];
        return Auth::check() && optional(Auth::user())->hasAnyRole($allowedRoles);
    }
}