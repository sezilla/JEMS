<?php

namespace App\Providers;

use Illuminate\View\View;
use Filament\Facades\Filament;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            'panels::auth.login.form.after',
            fn(): View => view('filament.auth.login')
        );

        Filament::registerRenderHook(
            PanelsRenderHook::TOPBAR_START,
            function (): string {
                $project = request()->route('project') ?? Auth::user()?->currentProject ?? null;

                if ($project) {
                    return Blade::render('<livewire:project-progress-loader :project="$project" />');
                }

                return '';
            },
        );
    }
}
