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
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function department()
    {
        return $this->hasOneThrough(Department::class, Task::class, 'id', 'id', 'task_id', 'department_id'); // FIXED
    }

    public function category()
    {
        return $this->hasOneThrough(TaskCategory::class, Task::class, 'id', 'id', 'task_id', 'task_category_id'); // FIXED
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($packageTask) { 
            $trelloPackage = new TrelloPackage();

            // Fetch related models using relationships
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

            // Logging package name
            if ($package) {
                Log::info('Creating Trello task for package: ' . $package->name);
            } else {
                Log::warning('Package not found for ID: ' . $packageTask->package_id);
            }

            // Logging department name
            if ($department) {
                Log::info("Department Name: {$department->name}");

                // Check if department Trello card exists
                $existingCard = $trelloPackage->getCardsInDepartmentsList($boardId, $department->name);

                if (!$existingCard) {
                    Log::info("Department card not found in Trello, creating new card: {$department->name}");
                    $createdCard = $trelloPackage->createDepartmentCard($boardId, $department->name);

                    if ($createdCard) {
                        Log::info("Trello department card created successfully: {$department->name}");
                        $departmentCardId = $createdCard['id'];
                        Log::info("Department Card ID: {$departmentCardId}");
                    } else {
                        Log::error("Failed to create Trello department card: {$department->name}");
                    }
                } else {
                    Log::info("Department Trello card already exists: {$department->name}");
                    return;
                }
            } else {
                Log::warning("Department not found for Task ID: {$packageTask->task_id}");
            }

            // Logging task category name
            if ($category && $existingCard) {
                Log::info('Task Category Name: ' . $category->name);
                $existingChecklist = $trelloPackage->getChecklistByName($existingCard['id'], $category->name);
                
                if (!$existingChecklist) {
                    Log::info("Checklist not found in Trello card, creating new checklist: {$category->name}");
                    $createdChecklist = $trelloPackage->createChecklist($existingCard['id'], $category->name);

                    if ($createdChecklist) {
                        Log::info("Trello checklist created successfully: {$category->name}");
                    } else {
                        Log::error("Failed to create Trello checklist: {$category->name}");
                    }
                } else {
                    Log::info("Checklist already exists in Trello card: {$category->name}");
                }
            } else {
                if ($category && !$existingCard) {
                    Log::warning('Department Card was missing, but category exists. Creating a new department card first.');
                    $createdCard = $trelloPackage->createDepartmentCard($boardId, $department->name);
                    if ($createdCard) {
                        Log::info("New department card created: {$department->name}");
                        Log::info("Now creating checklist: {$category->name}");
                        $trelloPackage->createChecklist($createdCard['id'], $category->name);
                    } else {
                        Log::error("Failed to create department card, so checklist cannot be added: {$category->name}");
                    }
                } else {
                    Log::warning('Task Category not found or Department Card missing for Task ID: ' . $packageTask->task_id);
                }
            }

            
            // Logging task name
            if ($task) {
                Log::info('Task Name: ' . $task->name);
            } else {
                Log::warning('Task not found for ID: ' . $packageTask->task_id);
            }

            // Create Trello checklist for the department card

        });
    }
    
}
