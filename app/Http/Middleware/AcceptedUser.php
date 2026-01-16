<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AcceptedUser
{
    /**
     * Handle an incoming request.
     *
     * Check if authenticated user has verified email and accepted account.
     * If not, return error response.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => __('trans.unauthenticated'),
                'errors' => ['message' => [__('trans.unauthenticated')]]
            ], 401);
        }

        // Check if email is verified
        if (!$user->email_verified_at) {
            return response()->json([
                'status' => 403,
                'message' => __('trans.email_not_verified'),
                'errors' => ['message' => [__('trans.email_not_verified')]]
            ], 403);
        }

        // Check if account is accepted
        if ($user->is_accepted != 1) {
            return response()->json([
                'status' => 403,
                'message' => __('trans.account_not_accepted'),
                'errors' => ['message' => [__('trans.account_not_accepted')]]
            ], 403);
        }

        return $next($request);
    }
}
