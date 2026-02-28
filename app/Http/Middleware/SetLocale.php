<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $lang = defaultLang();

        if ($request->is('api/*')) {
            // API: use only headers (no cookies - API clients typically don't send cookies)
            $lang = $this->localeFromRequest($request) ?? $lang;
        } else {
            // Web: cookie first (only if valid), then header, and persist in cookie
            $cookieLang = Cookie::get('lang');
            if ($cookieLang !== null && in_array($cookieLang, languages(), true)) {
                $lang = $cookieLang;
            } else {
                if ($cookieLang !== null) {
                    Cookie::queue(Cookie::forget('lang'));
                }
                $headerLang = $this->localeFromRequest($request);
                if ($headerLang !== null) {
                    $lang = $headerLang;
                    Cookie::queue('lang', $lang, 30 * 24 * 60, '/');
                }
            }
        }

        App::setLocale($lang);
        Carbon::setLocale($lang);

        return $next($request);
    }

    /**
     * Get locale from Accept-Language or X-Locale header. Returns null if none valid.
     */
    protected function localeFromRequest($request): ?string
    {
        $allowed = languages();

        if ($request->header('X-Locale')) {
            $v = strtolower(trim($request->header('X-Locale')));
            if (in_array($v, $allowed)) {
                return $v;
            }
        }

        if ($request->header('Accept-Language')) {
            $raw = $request->header('Accept-Language');
            $first = strtolower(trim(explode(',', $raw)[0]));
            // e.g. "en-US" -> "en"
            if (strlen($first) >= 2 && in_array(substr($first, 0, 2), $allowed)) {
                return substr($first, 0, 2);
            }
            if (in_array($first, $allowed)) {
                return $first;
            }
        }

        return null;
    }
}
