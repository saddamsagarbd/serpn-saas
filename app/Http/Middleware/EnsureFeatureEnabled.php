<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeatureEnabled
{
    /**
     * Block access to a route unless the current tenant has the
     * given feature enabled. Registered as route middleware alias
     * 'feature', used like: ->middleware('feature:inventory')
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (! hasFeature($feature)) {
            abort(403, "The \"{$feature}\" module is not enabled for your account.");
        }

        return $next($request);
    }
}