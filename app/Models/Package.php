<?php

namespace App\Models;

use App\Models\Task;
use App\Models\Department;
use App\Models\PackageTask;
use App\Services\TrelloPackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

                $trelloPackage->createList($boardResponse['id'], 'Departments');
                $trelloPackage->createList($boardResponse['id'], 'Coordinators');
                $projectDetailsList = $trelloPackage->createList($boardResponse['id'], 'Project details');

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
            Log::info('Checking for updated tasks in package: ' . $package->name);
            $trelloPackage = new TrelloPackage();

            foreach ($package->tasks as $task) {
                $department = $task->department;
                if (!$department) {
                    Log::warning('Task ' . $task->name . ' does not have a department. Skipping.');
                    continue;
                }

                if (!$department->trello_card_id) {
                    Log::info('Creating Trello card for department: ' . $department->name);
                    $departmentCard = $trelloPackage->createDepartmentCard($package->trello_board_template_id, $department->name);
                    if ($departmentCard && isset($departmentCard['id'])) {
                        $department->trello_card_id = $departmentCard['id'];
                        $department->save();
                    }
                }

                if ($department->trello_card_id) {
                    Log::info('Adding checklist item for task: ' . $task->name . ' in department card ID: ' . $department->trello_card_id);
                    $checklistItem = $trelloPackage->createChecklistItem($department->trello_card_id, $task->name);

                    if ($checklistItem && isset($checklistItem['id'])) {
                        DB::table('task_package')->where('package_id', $package->id)->where('task_id', $task->id)->update([
                            'trello_checklist_item_id' => $checklistItem['id']
                        ]);
                    }
                }
            }
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
