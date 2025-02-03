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

    public $timestamps = false;

    protected $primaryKey = null;

    public $incrementing = false;

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
            
            $checklistItem = $trelloPackage->createChecklistItem($categoryChecklistId, $task->name);
            
            if ($checklistItem) {
                Log::info("Checklist item created successfully: {$task->name}");
                
                Log::info("Updating task_package: ", [
                    'package_id' => $packageTask->package_id,
                    'task_id' => $packageTask->task_id,
                    'trello_checklist_item_id' => $checklistItem['id']
                ]);                
                // Save checklist item ID to the database
                // $packageTask->update(['trello_checklist_item_id' => $checklistItem['id']]);

                // $packageTask->trello_checklist_item_id = $checklistItem['id'];
                // $packageTask->save();        
                
                PackageTask::where('package_id', $packageTask->package_id)
                    ->where('task_id', $packageTask->task_id)
                    ->update([
                        'trello_checklist_item_id' => $checklistItem['id']
                    ]);


                Log::info("Checklist item ID saved to database: {$checklistItem['id']}");
            } else {
                Log::error("Failed to create checklist item: {$task->name}");
            }
        });
    }
    


}
