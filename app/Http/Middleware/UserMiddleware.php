<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::where('token', $request->bearerToken())->first();

        if (!$user || $request->bearerToken() == null) {
            return response()->json([
                'message' => "Invalid token"
            ], 401);
        }

        Auth::login($user);

        return $next($request);
    }
}
