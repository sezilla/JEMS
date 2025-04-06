<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'description',
        'image',
    ];

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'departments_has_teams', 'department_id', 'team_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_has_departments', 'department_id', 'user_id');
    }

    public function scopeForUser($query, $user)
    {
        // For example, if the user has a department_id field:
        return $query->where('id', $user->department_id);
    }

    // Existing scope for department name:
    public function scopeForUserByName($query, $user)
    {
        return $query->where('name', $user->department->name);
    }
}
