<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Auth\PasswordResetLinkController;

Route::get('/', function () {
    return view('landing');
});

Route::post('/projects/allocate-teams', [ProjectController::class, 'allocateTeams']);
Route::get('/projects/history', [ProjectController::class, 'getProjectHistory']);

Route::post('/tasks/mark-as-done', [TaskController::class, 'markAsDone'])->name('tasks.markAsDone');

Route::get('/login', function () {
    return view('login'); // this shows your Blade login form
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/admin/login', function () {
    return view('admin-login'); // We'll create this view next
})->name('admin.login');

Route::post('/admin/login', [AdminAuthController::class, 'login']);


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
