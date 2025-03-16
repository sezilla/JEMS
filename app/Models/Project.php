<?php

namespace App\Models;

use Carbon\Carbon;
use App\Services\PythonService;
use App\Services\TrelloService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Namu\WireChat\Models\Conversation;
use Namu\WireChat\Models\Group;
use Namu\WireChat\Enums\ConversationType;
use Namu\WireChat\Enums\ParticipantRole;
use Namu\WireChat\Models\Attachment;
use Filament\Notifications\Notification;


// use app\Models\

class Project extends Model
{
    use HasFactory;
    use HasRoles;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'trello_board_id',
        'package_id',
        'description',
        'user_id',
        // 'event_date',
        'venue',

        'groom_name',
        'bride_name',
        'theme_color',
        'special_request',
        'thumbnail_path',

        'groom_coordinator',
        'bride_coordinator',
        'head_coordinator',

        'groom_coor_assistant',
        'bride_coor_assistant',
        'head_coor_assistant',

        'start',
        'end',
    ];

    protected $casts = [
        'start' => 'date',
        'end' => 'date'
    ];

    public function allocateTeams($projectName, $packageId, $start, $end)
    {
        $pythonServiceUrl = env('PYTHON_SERVICE_URL');

        try {
            $response = Http::post("{$pythonServiceUrl}/allocate-teams", [
                'project_name' => $projectName,
                'package_id' => $packageId,
                'start' => $start,
                'end' => $end,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Team allocation response missing allocated teams.', ['error' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('PythonService::allocateTeams Exception', ['message' => $e->getMessage()]);
        }

        return false;
    }



    //mostly ffor trello and fast api....
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (Auth::check()) {
                $project->user_id = Auth::id();
            }

            if ($project->groom_name && $project->bride_name && $project->end) {
                $formattedDate = Carbon::parse($project->end)->format('M d, Y'); // Converts to "Jan 20, 2026"
                $project->name = "{$project->groom_name} & {$project->bride_name} @ {$formattedDate}";
            }
        });

        static::created(function ($project) {

            Log::info('Allocating teams for project: ' . $project->name);

            $pythonService = app(PythonService::class);
            DB::beginTransaction();

            //allocating teams
            try {
                $allocatedTeams = $pythonService->allocateTeams(
                    $project->id,
                    $project->package_id,
                    $project->start,
                    $project->end
                );

                Log::info('PythonService::allocateTeams - Raw Response', ['response' => $allocatedTeams]);

                if (isset($allocatedTeams['error'])) {
                    Log::error('Team Allocation Failed', ['error' => $allocatedTeams['error']]);

                    Notification::make()
                        ->title('Team Allocation Failed')
                        ->body($allocatedTeams['error'])
                        ->danger()
                        ->sendTo(Auth::user());
                    throw new \Exception('Team Allocation Failed: ' . $allocatedTeams['error']);
                }

                if (!isset($allocatedTeams['message']) || strtolower($allocatedTeams['message']) !== 'success') {
                    Log::error('Team Allocation Stopped - Unexpected Message', ['message' => $allocatedTeams['message'] ?? 'No message received']);

                    Notification::make()
                        ->title('Team Allocation Stopped')
                        ->body('Unexpected response message: ' . ($allocatedTeams['message'] ?? 'No message received'))
                        ->danger()
                        ->sendTo(Auth::user());

                    throw new \Exception('Team Allocation Stopped: Unexpected response message.');
                }

                $teamIds = array_map(fn($team) => $team['id'], $allocatedTeams);

                Log::info('Extracted Team IDs', ['team_ids' => $teamIds]);

                if (empty($teamIds)) {
                    Log::warning('No teams were allocated for this project', ['project_name' => $project->name]);

                    Notification::make()
                        ->title('No Teams Allocated')
                        ->body('No teams were allocated for project: ' . $project->name)
                        ->warning()
                        ->sendTo(Auth::user());
                } else {
                    $project->teams()->sync($teamIds);
                    Log::info('Project teams updated successfully', ['teams' => $teamIds]);

                    Notification::make()
                        ->title('Teams Allocated Successfully')
                        ->body('Teams have been assigned to project: ' . $project->name)
                        ->success()
                        ->sendTo(Auth::user());
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error during team allocation', ['message' => $e->getMessage()]);
            }

            //trello board creation
            Log::info('Creating Trello board for project: ' . $project->name);
            $trelloService = new TrelloService();

            $packageName = $project->package->name;
            $boardResponse = $trelloService->createBoardFromTemplate($project->name, $packageName);

            if ($boardResponse && isset($boardResponse['id'])) {
                $project->trello_board_id = $boardResponse['id'];
                $project->save();
                Log::info('Trello board created with ID: ' . $boardResponse['id']);

                // Helper function to create or update card
                $createOrUpdateCard = function ($listId, $cardName, $cardData) use ($trelloService) {
                    $card = $trelloService->getCardByName($listId, $cardName);
                    if (!$card) {
                        Log::info("$cardName card not found, creating new card.");
                        $card = $trelloService->createCardInList($listId, $cardName);
                    }
                    if ($card && isset($card['id'])) {
                        Log::info("Updating $cardName card.");
                        $trelloService->updateCard($card['id'], $cardData);
                        return $card['id'];
                    }
                    return null;
                };

                // Get the "Project details" and "Teams and Members" lists
                $projectDetailsList = $trelloService->getBoardListByName($project->trello_board_id, 'Project details');
                $coorList = $trelloService->getBoardListByName($project->trello_board_id, 'Coordinators');

                if ($coorList) {
                    Log::info('Project Coordinator list found.');

                    if ($project->groom_coordinator) {
                        $createOrUpdateCard($coorList['id'], 'groom coordinator', ['desc' => $project->groomCoordinator->name]);
                    }

                    if ($project->bride_coordinator) {
                        $createOrUpdateCard($coorList['id'], 'bride coordinator', ['desc' => $project->brideCoordinator->name]);
                    }

                    if ($project->head_coordinator) {
                        $createOrUpdateCard($coorList['id'], 'head coordinator', ['desc' => $project->headCoordinator->name]);
                    }
                }

                if ($projectDetailsList) {
                    Log::info('Project details list found.');
                    $coupleCardId = $createOrUpdateCard(
                        $projectDetailsList['id'],
                        'name of couple',
                        ['desc' => "{$project->groom_name} & {$project->bride_name}", 'due' => $project->end]
                    );
                    $createOrUpdateCard($projectDetailsList['id'], 'package', ['desc' => $project->package->name]);
                    $createOrUpdateCard($projectDetailsList['id'], 'description', ['desc' => $project->description]);
                    $createOrUpdateCard($projectDetailsList['id'], 'venue of wedding', ['desc' => $project->venue]);
                    $createOrUpdateCard($projectDetailsList['id'], 'wedding theme color', ['desc' => $project->theme_color]);
                    $createOrUpdateCard($projectDetailsList['id'], 'special request', ['desc' => $project->special_request]);

                    if ($project->thumbnail_path && $coupleCardId) {
                        Log::info('Adding thumbnail as cover to the couple name card.');
                        $trelloService->addAttachmentToCard($coupleCardId, $project->thumbnail_path);
                    }
                } else {
                    Log::error('Project details list not found.');
                }
            } else {
                Log::error('Failed to create Trello board for project: ' . $project->name);
            }




            // Special request handling
            if (!empty($project->special_request)) {
                Log::info('Classifying tasks due to special request', [
                    'project_id' => $project->id,
                    'special_request' => $project->special_request
                ]);

                $classificationResponse = $pythonService->special_request(
                    $project->id,
                    $project->special_request
                );
                Log::info('Task classification response', ['response' => json_encode($classificationResponse)]);

                if (isset($classificationResponse['error'])) {
                    throw new \Exception('Task Classification Error: ' . $classificationResponse['error']);
                }
            }





            // Schedule category prediction
            Log::info('Predicting categories for project: ' . $project->name);
            $categoryPredictions = $pythonService->predictCategories(
                $project->id,
                $project->start->format('Y-m-d'),
                $project->end->format('Y-m-d')
            );

            Log::info('Category prediction response', ['response' => json_encode($categoryPredictions)]);

            if (isset($categoryPredictions['error'])) {
                throw new \Exception('Category Prediction Error: ' . $categoryPredictions['error']);
            }




            // Create a group conversation for project coordinators
            $coordinatorIds = collect([
                $project->groom_coordinator,
                $project->bride_coordinator,
                $project->head_coordinator,
                $project->groom_coor_assistant,
                $project->bride_coor_assistant,
                $project->head_coor_assistant
            ])->filter()->unique();

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
                        $group->cover()->create([
                            'file_path' => $project->thumbnail_path,
                            'file_name' => basename($project->thumbnail_path),
                            'mime_type' => mime_content_type($project->thumbnail_path),
                            'url' => url($project->thumbnail_path)
                        ]);
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
        });

        static::updating(function ($project) {
            Log::info('Updating Trello board for project: ' . $project->name);
            $trelloService = new TrelloService();

            if ($project->trello_board_id) {
                Log::info('Trello board found for project: ' . $project->trello_board_id);

                $createOrUpdateCard = function ($listId, $cardName, $cardData) use ($trelloService) {
                    $card = $trelloService->getCardByName($listId, $cardName);
                    if (!$card) {
                        Log::info("$cardName card not found, creating new card.");
                        $card = $trelloService->createCardInList($listId, $cardName);
                    }
                    if ($card && isset($card['id'])) {
                        Log::info("Updating $cardName card.");
                        $trelloService->updateCard($card['id'], $cardData);
                        return $card['id'];
                    }
                    return null;
                };

                $projectDetailsList = $trelloService->getBoardListByName($project->trello_board_id, 'Project details');
                $coorList = $trelloService->getBoardListByName($project->trello_board_id, 'Coordinators');

                if ($coorList) {
                    Log::info('Updating Coordinator list.');

                    if ($project->groom_coordinator) {
                        $createOrUpdateCard($coorList['id'], 'groom coordinator', ['desc' => $project->groomCoordinator->name]);
                    }

                    if ($project->bride_coordinator) {
                        $createOrUpdateCard($coorList['id'], 'bride coordinator', ['desc' => $project->brideCoordinator->name]);
                    }

                    if ($project->head_coordinator) {
                        $createOrUpdateCard($coorList['id'], 'head coordinator', ['desc' => $project->headCoordinator->name]);
                    }
                }

                if ($projectDetailsList) {
                    Log::info('Updating Project details list.');
                    $coupleCardId = $createOrUpdateCard(
                        $projectDetailsList['id'],
                        'name of couple',
                        ['desc' => "{$project->groom_name} & {$project->bride_name}", 'due' => $project->end]
                    );
                    $package = $createOrUpdateCard($projectDetailsList['id'], 'package', ['desc' => $project->package->name]);
                    $createOrUpdateCard($projectDetailsList['id'], 'description', ['desc' => $project->description]);
                    $venue = $createOrUpdateCard($projectDetailsList['id'], 'venue of wedding', ['desc' => $project->venue]);
                    $createOrUpdateCard($projectDetailsList['id'], 'wedding theme color', ['desc' => $project->theme_color]);
                    $createOrUpdateCard($projectDetailsList['id'], 'special request', ['desc' => $project->special_request]);

                    if ($project->thumbnail_path && $coupleCardId) {
                        Log::info('Updating thumbnail on the couple name card.');
                        $trelloService->addAttachmentToCard($coupleCardId, $project->thumbnail_path);
                    }
                } else {
                    Log::error('Project details list not found.');
                }
            } else {
                Log::error('No Trello board found for project: ' . $project->name);
            }
        });
    }

    public function scopeForUser($query, $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->whereHas('coordinators', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->orWhereHas('groomCoordinator', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->orWhereHas('brideCoordinator', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->orWhereHas('headCoordinator', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })
                ->orWhereHas('teams', function ($query) use ($user) {
                    $query->whereHas('users', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
                });
        })
            ->orWhereHas('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Super Admin']);
            });
    }


    //for database....



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }



    // Relationship with coordinators (users)
    public function coordinators()
    {
        return $this->belongsToMany(User::class, 'project_coordinators', 'project_id', 'user_id');
    }
    public function groomCoordinator()
    {
        return $this->belongsTo(User::class, 'groom_coordinator');
    }
    public function brideCoordinator()
    {
        return $this->belongsTo(User::class, 'bride_coordinator');
    }
    public function headCoordinator()
    {
        return $this->belongsTo(User::class, 'head_coordinator');
    }



    public function teams()
    {
        return $this->belongsToMany(Team::class, 'project_teams', 'project_id', 'team_id');
    }


    public function cateringTeam()
    {
        return $this->teams()->whereHas('departments', function ($q) {
            $q->where('name', 'Catering');
        });
    }

    public function hairAndMakeupTeam()
    {
        return $this->teams()->whereHas('departments', function ($q) {
            $q->where('name', 'Hair and Makeup');
        });
    }

    public function photoAndVideoTeam()
    {
        return $this->teams()->whereHas('departments', function ($q) {
            $q->where('name', 'Photo and Video');
        });
    }

    public function designingTeam()
    {
        return $this->teams()->whereHas('departments', function ($q) {
            $q->where('name', 'Designing');
        });
    }

    public function entertainmentTeam()
    {
        return $this->teams()->whereHas('departments', function ($q) {
            $q->where('name', 'Entertainment');
        });
    }

    public function coordinationTeam()
    {
        return $this->teams()->whereHas('departments', function ($q) {
            $q->where('name', 'Coordination');
        });
    }
}
