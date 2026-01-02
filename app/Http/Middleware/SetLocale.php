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
        $lang = "ar";
        if (Cookie::has('lang'))
            $lang = Cookie::get('lang');


        if ($request->header('Accept-Language')
            && in_array($request->header('Accept-Language'), languages(),))
            setcookie('lang', $request->header('Accept-Language'), time() + (86400 * 30), "/");


        App::setLocale($lang);
        Carbon::setLocale($lang);

        return $next($request);
    }
}
