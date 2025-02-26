<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;


Route::get('/', function () {
    return view('welcome');
});

Route::post('/projects/allocate-teams', [ProjectController::class, 'allocateTeams']);
Route::get('/projects/history', [ProjectController::class, 'getProjectHistory']);


