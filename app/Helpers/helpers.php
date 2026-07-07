<?php

use App\Models\Tenant;

if (! function_exists('hasFeature')) {
    /**
     * Determine if the current tenant has a given feature/module enabled.
     *
     * Usage:
     *   hasFeature('inventory')   // true/false
     *   hasFeature('default_features') // always true — core app features
     *
     * @param  string  $key  Feature key matching saas.php / menu.php (e.g. 'inventory', 'sales')
     */
    function hasFeature(string $key): bool
    {
        // Core features (Dashboard, Profile, Settings) are always available.
        if ($key === 'default_features') {
            return true;
        }

        $tenant = currentTenant();

        if (! $tenant) {
            return false;
        }

        return $tenant->hasFeature($key);
    }
}

if (! function_exists('currentTenant')) {
    /**
     * Resolve the tenant for the current request.
     *
     * Uses stancl/tenancy's global tenant() helper, which returns the
     * initialized Tenant instance once the tenancy middleware has run
     * (e.g. InitializeTenancyByDomain / ByPath), or null outside of a
     * tenant context (central domain, console, queued jobs without
     * tenant context, etc.)
     */
    function currentTenant(): ?Tenant
    {
        return tenant();
    }
}