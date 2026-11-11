<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Resolves "where should the Back/Cancel link on this page go" consistently across the whole
 * app. Laravel's own url()->previous() already tracks the immediately preceding page correctly
 * for plain navigation (index -> show), but that tracking is overwritten across a POST-then-
 * redirect hop (create -> store -> show) — so create/edit forms carry an explicit `back` value
 * forward (see the `back` hidden field + the `back` query param appended on redirect) to bridge
 * that one hop. See the "App-wide back navigation fix" plan for the full rationale.
 */
class BackNavigation
{
    public static function url(array $fallbackParams = [], ?string $fallbackRoute = null): string
    {
        $request = request();

        $explicit = $request->input('back');
        if ($explicit && static::isInternal($explicit)) {
            return $explicit;
        }

        // Laravel's own previous() never actually returns empty — with nothing in the session it
        // silently falls back to the site root (`url('/')`) itself. That bare-root value isn't a
        // real "previous page" worth honoring; treating it as one would mask the smarter fallback
        // below (a module's own index, or an explicit parent) on every genuinely cold visit (a
        // bookmarked/shared link, a new tab) with just "go to the homepage" instead.
        $previous = url()->previous();
        if ($previous && $previous !== $request->fullUrl() && $previous !== url('/') && static::isInternal($previous)) {
            return $previous;
        }

        if ($fallbackRoute) {
            return static::safeRoute($fallbackRoute, $fallbackParams);
        }

        $indexRoute = preg_replace('/\.(show|create|edit)$/', '.index', (string) Route::currentRouteName());

        if (! Route::has($indexRoute)) {
            return url('/');
        }

        // Carry over the current route's own bound parameters (e.g. a `{type}` segment shared by
        // every action in a parameterized resource like warehouse.vouchers.*) — any that don't
        // match a placeholder on the index route are harmlessly appended as query string instead.
        return static::safeRoute($indexRoute, $request->route()?->parameters() ?? []);
    }

    /** Never let a bad/missing route parameter turn "where's Back go" into a 500. */
    protected static function safeRoute(string $name, array $params): string
    {
        try {
            return route($name, $params);
        } catch (\Throwable $e) {
            return url('/');
        }
    }

    protected static function isInternal(string $url): bool
    {
        return str_starts_with($url, url('/'));
    }
}
