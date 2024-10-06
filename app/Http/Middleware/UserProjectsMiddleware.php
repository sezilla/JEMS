<?php

namespace App\Http\Middleware;


//put here project resource something idk
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserProjectsMiddleware
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

        if (!filament()->getCurrentPanel()) {
            return $next($request);
        }

        $itemsList = [];

        $projects = auth()->user()->projects;

        foreach ($projects as $project) {
            $itemList[] = NavigationItem::make($project->name)
                ->icon('heroicon-o-document')
                ->group('My Projects')
                ->url(ProjectResource::geturl('edit', ['record' => $project] ));
        }

        filament()->getCurrentPanel()
            ->navigationItems($itemList);

        return $next($request);
    }
}
