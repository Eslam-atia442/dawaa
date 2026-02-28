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

        if (Cookie::has('lang')) {
            $lang = Cookie::get('lang');
        } elseif ($request->header('Accept-Language')) {
            $headerLang = $request->header('Accept-Language');
            // Accept-Language can be "en-US,en;q=0.9,ar;q=0.8" - take first valid code
            $headerLang = strtolower(trim(explode(',', $headerLang)[0]));
            if (in_array($headerLang, languages())) {
                $lang = $headerLang;
                Cookie::queue('lang', $lang, 30 * 24 * 60); // 30 days in minutes
            }
        }

        App::setLocale($lang);
        Carbon::setLocale($lang);

        return $next($request);
    }
}
