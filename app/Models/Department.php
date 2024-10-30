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
}
