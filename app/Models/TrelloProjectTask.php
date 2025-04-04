<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrelloProjectTask extends Model
{
    protected $fillable = [
        'project_id',
        'trello_board_id',
        'trello_board_data',
        'event_date',
    ];

    protected $casts = [
        'trello_board_data' => 'array',
        'event_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getTrelloBoardDataAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setTrelloBoardDataAttribute($value)
    {
        $this->attributes['trello_board_data'] = json_encode($value);
    }
}
