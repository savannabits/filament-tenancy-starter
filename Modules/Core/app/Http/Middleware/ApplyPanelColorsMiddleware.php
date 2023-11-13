<?php

namespace Modules\Core\app\Http\Middleware;

use Closure;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Request;

class ApplyPanelColorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        FilamentColor::register([
            'primary' => tenant()?->primary_color ?: Color::Indigo,
            'info' => tenant()?->secondary_color ?: Color::Amber,
        ]);
        return $next($request);
    }
}
