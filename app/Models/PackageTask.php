<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Services\TrelloPackage;
use Illuminate\Support\Facades\Log;
use App\Models\Package;
use App\Models\Task;
use App\Models\Department;

class PackageTask extends Pivot
{
    use HasEvents;

    protected $table = 'task_package';

    public $timestamps = false;

    // protected $primaryKey = null;

    // public $incrementing = false;

    protected $fillable = [
        'name',
        'package_id',
        'task_id',
        'trello_checklist_id',
        'trello_checklist_item_id',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function department()
    {
        return $this->hasOneThrough(
            Department::class,
            Task::class,
            'id', // Task's primary key
            'id', // Department's primary key
            'task_id', // Foreign key in PackageTask
            'department_id' // Foreign key in Task
            //para mas ma gets.
        );
    }

    public function category()
    {
        return $this->hasOneThrough(TaskCategory::class, Task::class, 'id', 'id', 'task_id', 'task_category_id'); // FIXED
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($packageTask) {
            $task = Task::find($packageTask->task_id);
        
            if ($task) {
                $packageTask->name = $task->name;
            }
        });

        static::created(function ($packageTask) {
            $trelloPackage = new TrelloPackage();

            // Fetch related models
            $package = $packageTask->package;
            $task = $packageTask->task;
            $department = $task ? $task->department : null;
            $category = $task ? $task->category : null;

            $boardId = $trelloPackage->getPackageBoardId($package);

            if (!$boardId) {
                Log::error("Trello board ID not found for package: " . ($package->name ?? 'Unknown'));
                return;
            }
            $boardDetails = $trelloPackage->getBoardDetails($boardId);
            if ($boardDetails && !empty($boardDetails['closed']) && $boardDetails['closed'] === true) {
                Log::error("Trello board is closed. Reopen it before proceeding.");
                return;
            }

            if (!$department) {
                Log::warning("Department not found for Task ID: {$packageTask->task_id}");
                return;
            }

            Log::info("Processing department: {$department->name}");

            // Check if department Trello card exists
            $departmentCard = $trelloPackage->getCardsInDepartmentsList($boardId, $department->name);

            if (!$departmentCard) {
                Log::info("Department card not found. Creating new card: {$department->name}");
                $departmentCard = $trelloPackage->createDepartmentCard($boardId, $department->name);

                if (!$departmentCard) {
                    Log::error("Failed to create Trello department card: {$department->name}");
                    return;
                }
            }

            $departmentCardId = $departmentCard['id'];
            Log::info("Department Card ID: {$departmentCardId}");

            if (!$category) {
                Log::warning("Category not found for Task ID: {$packageTask->task_id}");
                return;
            }

            Log::info("Processing category: {$category->name}");

            // Check if checklist exists inside the department card
            $categoryChecklist = $trelloPackage->getChecklistByName($departmentCardId, $category->name);

            if (!$categoryChecklist) {
                Log::info("Checklist not found. Creating new checklist: {$category->name}");
                $categoryChecklist = $trelloPackage->createChecklist($departmentCardId, $category->name);

                if (!$categoryChecklist) {
                    Log::error("Failed to create Trello checklist: {$category->name}");
                    return;
                }
            }

            $categoryChecklistId = $categoryChecklist['id'];
            Log::info("Category Checklist ID: {$categoryChecklistId}");

            if (!$task) {
                Log::warning("Task not found for ID: {$packageTask->task_id}");
                return;
            }

            Log::info("Adding task item: {$task->name} to checklist: {$category->name}");

            $checklistItem = $trelloPackage->createChecklistItem($categoryChecklistId, $packageTask->name);

            if ($checklistItem) {
                Log::info("Checklist item created successfully: {$packageTask->name}");

                Log::info("Updating task_package: ", [
                    'task name' => $packageTask->name,
                    'package_id' => $packageTask->package_id,
                    'task_id' => $packageTask->task_id,
                    'trello_checklist_item_id' => $checklistItem['id'],
                    'trello_checklist_id' => $categoryChecklistId
                ]);
                // Save checklist item ID to the database
                // $packageTask->update(['trello_checklist_item_id' => $checklistItem['id']]);

                // $packageTask->trello_checklist_item_id = $checklistItem['id'];
                // $packageTask->save();        

                PackageTask::where('package_id', $packageTask->package_id)
                    ->where('task_id', $packageTask->task_id)
                    ->update([
                        'trello_checklist_item_id' => $checklistItem['id'],
                        'trello_checklist_id' => $categoryChecklistId
                    ]);


                Log::info("Checklist item ID saved to database: {$checklistItem['id']}");
            } else {
                Log::error("Failed to create checklist item: {$task->name}");
            }
        });

        static::deleted(function ($packageTask) {
            if ($packageTask->package_id && $packageTask->trello_checklist_item_id) {
                $trelloPackage = new TrelloPackage();

                $trelloPackage->deleteChecklistItem($packageTask->trello_checklist_id, $packageTask->trello_checklist_item_id);
            } else {
                Log::warning("Checklist item deletion skipped: Missing package_id or trello_checklist_item_id for PackageTask ID: {$packageTask->id}");
            }
        });


        static::saving(function ($packageTask) {
            // Ensure task_id is changing
            if (!$packageTask->isDirty('task_id')) {
                return;
            }
            $trelloPackage = new TrelloPackage();



            // Update the Trello checklist item
            // $updated = $trelloPackage->updateChecklistItem($checklistItemId, $newTask->name);

            // if ($updated) {
            //     Log::info("Successfully updated Trello checklist item: {$checklistItemId} with new name: {$newTask->name}");
            // } else {
            //     Log::error("Failed to update Trello checklist item: {$checklistItemId}");
            // }
        });
    }
}
