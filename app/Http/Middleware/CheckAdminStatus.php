<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminStatus
{
    /**
     * Handle an incoming request.
     *
     * Check if authenticated admin is active and all their roles are active.
     * If not, logout the admin and redirect to login.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();

        if ($admin) {
            if ($admin->is_blocked == 1 || $admin->is_active == 0) {
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->withErrors(['email' => trans('auth.failed2')]);
            }
 
            $roles = $admin->roles;
            foreach ($roles as $role) {
                if (!$role->is_active) {
                    Auth::guard('admin')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect()->route('login')
                        ->withErrors(['email' => trans('auth.failed3')]);
                }
            }
        }

        return $next($request);
    }
}
