<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileCompleted
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->profile_completed) {
            if (!$request->routeIs('profile.complete') && !$request->routeIs('logout')) {
                return redirect()->route('profile.complete');
            }
        }
        return $next($request);
    }
}
