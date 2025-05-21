<?php

namespace App\Models;

use App\Enums\PriorityLevel;
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
        'attachment',
        'priority_level'
    ];

    protected $casts = [
        'due_date' => 'date',
        'attachment' => 'array',
        'priority_level' => PriorityLevel::class,
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeForUser($query, $user)
    {
        if (Auth::check() && optional(Auth::user())->hasRole('Coordinator')) {
            return $query;
        }

        if (Auth::check() && optional(Auth::user())->hasAnyRole(['Team Leader', 'Member'])) {
            $userModel = $user instanceof User ? $user : User::find($user);
            $department = $userModel && $userModel->teams()->exists()
                ? $userModel->teams()->first()->departments()->first()
                : null;
            $departmentName = $department ? $department->name : null;
            return $query->where('card_name', $departmentName);
        }

        return $query->where('card_name', optional($user->team)->department_name);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
