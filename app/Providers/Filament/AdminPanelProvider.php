<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use App\Filament\Pages\Auth\LoginForm;
use App\Http\Middleware\managepackage;
use App\Filament\pages\Auth\CustomLogin;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;

use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
// use Filament\Navigation\NavigationItem;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ->brandLogo(asset('storage/images/logo.svg'))
            // ->defaultThemeMode(ThemeMode::Light)
            ->brandName('JEM')
            ->default()
            ->id('admin')
            ->path('admin')
            ->passwordReset()
            ->emailVerification()
            ->login()
            ->font('Poppins')
            ->colors([
                'primary' => Color::Hex('#f472b6'),
                'secondary' => Color::Slate,

                'info' => Color::Purple,

                'success' => Color::Green,
                'warning' => Color::Orange,
                'danger' => Color::Rose,

                'Catering' => Color::Green,
                'Hair' => Color::Orange,
                'Photo' => Color::Blue,
                'Designing' => Color::Violet,
                'Entertainment' => Color::Yellow,
                'Coordination' => Color::Purple,

                'ruby' => Color::Red,
                'emerald' => Color::Emerald,
                'garnet' => Color::Pink,
                'sapphire' => Color::Sky,
                'infinity' => Color::Cyan,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems([
                MenuItem::make()
                    ->label('App')
                    ->url('/app')
                    ->icon('heroicon-o-cog-8-tooth'),
                'profile' => MenuItem::make()
                    ->label(fn() => auth()->user()->name)
                    ->url(fn(): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,

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

                managepackage::class,
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
            ->authMiddleware([
                Authenticate::class,
            ])
            // ->navigation([
            //     NavigationItem::make('Profile')
            //         ->visible(auth()->user()->can('view-edit-profile-page')),
            // ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Project Management'),
                NavigationGroup::make()
                    ->label('Packages'),
                NavigationGroup::make()
                    ->label('User Management'),
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->databaseNotifications()
            ->databaseNotificationsPolling('2s')
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),

                FilamentEditProfilePlugin::make()
                    // ->slug('profile')
                    ->setTitle('My Profile')
                    ->setNavigationLabel('Profile')
                    ->setIcon('heroicon-o-user')
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars',
                        rules: 'mimes:jpeg,png|max:1024'
                    )
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldRegisterNavigation(fn() => auth()->user()->can('view-edit-profile-page'))
                    ->customProfileComponents([
                        \App\Livewire\AddSkills::class,
                    ]),

                FilamentFullCalendarPlugin::make()
                    ->schedulerLicenseKey('')
                    ->selectable(false)
                    ->editable(false)
                    ->timezone(config('app.timezone'))
                    ->locale(config('app.locale'))
                    ->plugins([
                        'dayGrid',
                        'timeGrid',
                    ])
                    ->config([]),
            ]);
    }
}
