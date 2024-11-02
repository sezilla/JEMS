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

    protected $table = 'package_task_department';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'package_id',  
        'department_id',
        'task_category_id'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }
}
