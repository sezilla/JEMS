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

                // Step 1: Get the "Project details" list
                $projectDetailsList = $trelloService->getBoardListByName($project->trello_board_id, 'Project details');
                
                if ($projectDetailsList) {
                    Log::info('Project details list found.');

                    // Step 2: Find or create the "date" card
                    $dateCard = $trelloService->getCardByName($projectDetailsList['id'], 'date');
                    if (!$dateCard) {
                        Log::info('Date card not found, creating new card.');
                        $dateCard = $trelloService->createCardInList($projectDetailsList['id'], 'date');
                    }

                    // Step 3: Update the "date" card with the event date (due date)
                    if ($dateCard && isset($dateCard['id'])) {
                        Log::info('Updating date card with event date as due date.');
                        $trelloService->updateCard($dateCard['id'], [
                            'due' => $project->event_date, // Setting the due date
                        ]);
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
