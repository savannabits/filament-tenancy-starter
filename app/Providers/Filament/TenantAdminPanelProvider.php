<?php

namespace App\Providers\Filament;

use App\Models\User;
use App\Providers\TenancyServiceProvider;
use Closure;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Core\Plugins\CorePlugin;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Tenancy;

class TenantAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('tenant-admin')
            ->path('admin')
            ->login()
            ->brandName(fn() => \Str::of(config('app.name'))->append(": ")->append(tenant()?->name ?: tenant()?->id)->upper())
            ->discoverResources(in: app_path('Filament/TenantAdmin/Resources'), for: 'App\\Filament\\TenantAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/TenantAdmin/Pages'), for: 'App\\Filament\\TenantAdmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/TenantAdmin/Widgets'), for: 'App\\Filament\\TenantAdmin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
//            ->domains(fn() => tenant()?->domains()->pluck('domain'))
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
            ->authMiddleware([
                Authenticate::class,
            ])->plugin(CorePlugin::make());
    }
}
