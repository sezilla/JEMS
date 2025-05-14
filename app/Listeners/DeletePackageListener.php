<?php

namespace App\Listeners;

use App\Services\PackageService;
use App\Events\DeletePackageEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Filament\Notifications\Notification;
use App\Models\User;

class DeletePackageListener implements ShouldQueue
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
    public function handle(DeletePackageEvent $event): void
    {
        $package = $event->package;

        try {
            $this->PackageService->deletePackageBoard($package);

            Log::info('Package board deleted for package: ' . $package->name);

            $users = User::role(['Super Admin', 'HR Admin', 'Department Admin'])->get();

            foreach ($users as $user) {
                Notification::make()
                    ->title('Package Deleted')
                    ->body('The package "' . $package->name . '" has been deleted.')
                    ->sendToDatabase($user);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting package board: ' . $e->getMessage(), [
                'package_id' => $package->id ?? null,
                'exception' => $e,
            ]);

            // Notify admins about the error
            $admins = User::role(['Super Admin'])->get();
            foreach ($admins as $admin) {
                Notification::make()
                    ->danger()
                    ->title('Package Board Deletion Failed')
                    ->body("Failed to delete board for package: {$package->name}")
                    ->sendToDatabase($admin);
            }

            // Mark the job as failed but don't stop the queue worker
            $this->fail($e);
        }
    }
}
