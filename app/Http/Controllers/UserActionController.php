<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Filament\Notifications\Notification;

class UserActionController extends Controller
{
    public function clearOldTasks(Request $request)
    {
        $userId = $request->query('user_id');
        $oldTeamId = $request->query('old_team_id');
        if (!$userId || !$oldTeamId) {
            abort(400, 'Missing parameters');
        }
        try {
            app(UserService::class)->clearUserOldTasks($userId, $oldTeamId);
            Notification::make()->success()->title('Old tasks cleared.')->send();
        } catch (\Exception $e) {
            Notification::make()->danger()->title('Failed to clear tasks.')->body($e->getMessage())->send();
        }
        return redirect()->back();
    }
}
