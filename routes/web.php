<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use Filament\Notifications\Events\DatabaseNotificationsSent;


Route::get('/', function () {
    return view('welcome');
});

Route::post('/projects/allocate-teams', [ProjectController::class, 'allocateTeams']);
Route::get('/projects/history', [ProjectController::class, 'getProjectHistory']);

// Route::get('test', function () {
//     $recipient = auth()->user();
//         \Filament\Notifications\Notification::make()
//         ->title('Test Notification')
//         ->broadcast($recipient);
//     dd('Notification sent');
// })->middleware('auth');


Route::get('test', function () {
    $recipient = auth()->user();
        \Filament\Notifications\Notification::make()
        ->title('Test Notification')
        ->sendToDatabase($recipient);

    event(new DatabaseNotificationsSent($recipient));   
    dd('Notification sent');
})->middleware('auth');


