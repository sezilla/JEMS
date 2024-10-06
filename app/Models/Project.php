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
                $project->user_id = Auth::id(); // Set the authenticated user's ID
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

    // Other potential relationships or methods can be added here based on your project’s structure.
}
