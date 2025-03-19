<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamAllocation extends Model
{
    protected $fillable = [
        'project_id',
        'package_id',
        'start_date',
        'end_date',
        'allocated_teams',
    ];

    protected $casts = [
        'allocated_teams' => 'array',
    ];

    public function getAllocatedTeamsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
