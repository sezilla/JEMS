<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskPackage extends Model
{
    use HasFactory;

    protected $table = 'task_package';

    protected $fillable = [
        'task_id',
        'package_id',
        'trello_checklist_item_id'
    ];
}
