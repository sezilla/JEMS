<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Services\TrelloService;
use Illuminate\Support\Facades\Log;
// use app\Models\

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'trello_board_id',
        'package_id',
        'description',
        'user_id',
        'event_date',
        'venue',

        'groom_name',
        'bride_name',
        'theme_color',
        'special_request',
        'thumbnail_path',

        'groom_coordinator',
        'bride_coordinator',
        'head_coordinator',
    ];

    //mostly ffor trello....
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (Auth::check()) {
                $project->user_id = Auth::id();
            }
        });

        static::created(function ($project) {
            Log::info('Creating Trello board for project: ' . $project->name);
            $trelloService = new TrelloService();
            $boardResponse = $trelloService->createBoardFromTemplate($project->name);
        
            if ($boardResponse && isset($boardResponse['id'])) {
                $project->trello_board_id = $boardResponse['id']; // Save Trello board ID
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
        
                    // Check if groom coordinator exists
                    if ($project->groom_coordinator) {
                        $createOrUpdateCard($coorList['id'], 'groom coordinator', ['desc' => $project->groomCoordinator->name]);
                    }
        
                    // Check if bride coordinator exists
                    if ($project->bride_coordinator) {
                        $createOrUpdateCard($coorList['id'], 'bride coordinator', ['desc' => $project->brideCoordinator->name]);
                    }
        
                    // Check if head coordinator exists
                    if ($project->head_coordinator) {
                        $createOrUpdateCard($coorList['id'], 'head coordinator', ['desc' => $project->headCoordinator->name]);
                    }
                }
        
                // Handle the "Project details" list
                if ($projectDetailsList) {
                    Log::info('Project details list found.');
                    $coupleCardId = $createOrUpdateCard($projectDetailsList['id'], 'name of couple', 
                    ['desc' => "{$project->groom_name} & {$project->bride_name}", 'due' => $project->event_date]);
                    $createOrUpdateCard($projectDetailsList['id'], 'package', ['desc' => $project->package->name]);
                    $createOrUpdateCard($projectDetailsList['id'], 'description', ['desc' => $project->description]);
                    $createOrUpdateCard($projectDetailsList['id'], 'venue of wedding', ['desc' => $project->venue]);
                    $createOrUpdateCard($projectDetailsList['id'], 'wedding theme color', ['desc' => $project->theme_color]);
                    $createOrUpdateCard($projectDetailsList['id'], 'special request', ['desc' => $project->special_request]);
        
                    // Add the thumbnail photo as a cover if it exists
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
        });
        


        static::updating(function ($project) {
            Log::info('Updating Trello board for project: ' . $project->name);
            $trelloService = new TrelloService();
        
            // Check if the Trello board exists
            if ($project->trello_board_id) {
                Log::info('Trello board found for project: ' . $project->trello_board_id);
        
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
                    Log::info('Updating Coordinator list.');
        
                    // Update groom coordinator if exists
                    if ($project->groom_coordinator) {
                        $createOrUpdateCard($coorList['id'], 'groom coordinator', ['desc' => $project->groomCoordinator->name]);
                    }
        
                    // Update bride coordinator if exists
                    if ($project->bride_coordinator) {
                        $createOrUpdateCard($coorList['id'], 'bride coordinator', ['desc' => $project->brideCoordinator->name]);
                    }
        
                    // Update head coordinator if exists
                    if ($project->head_coordinator) {
                        $createOrUpdateCard($coorList['id'], 'head coordinator', ['desc' => $project->headCoordinator->name]);
                    }
                }
        
                if ($projectDetailsList) {
                    Log::info('Updating Project details list.');
                    $coupleCardId = $createOrUpdateCard($projectDetailsList['id'], 'name of couple', 
                        ['desc' => "{$project->groom_name} & {$project->bride_name}", 'due' => $project->event_date]);
                    $package = $createOrUpdateCard($projectDetailsList['id'], 'package', ['desc' => $project->package->name]);
                    $createOrUpdateCard($projectDetailsList['id'], 'description', ['desc' => $project->description]);
                    $venue = $createOrUpdateCard($projectDetailsList['id'], 'venue of wedding', ['desc' => $project->venue]);
                    $createOrUpdateCard($projectDetailsList['id'], 'wedding theme color', ['desc' => $project->theme_color]);
                    $createOrUpdateCard($projectDetailsList['id'], 'special request', ['desc' => $project->special_request]);
        
                    // Update thumbnail if it exists
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

    public function driversTeam()
    {
        return $this->teams()->whereHas('departments', function ($q) {
            $q->where('name', 'Drivers');
        });
    }

}
