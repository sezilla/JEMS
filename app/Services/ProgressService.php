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
            $progressRecord = ProjectProgress::updateOrCreate(
                ['project_id' => $projectId],
                [
                    'progress' => $progress,
                    'status' => $status,
                    'message' => $message,
                    'is_completed' => $progress >= 100,
                    'has_error' => $progress === -2
                ]
            );

            // Clear cache to force fresh data
            Cache::forget("project_progress_{$projectId}");
            
            // Cache the updated progress
            $this->cacheProgress($projectId, $progressRecord);

            // Broadcast the progress update - removed toOthers() to include current user
            broadcast(new ProjectProgressUpdated($projectId, [
                'progress' => $progress,
                'status' => $status,
                'message' => $message,
                'is_completed' => $progress >= 100,
                'has_error' => $progress === -2
            ]));

            Log::info('Progress broadcast sent', [
                'projectId' => $projectId,
                'progress' => $progress,
                'status' => $status,
                'message' => $message
            ]);

            // If progress is 100% or has error, schedule deletion
            if ($progress >= 100 || $progress === -2) {
                $this->scheduleDeletion($projectId);
            }

            return $progressRecord;
        } catch (\Exception $e) {
            Log::error('Error updating progress', [
                'projectId' => $projectId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getProgress($projectId)
    {
        try {
            // Try to get from cache first
            $cachedProgress = Cache::get("project_progress_{$projectId}");
            if ($cachedProgress) {
                return $cachedProgress;
            }

            // If not in cache, get from database
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
            return ProjectProgress::where('project_id', $projectId)->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting progress', [
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
        // Schedule deletion after 5 minutes using Laravel's job dispatch
        dispatch(function () use ($projectId) {
            sleep(300); // 5 minutes
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
            ->get();
    }

    public function getErrorProgress()
    {
        return ProjectProgress::error()
            ->orderBy('created_at', 'desc')
            ->get();
    }
}