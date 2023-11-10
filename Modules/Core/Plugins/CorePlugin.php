<?php

namespace Modules\Core\Plugins;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\Support\Assets\Theme;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use Modules\Core\app\Http\Middleware\RedirectIfInertiaMiddleware;

class CorePlugin implements Plugin
{
    private bool $registerResources = true;
    private bool $registerPages = true;
    private bool $registerWidgets = true;

    public function getId(): string
    {
        return 'core-plugin';
    }

    public static function getNavigationGroupLabel()
    {
        return 'System Setup';
    }

    public function register(Panel $panel): void
    {
        FilamentAsset::register([
            Theme::make($this->getId(), __DIR__ . '/../../resources/dist/theme.css'),
        ]);
        $panel->navigationGroups([
            NavigationGroup::make(static::getNavigationGroupLabel()),
            NavigationGroup::make('Settings')->collapsible()->collapsed(),
        ])
            ->middleware([
                RedirectIfInertiaMiddleware::class,
            ])
            ->theme($this->getId())
            ->maxContentWidth('screen-2xl');

        if ($this->shouldRegisterPages()) {
            $panel->discoverPages(in: __DIR__ . '/../Filament/Pages', for: 'Modules\\Core\\app\\Filament\\Pages');
        }
        if ($this->shouldRegisterResources()) {
            $panel->discoverResources(in: __DIR__ . '/../Filament/Resources', for: 'Modules\\Core\\app\\Filament\\Resources');
        }
        if ($this->shouldRegisterWidgets()) {
            $panel->discoverResources(in: __DIR__ . '/../Filament/Widgets', for: 'Modules\\Core\\app\\Filament\\Widgets');
        }
    }

    public function boot(Panel $panel): void
    {
        /*FilamentColor::register(fn() => [
            'primary' => tenant()?->primary_color ?: Color::Indigo,
            'info' => tenant()?->secondary_color ?: Color::Amber,
        ]);*/
        FilamentColor::register([
            'primary' => Color::Indigo,
            'info' => Color::Amber,
        ]);
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function registerResources(bool $registerResources = true): static
    {
        $this->registerResources = $registerResources;
        return $this;
    }

    public function shouldRegisterResources(): bool
    {
        return $this->registerResources;
    }

    public function registerPages(bool $registerPages = true): static
    {
        $this->registerPages = $registerPages;
        return $this;
    }

    public function shouldRegisterPages(): bool
    {
        return $this->registerPages;
    }

    public function registerWidgets(bool $registerWidgets = true): static
    {
        $this->registerWidgets = $registerWidgets;
        return $this;
    }

    public function shouldRegisterWidgets(): bool
    {
        return $this->registerWidgets;
    }


}
