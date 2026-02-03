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

        if (!$user) {
            abort(403);
        }

        // Get all user roles (primary + additional)
        $userRoles = $user->getAllRoleNames();
        
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

        // Check forbidden roles - if user has ANY forbidden role, deny access
        if (!empty($forbidden)) {
            foreach ($forbidden as $forbiddenRole) {
                if (in_array($forbiddenRole, $userRoles, true)) {
                    abort(403);
                }
            }
        }

        // Check allowed roles - if user has ANY allowed role, grant access
        if (!empty($allowed)) {
            $hasAllowedRole = false;
            foreach ($allowed as $allowedRole) {
                if (in_array($allowedRole, $userRoles, true)) {
                    $hasAllowedRole = true;
                    break;
                }
            }
            
            if (!$hasAllowedRole) {
                abort(403);
            }
        }

        return $next($request);
    }
}