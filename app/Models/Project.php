<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'event_date',
        'venue',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (Auth::check()) {
                $project->user_id = Auth::id(); // Set the authenticated user's ID as the user_id
            }
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'project_package', 'project_id', 'package_id');
    }

    public function project()
    {
        return $this->belongsToMany(team::class, 'project_teams', 'project_id', 'team_id');
    }

    public function coordinator()
    {
        return $this->belongsToMany(User::class, 'project_coordinators', 'project_id', 'user_id');
    }

    public function team()
    {
        return $this->belongsToMany(Team::class, 'users_has_teams', 'user_id', 'team_id');
    }
}
