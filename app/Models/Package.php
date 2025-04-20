<?php

namespace App\Models;

use App\Models\Task;
use App\Models\Department;
use App\Models\PackageTask;
use App\Services\TrelloPackage;
use App\Events\CreatePackageEvent;
use App\Events\DeletePackageEvent;
use App\Events\UpdatePackageEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image',
        'trello_board_template_id',
    ];
    protected $table = 'packages';

    protected static function boot()
    {
        parent::boot();

        static::created(function ($package) {
            CreatePackageEvent::dispatch($package);
        });

        static::deleting(function ($package) {
            DeletePackageEvent::dispatch($package);
        });
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_package', 'package_id', 'task_id')
            ->using(PackageTask::class)
            ->withPivot('trello_checklist_item_id');
    }

    public function packageTasks()
    {
        return $this->hasMany(PackageTask::class);
    }


    public function department()
    {
        return $this->belongsTo(Task::class);
    }
    public function coordination()
    {
        return $this->belongsToMany(Task::class, 'task_package', 'package_id', 'task_id');
    }
    public function catering()
    {
        return $this->belongsToMany(Task::class, 'task_package', 'package_id', 'task_id');
    }
    public function hair_and_makeup()
    {
        return $this->belongsToMany(Task::class, 'task_package', 'package_id', 'task_id');
    }
    public function photo_and_video()
    {
        return $this->belongsToMany(Task::class, 'task_package', 'package_id', 'task_id');
    }
    public function designing()
    {
        return $this->belongsToMany(Task::class, 'task_package', 'package_id', 'task_id');
    }
    public function entertainment()
    {
        return $this->belongsToMany(Task::class, 'task_package', 'package_id', 'task_id');
    }
    public function other()
    {
        return $this->belongsToMany(Task::class, 'task_package', 'package_id', 'task_id');
    }
}
