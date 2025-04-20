<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Package;
use App\Services\PackageService;
use App\Events\CreatePackageEvent;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreatePackageListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $PackageService;
    /**
     * Create the event listener.
     */
    public function __construct(PackageService $PackageService)
    {
        $this->PackageService = $PackageService;
    }

    /**
     * Handle the event.
     */
    public function handle(CreatePackageEvent $event): void
    {
        $package = $event->package;

        $this->PackageService->createPackageBoard($package);

        $users = User::role(['Super Admin', 'HR Admin', 'Department Admin'])->get();

        foreach ($users as $user) {
            Notification::make()
                ->title('New Package Created')
                ->body("A new package has been created: {$package->name}")
                ->sendToDatabase($user);
        }
    }
}
