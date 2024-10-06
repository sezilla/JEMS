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
        'description',
        'event_date',
        'venue',
        'user_id',
        'trello_board_id'
    ];

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with packages for the project
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'project_package', 'project_id', 'package_id');
    }

    // Correct team relationship
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'project_teams', 'project_id', 'team_id');
    }

    // Relationship with coordinators (users)
    public function coordinators()
    {
        return $this->belongsToMany(User::class, 'project_coordinators', 'project_id', 'user_id');
    }
}
