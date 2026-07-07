<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized.');
        }

        $user = auth()->user();
        $roles = explode('|', $role);

        if (!$user->role || !in_array($user->role->name, $roles)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
