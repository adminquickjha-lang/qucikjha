<?php

use App\Models\Seo;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

if (! function_exists('getSeo')) {
    /**
     * Get SEO data for a specific page key.
     */
    function getSeo(string $key)
    {
        try {
            $seo = Cache::remember("seo.{$key}", now()->addDays(7), function () use ($key) {
                return Seo::where('key', $key)->first();
            });

            if ($seo instanceof \__PHP_Incomplete_Class) {
                Cache::forget("seo.{$key}");
                return Seo::where('key', $key)->first();
            }

            return $seo;
        } catch (\Exception $e) {
            Cache::forget("seo.{$key}");
            return Seo::where('key', $key)->first();
        }
    }
}

if (! function_exists('isImpersonating')) {
    /**
     * Check if the current user is being impersonated.
     */
    function isImpersonating(): bool
    {
        return session()->has('impersonator_id');
    }
}

if (! function_exists('getImpersonator')) {
    /**
     * Get the original admin user who is impersonating.
     */
    function getImpersonator(): ?User
    {
        if (! isImpersonating()) {
            return null;
        }

        return User::find(session('impersonator_id'));
    }
}
