<?php

namespace App\Services;

use App\Models\ProjectProgress;
use App\Events\ProjectProgressUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProgressService
{
    public function updateProgress($projectId, $progress, $status = 'Processing', $message = '')
    {
        try {
            // Determine completion and error states
            $isCompleted = $progress >= 100;
            $hasError = $progress === -2;

            // Create or update progress record
            $progressRecord = ProjectProgress::updateOrCreate(
                ['project_id' => $projectId],
                [
                    'progress' => $progress,
                    'status' => $status,
                    'message' => $message,
                    'is_completed' => $isCompleted,
                    'has_error' => $hasError
                ]
            );

            // Clear and update cache
            Cache::forget("project_progress_{$projectId}");
            $this->cacheProgress($projectId, $progressRecord);

            // Prepare broadcast data
            $broadcastData = [
                'progress' => $progress,
                'status' => $status,
                'message' => $message,
                'is_completed' => $isCompleted,
                'has_error' => $hasError
            ];

            // Broadcast the progress update
            broadcast(new ProjectProgressUpdated($projectId, $broadcastData));

            Log::info('Progress updated and broadcasted', [
                'projectId' => $projectId,
                'data' => $broadcastData
            ]);

            // Schedule cleanup for completed or error states
            if ($isCompleted || $hasError) {
                $this->scheduleDeletion($projectId);
            }

            return $progressRecord;
        } catch (\Exception $e) {
            Log::error('Error updating progress', [
                'projectId' => $projectId,
                'progress' => $progress,
                'status' => $status,
                'message' => $message,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getProgress($projectId)
    {
        try {
            // Try cache first
            $cachedProgress = Cache::get("project_progress_{$projectId}");
            if ($cachedProgress) {
                return $cachedProgress;
            }

            // Get from database
            $progress = ProjectProgress::where('project_id', $projectId)->first();
            if ($progress) {
                $this->cacheProgress($projectId, $progress);
            }

            return $progress;
        } catch (\Exception $e) {
            Log::error('Error getting progress', [
                'projectId' => $projectId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function deleteProgress($projectId)
    {
        try {
            Cache::forget("project_progress_{$projectId}");
            Cache::forget("delete_progress_{$projectId}");

            $result = ProjectProgress::where('project_id', $projectId)->delete();

            Log::info('Progress deleted', ['projectId' => $projectId]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Error deleting progress', [
                'projectId' => $projectId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function resetProgress($projectId)
    {
        try {
            $this->deleteProgress($projectId);

            broadcast(new ProjectProgressUpdated($projectId, [
                'progress' => 0,
                'status' => 'idle',
                'message' => '',
                'is_completed' => false,
                'has_error' => false
            ]));

            return true;
        } catch (\Exception $e) {
            Log::error('Error resetting progress', [
                'projectId' => $projectId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    protected function cacheProgress($projectId, $progress)
    {
        Cache::put("project_progress_{$projectId}", $progress, now()->addMinutes(30));
    }

    protected function scheduleDeletion($projectId)
    {
        // Use Laravel's job system for cleanup
        dispatch(function () use ($projectId) {
            Log::info('Scheduled deletion executing for project', ['projectId' => $projectId]);
            $this->deleteProgress($projectId);
        })->delay(now()->addMinutes(5));
    }

    public function getActiveProgress()
    {
        return ProjectProgress::active()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getCompletedProgress()
    {
        return ProjectProgress::completed()
            ->orderBy('created_at', 'desc')
            ->take(10) // Limit to recent completions
            ->get();
    }

    public function getErrorProgress()
    {
        return ProjectProgress::error()
            ->orderBy('created_at', 'desc')
            ->take(10) // Limit to recent errors
            ->get();
    }

    /**
     * Update progress with indeterminate state
     */
    public function updateIndeterminateProgress($projectId, $status = 'Processing', $message = '')
    {
        return $this->updateProgress($projectId, -1, $status, $message);
    }

    /**
     * Update progress with error state
     */
    public function updateErrorProgress($projectId, $message = 'An error occurred')
    {
        return $this->updateProgress($projectId, -2, 'Error', $message);
    }
}
