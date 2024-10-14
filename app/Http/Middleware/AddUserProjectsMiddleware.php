<?php

namespace App\Http\Middleware;

use App\Filament\App\Resources\ProjectResource;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;
use App\Models\Project;
use Filament\Navigation\NavigationItem;

class AddUserProjectsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        if (!Filament::getCurrentPanel()) {
            return $next($request);
        }

        $itemsList = [];

        // Fetch all projects from the database
        $projects = Project::all();

        foreach ($projects as $project) {
            $itemsList[] = NavigationItem::make($project->name)
                ->icon('heroicon-o-document')
                ->group('My Projects') // Ensure this matches the navigation group name
                ->url(ProjectResource::getUrl('edit', ['record' => $project->id]));
        }

        Filament::getCurrentPanel()
            ->navigationItems($itemsList);

        return $next($request);
    }
}
