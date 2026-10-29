<?php

namespace App\Http\Controllers\SystemHealth;

use App\Http\Controllers\Controller;
use App\Services\SystemHealth\DiagnosticAuthService;
use App\Services\SystemHealth\SystemHealthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemHealthPortalController extends Controller
{
    protected DiagnosticAuthService $authService;
    protected SystemHealthService $healthService;

    public function __construct(DiagnosticAuthService $authService, SystemHealthService $healthService)
    {
        $this->authService = $authService;
        $this->healthService = $healthService;
    }

    /**
     * Display the System Health page or OTP challenge gate.
     */
    public function index(Request $request): View
    {
        if (!$this->authService->isAuthenticated($request)) {
            $maskedEmail = $this->authService->maskEmail($this->authService->getAuthorizedEmail());
            $hasActiveOtp = $this->authService->hasActiveOtp();
            $cooldownRemaining = $this->authService->getCooldownRemaining();
            $expiresInSeconds = $this->authService->getExpiryRemaining();

            return view('system-health.auth', [
                'maskedEmail'        => $maskedEmail,
                'hasActiveOtp'       => $hasActiveOtp,
                'cooldownRemaining'  => $cooldownRemaining,
                'expiresInSeconds'   => $expiresInSeconds,
            ]);
        }

        // Preload initial dashboard data for instant render
        $overview = $this->healthService->getOverview();
        $serverInfo = $this->healthService->getServerInfo();
        $phpInfo = $this->healthService->getPhpInfo();
        $laravelInfo = $this->healthService->getLaravelInfo();
        $database = $this->healthService->getDatabaseHealth();
        $resources = $this->healthService->getResourceUsage();
        $apiMetrics = $this->healthService->getApiMetrics('24h');
        $security = $this->healthService->getSecurityChecklist($request);

        return view('system-health.dashboard', [
            'overview'    => $overview,
            'serverInfo'  => $serverInfo,
            'phpInfo'     => $phpInfo,
            'laravelInfo' => $laravelInfo,
            'database'    => $database,
            'resources'   => $resources,
            'apiMetrics'  => $apiMetrics,
            'security'    => $security,
        ]);
    }

    /**
     * Trigger generation and delivery of verification OTP.
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $result = $this->authService->requestOtp($request);
        $statusCode = $result['success'] ? 200 : 429;
        if (!$result['success'] && str_contains($result['message'], 'SMTP')) {
            $statusCode = 500;
        }

        return response()->json($result, $statusCode);
    }

    /**
     * Verify submitted OTP and establish authenticated session.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $result = $this->authService->verifyOtp((string) $request->input('otp'), $request);
        $statusCode = $result['success'] ? 200 : 401;

        return response()->json($result, $statusCode);
    }

    /**
     * Terminate diagnostic session.
     */
    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        $this->authService->logout($request);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Diagnostic session terminated.',
                'redirect'=> route('system_health.index'),
            ]);
        }

        return redirect()->route('system_health.index')
            ->with('success', 'You have been securely logged out of the System Health portal.');
    }

    /**
     * API: Overall System Status.
     */
    public function overview(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->healthService->getOverview(),
        ]);
    }

    /**
     * API: Server & OS Information.
     */
    public function server(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->healthService->getServerInfo(),
        ]);
    }

    /**
     * API: PHP Runtime Environment.
     */
    public function phpRuntime(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->healthService->getPhpInfo(),
        ]);
    }

    /**
     * API: Laravel Application Status.
     */
    public function laravel(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->healthService->getLaravelInfo(),
        ]);
    }

    /**
     * API: Database Health & Latency.
     */
    public function database(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->healthService->getDatabaseHealth(),
        ]);
    }

    /**
     * API: RAM & Disk Resources.
     */
    public function resources(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->healthService->getResourceUsage(),
        ]);
    }

    /**
     * API: API Performance & Health Metrics.
     */
    public function apiMetrics(Request $request): JsonResponse
    {
        $window = (string) $request->input('window', '24h');
        if (!in_array($window, ['5m', '1h', '24h', '7d'], true)) {
            $window = '24h';
        }

        return response()->json([
            'status' => 'success',
            'data'   => $this->healthService->getApiMetrics($window),
        ]);
    }

    /**
     * API: Paginated Recent API Requests.
     */
    public function recentRequests(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->healthService->getRecentRequests($request),
        ]);
    }

    /**
     * API: Recent Application Errors & Exceptions.
     */
    public function errors(Request $request): JsonResponse
    {
        $limit = max(5, min(100, (int) $request->input('limit', 30)));
        return response()->json([
            'status' => 'success',
            'data'   => $this->healthService->getRecentErrors($limit),
        ]);
    }

    /**
     * API: Security Configuration Checklist.
     */
    public function security(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->healthService->getSecurityChecklist($request),
        ]);
    }

    /**
     * API: Run Synthetic Health Probe.
     */
    public function runSyntheticPing(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->healthService->runSyntheticPing(),
        ]);
    }
}

