<?php

namespace App\Http\Controllers;

use App\Models\User;
use Livewire\Livewire;
use App\Models\UserTask;
use App\Services\TrelloTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Filament\App\Resources\ProjectResource\Pages\task;

class TaskActionController extends Controller
{
    public function approveTask(Request $request, $taskId)
    {
        $userTask = UserTask::find($taskId);

        if ($userTask) {
            $userTask->approved_by = Auth::id();
            $userTask->save();

            $user = User::find($userTask->user_id);

            if ($user) {
                Notification::make()
                    ->success()
                    ->title('Task Approved')
                    ->body('The task "' . $userTask->task_name . '" has been approved.')
                    ->sendToDatabase($user);
            }

            return redirect()->back()->with('status', 'Task approved successfully');
        }

        return redirect()->back()->with('error', 'Task not found');
    }

    public function rejectTask(Request $request, $taskId)
    {
        $userTask = UserTask::find($taskId);

        if (!$userTask) {
            return redirect()->back()->with('error', 'Task not found');
        }

        $trelloService = app(TrelloTask::class);
        $state = 'incomplete';

        Log::info("Attempting to reject task with ID {$taskId} (Card ID: {$userTask->card_id}, Check Item ID: {$userTask->check_item_id})");

        $response = $trelloService->setCheckItemState(
            $userTask->card_id,
            $userTask->check_item_id,
            $state
        );

        $success = $response && isset($response['id']);

        if ($success) {
            $userTask->status = $state;
            $userTask->save();

            $user = User::find($userTask->user_id);
            if ($user) {
                Notification::make()
                    ->danger()
                    ->title('Task Rejected')
                    ->body('The task "' . $userTask->task_name . '" was marked as incomplete by a coordinator.')
                    ->sendToDatabase($user);
            }

            Log::info("Task rejected successfully. Task ID: {$taskId}, Status: {$state}");

            return redirect()->back()->with('status', 'Task rejected successfully');
        }

        Log::error("Failed to reject task in Trello. Task ID: {$taskId}, Response: " . json_encode($response));

        return redirect()->back()->with('error', 'Failed to reject task in Trello');
    }
}
