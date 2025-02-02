<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Services\TrelloPackage;
use Illuminate\Support\Facades\Log;
use App\Models\Package;
use App\Models\Task;
use App\Models\Department;

class PackageTask extends Pivot
{
    protected $table = 'task_package';

    protected $fillable = [
        'package_id',
        'task_id',
        'trello_checklist_item_id',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'name', 'department_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'name');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($packageTask) { 
            // Fetch the package name properly
            $package = Package::find($packageTask->package_id);
            $task = Task::find($packageTask->task_id);
            $department = Department::find($task->department_id);

            if ($package) {
                Log::info('Creating Trello task for package: ' . $package->name);
            } else {
                Log::warning('Package not found for ID: ' . $packageTask->package_id);
            }

            // Instantiate TrelloPackage service (if needed)
            $trelloPackage = new TrelloPackage();

            // Check department has a trello card

            // Create Trello card for the department

            // Create Trello checklist for the department card

        });
    }
    
}
