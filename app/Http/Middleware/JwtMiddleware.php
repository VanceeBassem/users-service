<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use Illuminate\Http\Request;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            $payload = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            // load user from DB and attach as Laravel user resolver
            $user = User::with('role')->find($payload->sub);
            if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);
            // make request()->user() return this user
            $request->setUserResolver(function () use ($user) {
                return $user;
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token', 'error' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}
