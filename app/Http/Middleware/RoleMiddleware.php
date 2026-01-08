<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user(); // avoids "auth()->user()" IDE warning

        $userRole = $user?->role?->name;

        $allowed = [];
        $forbidden = [];

        foreach ($roles as $roleToken) {
            $roleToken = (string) $roleToken;

            if ($roleToken === '') {
                continue;
            }

            if (str_starts_with($roleToken, '!')) {
                $forbidden[] = substr($roleToken, 1);
                continue;
            }

            if (str_starts_with($roleToken, 'not:')) {
                $forbidden[] = substr($roleToken, 4);
                continue;
            }

            $allowed[] = $roleToken;
        }

        if (!$user || !$userRole) {
            abort(403);
        }

        if (!empty($forbidden) && in_array($userRole, $forbidden, true)) {
            abort(403);
        }

        if (!empty($allowed) && !in_array($userRole, $allowed, true)) {
            abort(403);
        }

        return $next($request);
    }
}