<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            // Redirect berdasarkan role user
            if ($request->user()) {
                if ($request->user()->role === 'doctor') {
                    return redirect('/examinations');
                } elseif ($request->user()->role === 'pharmacist') {
                    return redirect('/pharmacy');
                }
            }

            return redirect('/login');
        }

        return $next($request);
    }
}
