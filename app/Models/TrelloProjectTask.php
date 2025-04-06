<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrelloProjectTask extends Model
{
    protected $fillable = [
        'project_id',
        'trello_board_id',
        'trello_board_data',
        'start_date',
        'event_date',
    ];

    protected $casts = [
        'trello_board_data' => 'array',
        'event_date' => 'date',
        'start_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
