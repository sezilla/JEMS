<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTask extends Model
{
    protected $table = 'user_tasks';

    protected $fillable = [
        'user_id',
        'check_item_id',
        'status',
        'task_name',
        'approved_by',
        'card_id',
        'due_date',
        'project_id',
        'card_name'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
