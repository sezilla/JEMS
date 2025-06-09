<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectProgress;
use App\Events\ProjectProgressUpdate;

class ProjectProgressService
{
    public function updateProgress(
        int $projectId,
        string $status,
        string $message,
        int $progress,
        bool $isCompleted = false,
        bool $hasError = false
    ): ProjectProgress {
        $projectProgress = ProjectProgress::updateOrCreate(
            ['project_id' => $projectId],
            [
                'status' => $status,
                'message' => $message,
                'progress' => $progress,
                'is_completed' => $isCompleted,
                'has_error' => $hasError
            ]
        );

        broadcast(new ProjectProgressUpdate($projectId))->toOthers();

        return $projectProgress;
    }

    public function getProjectProgress(Project $project): array
    {
        $progress = ProjectProgress::where('project_id', $project->id)->first();

        if (!$progress) {
            return [
                'status' => 'not_started',
                'message' => 'Project has not started yet.',
                'progress' => 0,
                'is_completed' => false,
                'has_error' => false
            ];
        }

        return [
            'status' => $progress->status,
            'message' => $progress->message,
            'progress' => $progress->progress,
            'is_completed' => $progress->is_completed,
            'has_error' => $progress->has_error
        ];
    }
}
