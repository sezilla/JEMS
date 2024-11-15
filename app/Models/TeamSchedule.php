<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamSchedule extends Model
{
    use HasFactory;

    protected $table = 'team_schedule';

    protected $fillable = [
        'team_id',
        'project_id',
        'task_id',
        'start',
        'end',
    ];
}
