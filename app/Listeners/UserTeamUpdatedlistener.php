<?php

namespace App\Listeners;

use App\Events\UserTeamUpdated;
use App\Services\UserService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserTeamUpdatedlistener
{
    protected $userService;

    /**
     * Create the event listener.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserTeamUpdated $event): void
    {
        // Set the old team id on the user model so UserService can use it
        $event->user->setOriginal('team_id', $event->oldTeamId);
        $this->userService->handleUserUpdate($event->user);
    }
}
