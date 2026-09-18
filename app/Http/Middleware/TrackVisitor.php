<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            // Throttle updating last_seen_at to at most once per 60 seconds
            if (!$user->last_seen_at || $user->last_seen_at->lt(now()->subSeconds(60))) {
                \App\Models\User::where('id', $user->id)->update([
                    'last_seen_at' => now(),
                ]);

                // Update active login session heartbeat
                $activeSession = \App\Models\UserLoginLog::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->latest()
                    ->first();
                if ($activeSession) {
                    $activeSession->recordHeartbeat();
                }
            }
        }

        return $next($request);
    }
}
