<?php

namespace App\Http\Middleware;

use App\Services\SystemHealth\DiagnosticAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDiagnosticAuthenticated
{
    protected DiagnosticAuthService $authService;

    public function __construct(DiagnosticAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->authService->isAuthenticated($request)) {
            if ($request->expectsJson() || $request->is('*/api/*') || $request->is('api/*')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Diagnostic authentication required. Please verify access at /system-health.',
                ], 401)->header('Cache-Control', 'no-store, no-cache, must-revalidate');
            }

            return redirect()->route('system_health.index')
                ->with('error', 'Authentication required to access System Health & Diagnostics.');
        }

        /** @var Response $response */
        $response = $next($request);

        // Enforce strict security headers preventing CDN / browser caching of diagnostic metrics
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}

