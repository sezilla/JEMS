<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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
        'card_name',
        'attachment'
    ];

    protected $casts = [
        'due_date' => 'date',
        'attachment' => 'array',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeForUser($query, $user)
    {
        if (Auth::check() && Auth::user()->hasRole('Coordinator')) {
            return $query;
        }

        return $query->where('user_id', $user);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
