<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    public $table = 'teams';

    public $fillable = [
        'name',
        'description',
        'image',
        'leader_id',
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function setLeader(User $user)
    {
        $this->leader_id = $user->id;
        $this->save();

        // Optionally set the user's team_id to this team's id
        $user->team_id = $this->id;
        $user->save();
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'departments_has_teams', 'team_id', 'department_id');
    }

    public function members()
    {
        return $this->hasMany(User::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_teams', 'team_id', 'project_id');
    }
}
