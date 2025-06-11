<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use App\Models\Project;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Http\Controllers\UserActionController;
use App\Filament\App\Resources\ProjectResource;

class UserService
{
    /**
     * Handle the user update logic.
     *
     * @param User $user
     * @return void
     */
    public function handleUserUpdate(User $user): void
    {
        // Check if the user's team has changed
        if ($user->isDirty('team_id')) {
            $this->notifyTeamChange($user);
        }
    }

    /**
     * Notify head coordinators about the team change.
     *
     * @param User $user
     * @return void
     */
    protected function notifyTeamChange(User $user): void
    {
        $oldTeamId = $user->getOriginal('team_id');
        $newTeamId = $user->team_id;
        $projects = Project::with('headCoordinator')->whereHas('teams', function ($query) use ($oldTeamId) {
            $query->where('teams.id', $oldTeamId);
        })->get();
        $oldTeamName = Team::find($oldTeamId)->name ?? 'Unknown Team';
        $newTeamName = Team::find($newTeamId)->name ?? 'Unknown Team';
        $headCoordinators = [];

        foreach ($projects as $project) {
            $coordinator = $project->headCoordinator;
            if ($coordinator) {
                $headCoordinators[$coordinator->id] = [
                    'coordinator' => $coordinator,
                    'project' => $project
                ];
            }
        }

        foreach ($headCoordinators as $coordinatorData) {
            $coordinator = $coordinatorData['coordinator'];
            $project = $coordinatorData['project'];
            
            Notification::make()
                ->title('Team Change Notification')
                ->info()
                ->body("The team for user {$user->name} on event {$project->name} has been changed from team {$oldTeamName} to team {$newTeamName}.")
                ->actions([
                    Action::make('clear')
                        ->label('Clear old Task')
                        ->icon('heroicon-o-arrow-path')
                        ->markAsRead()
                        ->url(route('user.clear-old-tasks', [
                            'user_id' => $user->id, 
                            'old_team_id' => $oldTeamId,
                            'project_id' => $project->id
                        ])),
                    Action::make('keep')
                        ->label('Keep old tasks')
                        ->icon('heroicon-o-check-circle')
                        ->markAsRead(),
                ])
                ->sendToDatabase($coordinator);
        }
    }

    public function clearUserOldTasks(int $userId, int $oldTeamId)
    {
        $oldTeamProjects = Project::whereHas('teams', function ($query) use ($oldTeamId) {
            $query->where('teams.id', $oldTeamId);
        })->get();

        $user = User::find($userId);
        $userTasks = [];
        foreach ($oldTeamProjects as $project) {
            $tasks = $user->tasks()->where('project_id', $project->id)->get();
            foreach ($tasks as $task) {
                $userTasks[] = $task;
                $task->user_id = null;
                $task->save();
            }
        }
    }
}
