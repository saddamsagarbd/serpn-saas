<?php

namespace App\Models\Concerns;

trait HasFeatures
{
    /**
     * Check whether this plan has a given feature/module enabled.
     *
     * Expects the model to have a `features` column, cast to array,
     * saved as: ['inventory' => 1, 'sales' => 1, 'hrm' => 0, ...]
     * — i.e. exactly the shape posted by the
     * `features[{key}]` checkboxes in the plan's settings form.
     */
    public function hasFeature(string $key): bool
    {
        if ($key === 'default_features') {
            return true;
        }

        $features = $this->features ?? [];

        return (bool) ($features[$key] ?? false);
    }

    /**
     * Check multiple features at once — returns true only if ALL are enabled.
     */
    public function hasFeatures(array $keys): bool
    {
        foreach ($keys as $key) {
            if (! $this->hasFeature($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check multiple features at once — returns true if ANY is enabled.
     */
    public function hasAnyFeature(array $keys): bool
    {
        foreach ($keys as $key) {
            if ($this->hasFeature($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Enable a feature and persist it.
     */
    public function enableFeature(string $key): void
    {
        $features = $this->features ?? [];
        $features[$key] = true;
        $this->features = $features;
        $this->save();
    }

    /**
     * Disable a feature and persist it.
     */
    public function disableFeature(string $key): void
    {
        $features = $this->features ?? [];
        $features[$key] = false;
        $this->features = $features;
        $this->save();
    }
}