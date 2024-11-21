<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Task;
use App\Models\Department; 
// use App\Models\TaskPackage;
use Illuminate\Support\Facades\Log;

use App\Services\TrelloPackage;

class Package extends Model
{
    use HasFactory;

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
            Log::info('Creating Trello board for project: ' . $package->name);
            $trelloPackage = new TrelloPackage();

            // Create Trello board for the new package
            $boardResponse = $trelloPackage->createPackageBoard($package->name);

            if ($boardResponse && isset($boardResponse['id'])) {
                $package->trello_board_template_id = $boardResponse['id']; 
                $package->save();
                Log::info('Trello board created with ID: ' . $boardResponse['id']);

                // Create "Departments" list on the Trello board
                $trelloPackage->createList($boardResponse['id'], 'Coordinators');
                $departmentsList = $trelloPackage->createList($boardResponse['id'], 'Departments');
                $projectDetailsList = $trelloPackage->createList($boardResponse['id'], 'Project details');

                if ($departmentsList && isset($departmentsList['id'])) {
                    // Fetch all departments
                    $departments = Department::all();
            
                    // Create a card for each department in the "Departments" list
                    foreach ($departments as $department) {
                        $cardResponse = $trelloPackage->createCard($departmentsList['id'], $department->name);
    
                        if ($cardResponse && isset($cardResponse['id'])) {
                            // Create a single checklist for the department card
                            $checklistResponse = $trelloPackage->createChecklist($cardResponse['id'], 'Department Tasks'); // Create a checklist titled "Department Tasks"
    
                            if ($checklistResponse && isset($checklistResponse['id'])) {
                                $checklistId = $checklistResponse['id'];
    
                                // Add each task as a checklist item to that checklist
                                $tasks = $department->tasks; // Assuming you have a relationship between Department and Task
                                
                                foreach ($tasks as $task) {
                                    // Add checklist item for each task in the created checklist
                                    $checklistItem = $trelloPackage->createChecklistItem($checklistId, $task->name);
        
                                    if ($checklistItem && isset($checklistItem['id'])) {
                                        // Save checklist item ID to the pivot table (task_package)
                                        $taskPackage = TaskPackage::where('task_id', $task->id)
                                                                  ->where('package_id', $package->id)
                                                                  ->first();
                                        if ($taskPackage) {
                                            $taskPackage->trello_checklist_item_id = $checklistItem['id'];
                                            $taskPackage->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($projectDetailsList && isset($projectDetailsList['id'])) {
                    $trelloPackage->createCard($projectDetailsList['id'], 'name of couple');
                    $trelloPackage->createCard($projectDetailsList['id'], 'package');
                    $trelloPackage->createCard($projectDetailsList['id'], 'description');
                    $trelloPackage->createCard($projectDetailsList['id'], 'special request');
                    $trelloPackage->createCard($projectDetailsList['id'], 'venue of wedding');
                    $trelloPackage->createCard($projectDetailsList['id'], 'wedding theme color');
                }
            }
        });

        static::updated(function ($package) {
            // Code to handle updates if needed
        });

        static::deleting(function ($package) {
            // Code to handle deletion if needed
        });
    }


    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_package', 'package_id', 'task_id')
                    ->withPivot('trello_checklist_item_id');
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
