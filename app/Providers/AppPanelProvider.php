<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('')
            ->topNavigation()
            ->topbar(true)
            ->spa()
            ->databaseNotifications()
            ->databaseTransactions()
            ->defaultThemeMode(ThemeMode::Dark)
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Yellow,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'secondary' => Color::Blue,
                'indigo' => Color::Indigo,
                'slate' => Color::Slate,
                'sky' => Color::Sky,
                'violet' => Color::Violet,
                'fuchsia' => Color::Fuchsia,
                'purple' => Color::Purple,
                'red' => Color::Red,
                'green' => Color::Green,
                'amber' => Color::Amber,
                'lime' => Color::Lime,
            ])
            ->plugins([
                BreezyCore::make()
                    ->enableTwoFactorAuthentication(
                        force: false,
                    )
                    ->myProfile(
                        navigationGroup: 'Pengaturan',
                    ),
            ])
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([

            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([

            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([

            ])
            ->viteTheme('resources/css/filament/app/theme.css');
    }
}
