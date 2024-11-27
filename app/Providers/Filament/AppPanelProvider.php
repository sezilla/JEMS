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
use Filament\Navigation\NavigationGroup;
use App\Http\Middleware\AddUserProjectsMiddleware;


class AppPanelProvider extends PanelProvider
{
    
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ->brandLogo(asset('storage/images/logo.svg'))
            ->brandName('JEM')
            ->id('app')
            ->login()
            ->path('app')
            ->colors([
                'primary' => '#6366f1',
            ])
            
            // ->topNavigation([
            //     navigation::make('dashboard'),
            //     navigation::make('trello boards'),
            //     navigation::make('profile'),
            // ])
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems([
                MenuItem::make()
                    ->label('Administration')
                    ->url('/admin')
                    ->icon('heroicon-o-cog-8-tooth')
                    ->visible(fn () => auth()->user() && auth()->user()->hasRole(['super_admin', 'Admin', 'Coordinator'])),
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

                AddUserProjectsMiddleware::class, 
                // enable when working ito ay yung projects per usser somethin idk
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            
            ->plugins([
                // \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),

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
                    ->shouldRegisterNavigation(fn () => auth()->user()->can('view-edit-profile-page'))
                    ->customProfileComponents([
                        \App\Livewire\AddSkills::class,
                    ]),
            ])
     
                ->navigationGroups([
                    NavigationGroup::make('My Projects')
                        ->collapsible()
                        ->collapsed()
                ])
            ;
    }
}
