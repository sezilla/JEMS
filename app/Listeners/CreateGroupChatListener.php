<?php

namespace App\Listeners;

use Exception;
use App\Models\User;
use App\Services\ProjectService;
use Namu\WireChat\Models\Message;
use Illuminate\Support\Facades\DB;
use App\Events\ProjectCreatedEvent;
use Illuminate\Support\Facades\Log;
use Namu\WireChat\Models\Conversation;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Namu\WireChat\Enums\ParticipantRole;
use Namu\WireChat\Enums\ConversationType;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateGroupChatListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $projectService;

    /**
     * Create the event listener.
     */
    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Handle the event.
     */
    public function handle(ProjectCreatedEvent $event): void
    {
        $project = $event->project;

        $coordinatorIds = collect([
            $project->head_coordinator,
            $project->groom_coordinator,
            $project->bride_coordinator,
            $project->groom_coor_assistant,
            $project->bride_coor_assistant,
            $project->head_coor_assistant,
        ]);

        $coordinationTeams = $project->coordinationTeam()->get();

        $coordinationUserIds = $coordinationTeams
            ->flatMap(function ($team) {
                return $team->users->pluck('id');
            });

        $coordinatorIds = $coordinatorIds
            ->merge($coordinationUserIds)
            ->filter()
            ->unique()
            ->values();


        Log::info('Filtered coordinator IDs', ['ids' => $coordinatorIds->toArray()]);

        if ($coordinatorIds->count() > 1) {
            DB::beginTransaction();

            try {
                Log::info('Attempting to create conversation', [
                    'type' => ConversationType::GROUP,
                    'current_time' => now()
                ]);
                $conversation = Conversation::create([
                    'type' => ConversationType::GROUP,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]);
                $group = $conversation->group()->create([
                    'name' => "{$project->groom_name} & {$project->bride_name} Project Coordinators",
                    'description' => "Coordination group for {$project->groom_name} and {$project->bride_name}'s project"
                ]);
                if ($project->thumbnail_path) {
try {
   $group->cover()->create([
                        'file_path' => $project->thumbnail_path,
                        'file_name' => basename($project->thumbnail_path),
                        'mime_type' => mime_content_type($project->thumbnail_path),
                        'url' => url($project->thumbnail_path)
                    ]);
} catch (Exception $e){
Log:error('failed to create group cover', [
'error' => $e->getMessage(),
]);
}
                 
                } else {
                    Log::warning('Thumbnail file does not exist', ['path' => $project->thumbnail_path]);
                }
                $headCoordinator = $coordinatorIds->first();
                $coordinatorIds->each(function ($userId) use ($conversation, $headCoordinator) {
                    $user = User::find($userId);
                    if ($user) {
                        $role = ($userId == $headCoordinator)
                            ? ParticipantRole::OWNER
                            : ParticipantRole::PARTICIPANT;

                        $conversation->addParticipant($user, $role);
                        Log::info("Added user {$userId} as " . $role->value . " to conversation", ['conversation_id' => $conversation->id]);
                    } else {
                        Log::warning("User ID {$userId} not found.");
                    }
                });
                DB::commit();

                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $headCoordinator,
                    'sendable_type' => User::class,
                    'sendable_id' => $headCoordinator,
                    'body' => "Welcome to the {$group->name} group!",
                    'type' => 'text',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $users = User::whereIn('id', $coordinatorIds)->get();

                Notification::make()
                    ->success()
                    ->title('Group chat Created')
                    ->body('You have been assigned as Coordinator for: ' . $project->name)
                    ->sendToDatabase($users);

                Log::info('Project coordinator group conversation created', [
                    'project_id' => $project->id,
                    'conversation_id' => $conversation->id,
                    'participants' => $coordinatorIds->toArray()
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to create project coordinator group', ['error' => $e->getMessage()]);
            }
        }
    }
}
