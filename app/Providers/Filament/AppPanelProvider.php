<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use App\Filament\App\Widgets\UserInfo;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use App\Filament\App\Widgets\StatsOverview;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\App\Widgets\ProjectStatsCoorView;
use App\Http\Middleware\AddUserProjectsMiddleware;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

// use App\Filament\pages\Auth\CustomLogin;

class AppPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            // ->brandLogo(asset('storage/images/logo.svg'))
            ->brandName('JEM')
            ->id('app')
            // ->login(CustomLogin::class)
            ->login()
            ->passwordReset()
            ->emailVerification()
            // ->profile()
            ->path('app')
            ->colors([
                'primary' => '#b22d67',

                'secondary' => Color::Slate,
                'info' => Color::Purple,
                'success' => Color::Green,
                'warning' => Color::Orange,
                'danger' => Color::Rose,

                'ruby' => Color::Red,
                'emerald' => Color::Emerald,
                'garnet' => Color::Pink,
                'sapphire' => Color::Sky,
                'infinity' => Color::Cyan,
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
                    ->visible(fn() => optional(Auth::user())->hasAnyRole(['super admin', 'Coordinator', 'Department Admin', 'HR Admin'])),
                'profile' => MenuItem::make()
                    ->label(fn() => Auth::user()->name)
                    ->url(fn(): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle'),
            ])
            ->viteTheme('resources/css/filament/app/theme.css')
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                ProjectStatsCoorView::class,
                StatsOverview::class,
                UserInfo::class,
            ])
            ->viteTheme('resources/css/filament/app/theme.css')
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
            ->databaseNotifications()
            ->plugins([
                FilamentShieldPlugin::make(),

                FilamentFullCalendarPlugin::make(),

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
                    // ->shouldRegisterNavigation(fn() => Auth::check() && Auth::user()->can('view-edit-profile-page'))

                    ->customProfileComponents([
                        \App\Livewire\AddSkills::class,
                    ]),
            ])
            //wirechat
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn(): string => Blade::render('@wirechatStyles'),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn(): string => Blade::render('@wirechatAssets'),
            )

            ->navigationGroups([
                NavigationGroup::make('My Projects')
                    ->collapsible()
                    ->collapsed()
            ])
        ;
    }
}
