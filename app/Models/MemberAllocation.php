<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberAllocation extends Model
{
    protected $fillable = [
        'project_id',
        'team_members',
        'user_skills',
    ];

    protected $casts = [
        'team_members' => 'array',
        'user_skills' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function getTeamMembersAttribute($value)
    {
        return json_decode($value, true);
    }
    public function getUserSkillsAttribute($value)
    {
        return json_decode($value, true);
    }
}
