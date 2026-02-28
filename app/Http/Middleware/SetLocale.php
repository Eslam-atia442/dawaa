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
        $lang = 'ar';

        // ✅ Check if request URL starts with api/
        if ($request->is('api/*')) {

            $lang = $request->header('Accept-Language', 'ar');

        } else {

            if (Cookie::has('lang')) {
                $lang = Cookie::get('lang');
            }

            if (
                $request->header('Accept-Language') &&
                in_array($request->header('Accept-Language'), languages())
            ) {
                Cookie::queue(
                    'lang',
                    $request->header('Accept-Language'),
                    60 * 24 * 30
                );

                $lang = $request->header('Accept-Language');
            }
        }

        App::setLocale($lang);
        Carbon::setLocale($lang);

        return $next($request);
    }
}