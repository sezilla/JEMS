<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        // 'event_type_id'
    ];
    protected $table = 'packages';

    // public function attributes()
    // {
    //     return $this->belongsToMany(Attribute::class, 'package_attribute', 'package_id', 'attribute_id');
    // }

    // public function eventType()
    // {
    //     return $this->belongsTo(EventType::class);
    // }

}
