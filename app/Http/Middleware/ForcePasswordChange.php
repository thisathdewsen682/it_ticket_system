<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->force_password_change) {
            // Allow access to the force-change-password page, logout, and password update
            if ($request->routeIs('password.force-change', 'password.force-update', 'logout')) {
                return $next($request);
            }

            return redirect()->route('password.force-change');
        }

        return $next($request);
    }
}
