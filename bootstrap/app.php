<?php

if (file_exists(__DIR__ . '/../vendor/predis/predis/autoload.php')) {
    require_once __DIR__ . '/../vendor/predis/predis/autoload.php';
}

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register global web middleware
        $middleware->web(append: [
            \App\Http\Middleware\TrackVisitor::class,
            \App\Http\Middleware\JsonLdMiddleware::class,
        ]);

        // Register API telemetry middleware
        $middleware->api(append: [
            \App\Http\Middleware\RecordApiTelemetry::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'user-heartbeat',
        ]);

        // Register alias middleware
        $middleware->alias([
            'profile.completed'  => \App\Http\Middleware\ProfileCompleted::class,
            'role'               => \App\Http\Middleware\RoleMiddleware::class,
            'system_health.auth' => \App\Http\Middleware\EnsureDiagnosticAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your session has expired. Please refresh the page.',
                    'csrf_error' => true,
                ], 419);
            }

            return redirect()->back()->with('error', 'Your session expired or the page timed out. Please try again.');
        });
    })->create();
