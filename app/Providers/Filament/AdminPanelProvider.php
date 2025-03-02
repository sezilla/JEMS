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

use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use App\Http\Middleware\managepackage;

use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Enums\ThemeMode;
// use Filament\Navigation\NavigationItem;
use App\Filament\pages\Auth\CustomLogin;

use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ->brandLogo(asset('storage/images/logo.svg'))
            ->defaultThemeMode(ThemeMode::Light)
            ->brandName('JEM')
            ->default()
            ->id('admin')
            ->path('admin')
            ->passwordReset()
            ->emailVerification()
            ->login()
            ->font('Poppins')
            ->colors([
                'primary' => Color::Indigo,
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
                    ->label(fn () =>auth()->user()->name)
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
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
                    ->shouldRegisterNavigation(fn () => auth()->user()->can('view-edit-profile-page'))
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
