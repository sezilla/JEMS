<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\TaskActionController;
use App\Http\Controllers\UserActionController;
use App\Http\Controllers\ProjectReportController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Filament\Facades\Filament;

// Add broadcasting routes
Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    return Broadcast::auth($request);
})->middleware(['web', 'auth']);

Route::post('/admin/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    return Broadcast::auth($request);
})->middleware(['web', 'auth']);

Route::post('/admin/projects/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    return Broadcast::auth($request);
})->middleware(['web', 'auth']);

Route::post('/admin/projects/{projectId}/broadcasting/auth', function (\Illuminate\Http\Request $request, $projectId) {
    return Broadcast::auth($request);
})->middleware(['web', 'auth']);

Route::get('/', function () {
    return view('welcomepage-final');
});

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::post('/projects/allocate-teams', [ProjectController::class, 'allocateTeams']);
Route::get('/projects/history', [ProjectController::class, 'getProjectHistory']);

// Route::get('/login', function () {
//     return view('login'); // this shows your Blade login form
// })->name('login');
// Route::post('/login', [AuthController::class, 'login'])->name('login');

// Route::get('/admin/login', function () {
//     return view('admin-login'); // We'll create this view next
// })->name('admin-login');

// Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');

Route::get('/projects/{id}/export-pdf', [App\Http\Controllers\ProjectController::class, 'exportPdf'])->name('projects.exportPdf');


Route::get('/projects/report/download', [ProjectReportController::class, 'download'])->name('projects.report.download')->middleware(['auth']);
Route::get('/projects/report/download-reports', [ProjectReportController::class, 'downloadReports'])->name('projects.report.downloadReports')->middleware(['auth']);

Route::get('/task/approve/{taskId}', [TaskActionController::class, 'approveTask'])->name('task.approve');
Route::get('/task/reject/{taskId}', [TaskActionController::class, 'rejectTask'])->name('task.reject');


Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/home');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/resend', function () {
        request()->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Add route for clearing old tasks via notification action
Route::get('/user/clear-old-tasks', [UserActionController::class, 'clearOldTasks'])
    ->name('user.clear-old-tasks')
    ->middleware('auth');

// Test route for broadcasting
Route::get('/test-broadcast', function () {
    Log::info('Dispatching test broadcast event');
    
    $projectId = request()->query('project_id');
    $route = request()->route();
    
    if (!$projectId && $route) {
        // Check for project ID in route parameters
        $project = $route->parameter('project');
        if ($project) {
            $projectId = is_object($project) ? $project->id : $project;
        }
        // Check for record ID in Filament resource routes
        else {
            $record = $route->parameter('record');
            if ($record) {
                $projectId = is_object($record) ? $record->id : $record;
            }
        }
    }

    Log::info('Using project ID for test broadcast', [
        'projectId' => $projectId,
        'route' => $route ? $route->getName() : null,
        'query' => request()->query()
    ]);
    
    if (!$projectId) {
        return 'No project ID found. Please provide a project_id in the URL or visit a project page.';
    }
    
    $event = new App\Events\ProgressUpdated(50, 'Testing', 'Testing broadcast', $projectId, 1);
    event($event);
    Log::info('Test broadcast event dispatched');
    return 'Event dispatched! Check the logs for details.';
});
