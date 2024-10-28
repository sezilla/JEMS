<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'package_task_department';

    protected $fillable = [
        'name',
    ];

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'package_task_department', 'package_id', 'department_id')
                    ->withPivot('name');
    }
}
