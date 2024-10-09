<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Services\TrelloService;
use Illuminate\Support\Facades\Log;

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

                // Get the "Project details" list
                $projectDetailsList = $trelloService->getBoardListByName($project->trello_board_id, 'Project details');

                if ($projectDetailsList) {
                    Log::info('Project details list found.');

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

                    // Update the cards with the project data
                    $createOrUpdateCard($projectDetailsList['id'], 'date', ['due' => $project->event_date]);
                    $createOrUpdateCard($projectDetailsList['id'], 'package', ['name' => $project->package->name]);
                    $createOrUpdateCard($projectDetailsList['id'], 'description', ['desc' => $project->description]);
                    $createOrUpdateCard($projectDetailsList['id'], 'venue of wedding', ['name' => $project->venue]);
                    $coupleCardId = $createOrUpdateCard($projectDetailsList['id'], 'name of couple', [
                        'name' => "{$project->groom_name} & {$project->bride_name}"
                    ]);
                    $createOrUpdateCard($projectDetailsList['id'], 'wedding theme color', ['desc' => $project->theme_color]);
                    $createOrUpdateCard($projectDetailsList['id'], 'special request', ['desc' => $project->special_request]);
                    $createOrUpdateCard($projectDetailsList['id'], 'coordinators', [
                        'desc' => "Groom Coordinator: {$project->groomCoordinator->name}\nBride Coordinator: {$project->brideCoordinator->name}\nHead Coordinator: {$project->headCoordinator->name}"
                    ]);

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
        return $this->belongsToMany(Team::class, 'departments_has_teams', 'department_id', 'team_id');
    }
    



    public function team1()
    {
        return $this->belongsToMany(Team::class, 'project_teams', 'project_id', 'team_id');
    }
    public function team2()
    {
        return $this->belongsToMany(Team::class, 'project_teams', 'project_id', 'team_id');
    }
    public function team3()
    {
        return $this->belongsToMany(Team::class, 'project_teams', 'project_id', 'team_id');
    }
    public function team4()
    {
        return $this->belongsToMany(Team::class, 'project_teams', 'project_id', 'team_id');
    }
    public function team5()
    {
        return $this->belongsToMany(Team::class, 'project_teams', 'project_id', 'team_id');
    }
    public function team6()
    {
        return $this->belongsToMany(Team::class, 'project_teams', 'project_id', 'team_id');
    }



    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // Relationship with coordinators (users)
    // public function coordinators()
    // {
    //     return $this->belongsToMany(User::class, 'project_coordinators', 'project_id', 'head_coordinator', 'groom_coordinator', 'bride_coordinator', 'other_coordinator');
    // }
    // public function groomCoordinator()
    // {
    //     return $this->belongsTo(User::class, 'project_coordinators', 'project_id', 'groom_coordinator');
    // }

    // // Bride coordinator relationship
    // public function brideCoordinator()
    // {
    //     return $this->belongsTo(User::class, 'bride_coordinator');
    // }

    // // Head coordinator relationship
    // public function headCoordinator()
    // {
    //     return $this->belongsTo(User::class, 'head_coordinator');
    // }

    // // Other coordinators (many-to-many relationship)
    // public function otherCoordinators()
    // {
    //     return $this->belongsToMany(User::class, 'project_coordinators', 'project_id', 'user_id');
    // }



    

    // Correct team relationship
    // public function teams()
    // {
    //     return $this->belongsToMany(Team::class, 'project_teams', 'project_id', 'catering', 'hair_and_makeup', 'photo_and_video', 'designing', 'entertainment', 'drivers');
    // }
}
