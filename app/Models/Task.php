<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Package;
use App\Models\Department;
use App\Models\TaskCategory;

class Task extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'duration',
        'department_id',
        'task_category_id',
    ];

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'task_package', 'task_id', 'package_id')
                    ->using(TaskPackage::class)
                    ->withPivot('trello_checklist_item_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    public function tasks()
    {
        return $this->belongsTo(Task::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'task_skills', 'task_id', 'skill_id');
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

}
