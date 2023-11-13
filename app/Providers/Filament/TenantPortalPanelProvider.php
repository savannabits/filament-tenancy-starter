<?php

namespace App\Providers\Filament;

use App\Providers\TenancyServiceProvider;
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
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Core\Plugins\CorePlugin;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class TenantPortalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenantPortal')
            ->path('')
            ->brandName(fn() => \Str::of(config('app.name'))->append(": ")->append(tenant()?->name ?: tenant()?->id)->upper())
            ->topNavigation()
            ->login()
            ->discoverResources(in: app_path('Filament/TenantPortal/Resources'), for: 'App\\Filament\\TenantPortal\\Resources')
            ->discoverPages(in: app_path('Filament/TenantPortal/Pages'), for: 'App\\Filament\\TenantPortal\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/TenantPortal/Widgets'), for: 'App\\Filament\\TenantPortal\\Widgets')
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
            ])->middleware([
                'universal',
                TenancyServiceProvider::TENANCY_IDENTIFICATION,
                PreventAccessFromCentralDomains::class,
            ], isPersistent: true)
//            ->domains(fn() => tenant()?->domains()->pluck('domain'))
            ->authMiddleware([
                Authenticate::class,
            ])->plugin(CorePlugin::make()
                ->registerResources(false)
                ->registerPages(false)
                ->registerWidgets(false)
            );
    }
}
