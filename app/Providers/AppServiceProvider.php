<?php

namespace App\Providers;

use Illuminate\View\View;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Log;

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

        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_START,
            function(): string {
                $projectId = null;
                
                // Get project ID from the current route
                if ($route = request()->route()) {
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

                Log::info('Rendering ProgressLoader in Filament', [
                    'projectId' => $projectId,
                    'route' => $route ? $route->getName() : null
                ]);

                return Blade::render('<livewire:progress-loader :projectId="$projectId" />', ['projectId' => $projectId]);
            }
        );
    }
}
