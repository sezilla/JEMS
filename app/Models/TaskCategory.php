<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskCategory extends Model
{
    use HasFactory;

    protected $table = 'task_category';

    protected $fillable = [
        'name',
        'description',
        'slug',
    ];

    protected $unique = [
        'slug',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'task_category_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($taskCategory) {
            if (empty($taskCategory->slug)) {
                $taskCategory->slug = Str::slug($taskCategory->name);
            }
        });
    }
}
