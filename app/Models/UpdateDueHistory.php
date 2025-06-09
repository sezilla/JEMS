<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdateDueHistory extends Model
{
    protected $table = 'task_due_update_histories';

    protected $fillable = [
        'user_task_id',
        'user_id',
        'old_due_date',
        'new_due_date',
        'remarks',
    ];

    protected $casts = [
        'old_due_date' => 'date',
        'new_due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(UserTask::class, 'user_task_id');
    }
}
