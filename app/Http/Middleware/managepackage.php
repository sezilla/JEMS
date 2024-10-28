<?php

namespace App\Http\Middleware;

use App\Filament\Resources\PackageResource;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;
use App\Models\Package;
use Filament\Navigation\NavigationItem;

class ManagePackage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Filament::getCurrentPanel()) {
            return $next($request);
        }

        $itemList = [];

        $packages = Package::all();

        foreach ($packages as $package) {
            $itemList[] = NavigationItem::make($package->name)
                ->icon('heroicon-o-plus-circle')
                ->group('Project Management')
                ->url(PackageResource::getUrl('task', ['record' => $package->id]));
        }

        Filament::getCurrentPanel()
            ->navigationItems($itemList);

        return $next($request);
    }
}
