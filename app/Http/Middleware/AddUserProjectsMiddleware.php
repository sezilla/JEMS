<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Project;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Filament\Navigation\NavigationItem;
use Symfony\Component\HttpFoundation\Response;
use App\Filament\App\Resources\ProjectResource;

class AddUserProjectsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        if (!Filament::getCurrentPanel()) {
            return $next($request);
        }

        $itemsList = [];

        // Fetch projects associated with the authenticated user using the scope
        $user = Auth::user();
        $projects = Project::forUser($user)->where('status', config('project.project_status.active'))->get(); // Use the scopeForUser here

        foreach ($projects as $project) {
            $itemsList[] = NavigationItem::make($project->name)
                ->icon('heroicon-o-document')
                ->group('My Active Projects') // Ensure this matches the navigation group name
                ->url(ProjectResource::getUrl('task', ['record' => $project->id]));
        }

        Filament::getCurrentPanel()
            ->navigationItems($itemsList);

        return $next($request);
    }
}
