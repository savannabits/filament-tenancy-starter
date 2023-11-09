<?php

namespace Modules\Core\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfInertiaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (class_exists('\Inertia\Inertia') && $request->inertia()) {
            return app('\Inertia\Inertia')::location($request->url());
        }
        return $next($request);
    }
}
