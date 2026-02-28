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
            $lang = $this->localeFromRequest($request, true) ?? $this->localeFromRequest($request, false) ?? $lang;
        } else {
            // Web: X-Locale first (explicit switch from UI), then cookie, then Accept-Language
            $explicitLang = $this->localeFromRequest($request, true);  // only X-Locale
            if ($explicitLang !== null) {
                $lang = $explicitLang;
                Cookie::queue('lang', $lang, 30 * 24 * 60, '/');
            } else {
                $cookieLang = Cookie::get('lang');
                if ($cookieLang !== null && in_array($cookieLang, languages(), true)) {
                    $lang = $cookieLang;
                } else {
                    if ($cookieLang !== null) {
                        Cookie::queue(Cookie::forget('lang'));
                    }
                    $headerLang = $this->localeFromRequest($request, false); // Accept-Language only
                    if ($headerLang !== null) {
                        $lang = $headerLang;
                        Cookie::queue('lang', $lang, 30 * 24 * 60, '/');
                    }
                }
            }
        }

        App::setLocale($lang);
        Carbon::setLocale($lang);

        return $next($request);
    }

    /**
     * Get locale from headers. Returns null if none valid.
     * @param  bool  $xLocaleOnly  When true, only check X-Locale (explicit switch). When false, only check Accept-Language.
     */
    protected function localeFromRequest($request, bool $xLocaleOnly = false): ?string
    {
        $allowed = languages();

        if ($xLocaleOnly) {
            $v = $request->header('X-Locale') ? strtolower(trim($request->header('X-Locale'))) : null;
            return $v && in_array($v, $allowed) ? $v : null;
        }

        if ($request->header('Accept-Language')) {
            $raw = $request->header('Accept-Language');
            $first = strtolower(trim(explode(',', $raw)[0]));
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
