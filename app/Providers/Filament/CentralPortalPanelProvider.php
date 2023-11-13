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
use Modules\Core\Plugins\CorePlugin;

class CentralPortalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('portal')
            ->path('')
            ->login()
            ->discoverResources(in: app_path('Filament/CentralPortal/Resources'), for: 'App\\Filament\\CentralPortal\\Resources')
            ->discoverPages(in: app_path('Filament/CentralPortal/Pages'), for: 'App\\Filament\\CentralPortal\\Pages')
            ->brandName('CENTRAL PORTAL')
            ->discoverWidgets(in: app_path('Filament/CentralPortal/Widgets'), for: 'App\\Filament\\CentralPortal\\Widgets')
            ->topNavigation()
            ->pages([
                Pages\Dashboard::class,
            ])
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
            ])
            ->persistentMiddleware(['universal'])
            ->domains(config('tenancy.central_domains'))
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
