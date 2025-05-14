<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Package;
use App\Services\PackageService;
use App\Events\CreatePackageEvent;
use Illuminate\Support\Facades\Log;
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

        try {
            $this->PackageService->createPackageBoard($package);

            $users = User::role(['Super Admin', 'HR Admin', 'Department Admin'])->get();

            foreach ($users as $user) {
                Notification::make()
                    ->title('New Package Created')
                    ->body("A new package has been created: {$package->name}")
                    ->sendToDatabase($user);
            }
        } catch (\Exception $e) {
            Log::error('Error creating package board: ' . $e->getMessage(), [
                'package_id' => $package->id ?? null,
                'exception' => $e,
            ]);

            // Notify admins about the error
            $admins = User::role(['Super Admin'])->get();
            foreach ($admins as $admin) {
                Notification::make()
                    ->danger()
                    ->title('Package Board Creation Failed')
                    ->body("Failed to create board for package: {$package->name}")
                    ->sendToDatabase($admin);
            }

            // Mark the job as failed but don't stop the queue worker
            $this->fail($e);
        }
    }
}
