<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Package;
use App\Models\Department;

class Task extends Model
{
    use HasFactory;

    protected $table = 'package_task_department';

    public $timestamps = false; // Disable timestamps

    protected $fillable = [
        'name',
        'package_id',  
        'department_id'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}

