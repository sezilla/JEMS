<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistUser extends Model
{
    protected $fillable = [
        'project_id',
        'user_checklist',
    ];

    protected $casts = [
        'user_checklist' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUserChecklistAttribute($value)
    {
        return json_decode($value, true);
    }
}
