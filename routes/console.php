<?php

use App\Models\Project;
use App\Models\UserTask;
use App\Jobs\CheckTaskDue;
use App\Services\TrelloTask;
use App\Services\ProjectTaskService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::job(new CheckTaskDue(new ProjectTaskService(new Project, new TrelloTask, new UserTask)))->dailyAt('00:00');
