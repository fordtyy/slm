<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Dashboard;
use App\Filament\Admin\Widgets\PendingBorrowRequest;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\RedirectIfNotFilamentAuthenticated;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->emailVerification()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Dashboard::class
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('My Profile')
                    ->url(fn(): string => EditProfilePage::getUrl())
                    ->icon('heroicon-o-user'),
            ])
            ->navigationGroups([
                'Requests',
                'Book Management'
            ])
            ->globalSearch()
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
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
                AdminMiddleware::class,
            ])
            ->databaseNotifications()
            ->viteTheme('resources/css/filament/admin/theme.css');
    }

    public function boot()
    {
        FilamentView::registerRenderHook(
            TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_BEFORE,
            fn() => '<h3 class="fi-ta-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    Pending Borrow Request
                </h3>',
            PendingBorrowRequest::class
        );
    }
}
