<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PortalVisitorLog;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log successful GET requests to public pages
        // We exclude admin pages, API, and debugbar/internal routes
        if ($request->isMethod('GET') && 
            !$request->is('admin*') && 
            !$request->is('api*') && 
            !$request->is('_debugbar*') &&
            $response->getStatusCode() === 200) {
            
            try {
                PortalVisitorLog::logVisit(
                    $request->ip(),
                    $request->fullUrl(),
                    auth()->id()
                );
            } catch (\Exception $e) {
                // Fail silently to not break the user experience
            }
        }

        return $response;
    }
}
