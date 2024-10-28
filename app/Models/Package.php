<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Task;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];
    protected $table = 'packages';

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

}
