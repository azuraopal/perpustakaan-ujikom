<?php

namespace App\Providers\Filament;

use App\Filament\Siswa\Widgets\SiswaAktivitasPinjamChart;
use App\Filament\Siswa\Widgets\SiswaActivePeminjamanWidget;
use App\Filament\Siswa\Widgets\SiswaRingkasanWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SiswaPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('siswa')
            ->path('siswa')
            ->brandName('Portal Siswa')
            ->font('Inter')
            ->viteTheme('resources/css/filament/siswa/theme.css')
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->maxContentWidth('full')
            ->databaseNotifications()
            ->colors([
                'primary' => Color::Gray,
                'danger' => Color::Rose,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Siswa/Resources'), for: 'App\Filament\Siswa\Resources')
            ->discoverPages(in: app_path('Filament/Siswa/Pages'), for: 'App\Filament\Siswa\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Siswa/Widgets'), for: 'App\Filament\Siswa\Widgets')
            ->widgets([
                \App\Filament\Siswa\Widgets\SiswaGreetingWidget::class,
                SiswaRingkasanWidget::class,
                SiswaActivePeminjamanWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
