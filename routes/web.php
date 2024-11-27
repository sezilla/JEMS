<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamAllocatorController;



Route::get('/', function () {
    return view('welcome');
});

Route::post('/allocate-teams', [TeamAllocatorController::class, 'allocateTeams']);
Route::get('/project-history', [TeamAllocatorController::class, 'getProjectHistory']);


