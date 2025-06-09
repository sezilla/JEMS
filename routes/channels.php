<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Models\Project;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Updated channel name to match exactly what's being used
Broadcast::channel('project.progress.{projectId}', function ($user, $projectId) {
    Log::info('Authorizing project progress channel', [
        'user_id' => $user->id,
        'project_id' => $projectId,
        'channel' => "project.progress.{$projectId}"
    ]);
    
    // Check if user has access to the project
    $project = Project::find($projectId);
    if (!$project) {
        Log::warning('Project not found for channel authorization', ['project_id' => $projectId]);
        return false;
    }

    // Check if user has any role that should see project progress
    $hasAccess = $user->hasAnyRole(['super admin', 'Department Admin', 'Hr Admin']) || 
                 $project->users()->where('user_id', $user->id)->exists();

    Log::info('Channel authorization result', [
        'user_id' => $user->id,
        'project_id' => $projectId,
        'has_access' => $hasAccess
    ]);

    return $hasAccess;
});