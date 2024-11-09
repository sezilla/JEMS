<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Task;
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
                $trelloPackage->createList($boardResponse['id'], 'Departments');
                $projectDetailsList = $trelloPackage->createList($boardResponse['id'], 'Project djjjetails');

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
    }


















    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_package', 'package_id', 'task_id');
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

    



}
