<?php

namespace App\Models;

use Carbon\Carbon;
use App\Services\PythonService;
use Illuminate\Support\Facades\DB;
use App\Events\ProjectCreatedEvent;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Events\ProjectCreationFailed;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

        'status',
    ];

    protected $casts = [
        'start' => 'date',
        'end' => 'date'
    ];

    protected static function booted(): void
    {
        parent::boot();

        static::creating(function ($project) {
            if (Auth::check()) {
                $project->user_id = Auth::id();
            }

            if ($project->groom_name && $project->bride_name && $project->end) {
                $formattedDate = Carbon::parse($project->end)->format('M d, Y');
                $project->name = "{$project->groom_name} & {$project->bride_name} @ {$formattedDate}";
            }
        });

        static::created(function ($project) {
            Log::info("New project created: {$project->name}");
            Log::info('Now allocating teams for project: ' . $project->name);

            $pythonService = app(PythonService::class);
            DB::beginTransaction();
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
                    throw new \Exception('Team Allocation Failed: ' . $allocatedTeams['error']);
                }

                if (!is_array($allocatedTeams) || empty($allocatedTeams)) {
                    Log::warning('No teams were allocated for this project', ['project_name' => $project->name]);
                    DB::rollBack();
                    throw new \Exception('No valid team allocations received.');
                }

                TeamAllocation::create([
                    'project_id' => $project->id,
                    'package_id' => $project->package_id,
                    'start_date' => $project->start,
                    'end_date' => $project->end,
                    'allocated_teams' => $allocatedTeams,
                ]);

                $project->teams()->sync($allocatedTeams);
                Log::info('Project teams updated successfully', ['teams' => $allocatedTeams]);

                DB::commit();

                event(new ProjectCreatedEvent($project));
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error during team allocation', ['message' => $e->getMessage()]);
                event(new ProjectCreationFailed($project));
            }
        });

        static::deleted(function ($project) {
            Log::info("Project deleted: {$project->name}");
            $project->status = config('project.project_status.archived');
            $project->save();
        });

        static::restored(function ($project) {
            Log::info("Project restored: {$project->name}");
            $project->status = config('project.project_status.active');
            $project->save();
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    // Relationship with coordinators (users)
    public function coordinators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_coordinators', 'project_id', 'user_id');
    }
    public function groomCoordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'groom_coordinator');
    }
    public function brideCoordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bride_coordinator');
    }
    public function headCoordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_coordinator');
    }
    public function headAssistant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_coor_assistant');
    }
    public function groomAssistant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'groom_coor_assistant');
    }
    public function brideAssistant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bride_coor_assistant');
    }

    public function teams(): BelongsToMany
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

    public function checklist()
    {
        return $this->hasOne(ChecklistUser::class);
    }
}
