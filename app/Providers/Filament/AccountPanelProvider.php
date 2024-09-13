<?php

namespace App\Providers\Filament;

use App\Filament\Account\Pages\AccountDashboard;
use App\Filament\Account\Pages\BrowseBooks;
use App\Http\Middleware\AccountMiddleware;
use App\Http\Middleware\RedirectIfNotFilamentAuthenticated;
use App\Livewire\BrowseBookPage;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
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

class AccountPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('account')
            ->path('account')
            ->emailVerification()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->topNavigation()
            ->discoverResources(in: app_path('Filament/Account/Resources'), for: 'App\\Filament\\Account\\Resources')
            ->discoverPages(in: app_path('Filament/Account/Pages'), for: 'App\\Filament\\Account\\Pages')
            ->pages([
                AccountDashboard::class,
                BrowseBooks::class,
            ])
            ->viteTheme('resources/css/filament/account/theme.css')
            ->discoverWidgets(in: app_path('Filament/Account/Widgets'), for: 'App\\Filament\\Account\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('My Profile')
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-o-user'),
            ])
            ->plugins([
                FilamentEditProfilePlugin::make()
                    ->customProfileComponents([
                        \App\Livewire\UserProfile::class,
                    ])
                    ->shouldShowBrowserSessionsForm(false)
                    ->shouldShowEditProfileForm(false)
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldRegisterNavigation(false)
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
            ])
            ->authMiddleware([
                RedirectIfNotFilamentAuthenticated::class,
                AccountMiddleware::class,
            ])
            ->databaseNotifications();
    }
}
