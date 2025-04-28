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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUserChecklistAttribute($value)
    {
        return json_decode($value, true);
    }

    public function countUserTasks($userId)
    {
        $count = 0;

        $checklistsData = json_decode($this->user_checklist, true);

        foreach ($checklistsData as $checklist) {
            foreach ($checklist as $item) {
                if ($item['user_id'] == $userId) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
