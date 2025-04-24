<?php

declare(strict_types=1);

namespace App\Providers;

use App\Filament\Admin\Pages\Settings;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Croustibat\FilamentJobsMonitor\FilamentJobsMonitorPlugin;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Filafly\PhosphorIconReplacement;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Tapp\FilamentWebhookClient\FilamentWebhookClientPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->maxContentWidth(MaxWidth::Full)
            ->databaseNotifications()
            ->databaseTransactions()
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Lime,
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
                'yellow' => Color::Yellow,
            ])
            ->resources([
                config('filament-logger.activity_resource'),
            ])
            ->navigationGroups([
                'Pendaftaran',
                'Master',
                'Administration',
                'Settings',
                'Webhooks',
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->globalSearchFieldKeyBindingSuffix()
            ->font('Poppins')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
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

                SetTheme::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                BreezyCore::make()
                    ->myProfile(
                        navigationGroup: 'Settings',
                    ),
                FilamentJobsMonitorPlugin::make(),
                PhosphorIconReplacement::make(),
                FilamentWebhookClientPlugin::make(),
                ThemesPlugin::make(),
                FilamentBackgroundsPlugin::make(),
                FilamentSettingsPlugin::make()
                    ->pages([
                        Settings::class,
                    ]),
                EasyFooterPlugin::make()
                    ->withLoadTime('Halaman ini dimuat dalam ')
                    ->withGithub()
                    ->withSentence(config('app.brand') . ' - ' . config('app.event')),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
