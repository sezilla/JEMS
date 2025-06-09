<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectProgress extends Model
{
    protected $table = 'project_create_progress';

    protected $fillable = [
        'project_id',
        'status',
        'message',
        'progress',
        'is_completed',
        'has_error'
    ];

    protected $casts = [
        'progress' => 'integer',
        'is_completed' => 'boolean',
        'has_error' => 'boolean'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_completed', false)
                    ->where('has_error', false);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeError($query)
    {
        return $query->where('has_error', true);
    }
}
