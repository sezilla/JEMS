<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Navigation\MenuItem;
use Filament\Navigation\Navigation;
use Filament\Navigation\NavigationItem;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;


class AppPanelProvider extends PanelProvider
{
    
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->login()
            ->path('app')
            ->colors([
                'primary' => '#6366f1',
            ])
            // ->navigationGroups([
            //     NavigationGroup::make('My Projects')
            //         ->collapsible()
            // ])
            // ->topNavigation()
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems([
                MenuItem::make()
                    ->label('Administration')
                    ->url('/admin')
                    ->icon('heroicon-o-cog-8-tooth')
                    ->visible(fn () => auth()->user() && auth()->user()->hasRole(['super_admin', 'Admin'])),
                'profile' => MenuItem::make()
                    ->label(fn () =>auth()->user()->name)
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle'),
            ])

            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,

                // UserProjectsMiddleware::class, enable when working ito ay yung projects per usser somethin idk
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            
            ->plugins([
                FilamentEditProfilePlugin::make()
                    // ->slug('profile')
                    ->setTitle('My Profile')
                    ->setNavigationLabel('Profile')
                    ->setIcon('heroicon-o-user')
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars', 
                    )
                    ->shouldShowDeleteAccountForm(false)
            ])
            // ->plugins([
            //     \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
            // ])
            ;
    }
}
