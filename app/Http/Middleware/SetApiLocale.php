<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\App;

class SetApiLocale
{
    public function handle($request, Closure $next)
    {
        $lang = 'ar';

        if ($request->header('Accept-Language')
            && in_array($request->header('Accept-Language'), languages())) {
            $lang = $request->header('Accept-Language');
        }

        App::setLocale($lang);
        Carbon::setLocale($lang);

        return $next($request);
    }
}
