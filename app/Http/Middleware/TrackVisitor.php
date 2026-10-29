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
                try {
                    $activeSession = \App\Models\UserLoginLog::where('user_id', $user->id)
                        ->where('is_active', true)
                        ->latest('id')
                        ->first();
                    if ($activeSession) {
                        $activeSession->recordHeartbeat();
                    }
                } catch (\Throwable $e) {}
            }
        }

        // Public visitor logging (privacy-conscious, bot-filtered, throttled)
        if ($request->isMethod('GET') && !$request->ajax() && !$request->wantsJson()) {
            $path = $request->path();
            $isPublic = !str_starts_with($path, 'admin') 
                && !str_starts_with($path, 'mufti') 
                && !str_starts_with($path, 'user-heartbeat')
                && !str_starts_with($path, 'files')
                && !str_starts_with($path, 'api');

            if ($isPublic) {
                $ua = (string) $request->userAgent();
                $isBot = (bool) preg_match('/(bot|crawl|spider|slurp|facebookexternalhit|whatsapp|preview|mediapartners)/i', $ua);
                if (!$isBot) {
                    $ip = $request->ip();
                    if ($ip) {
                        $throttleKey = 'pv_log_' . md5($ip . '_' . $path);
                        if (!\Illuminate\Support\Facades\Cache::has($throttleKey)) {
                            \Illuminate\Support\Facades\Cache::put($throttleKey, true, now()->addMinutes(10));
                            try {
                                \App\Models\PortalVisitorLog::logVisit($ip, $request->fullUrl(), auth()->id());
                            } catch (\Throwable $e) {}
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
