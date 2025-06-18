<?php
// App\Http\Middleware\AuthenticateAny.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateAny
{
    public function handle($request, Closure $next)
    {
        // Try user guard
        if (Auth::guard('api')->check()) {
            Auth::shouldUse('api');
            return $next($request);
        }

        // Try admin guard
        if (Auth::guard('admin')->check()) {
            Auth::shouldUse('admin');
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
