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
        'description'
    ];


    public function leaders()
    {
        return $this->belongsToMany(User::class, 'users_has_teams', 'team_id', 'user_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'users_has_teams', 'team_id', 'user_id');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'departments_has_teams', 'team_id', 'department_id');
    }

}
