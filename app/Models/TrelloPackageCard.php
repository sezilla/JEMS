<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrelloPackageCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'package_id',
        'trello_card_id',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
