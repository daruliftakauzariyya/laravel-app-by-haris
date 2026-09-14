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
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated. Please log in.'
                ], 401);
            }

            return redirect()->guest(route('login'))->with('error', 'Please sign in to access this page.');
        }

        $user = auth()->user();
        $allowedRoles = explode('|', $role);
        $userRole = $user->role ? strtolower($user->role->name) : 'questioner';

        // Check if user role matches any allowed role
        if (!in_array($userRole, array_map('strtolower', $allowedRoles))) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. You do not have permission for this resource.'
                ], 403);
            }

            // Graceful redirection based on actual user role
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied: You do not have permission to view that section.');
            } elseif ($userRole === 'mufti') {
                return redirect()->route('mufti.dashboard')->with('error', 'Access Denied: This area is restricted to administrators.');
            } else {
                return redirect()->route('user.dashboard')->with('error', 'Access Denied: Administrative access requires privileged credentials.');
            }
        }

        return $next($request);
    }
}
