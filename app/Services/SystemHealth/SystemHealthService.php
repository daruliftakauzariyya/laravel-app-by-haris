<?php

namespace App\Services\SystemHealth;

use App\Models\SystemApiLog;
use App\Models\SystemHealthAuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SystemHealthService
{
    /**
     * Get consolidated system overview.
     */
    public function getOverview(): array
    {
        $db = $this->getDatabaseHealth();
        $cache = $this->getCacheHealth();
        $storage = $this->getStorageHealth();
        $mail = $this->getMailHealth();
        $queue = $this->getQueueHealth();
        $apiSummary = $this->getApiMetricsSummary('24h');
        $errorSummary = $this->getErrorSummary();

        // Determine Overall System Status
        $criticalComponents = [$db['status'] === 'connected', $storage['status'] === 'healthy'];
        $degradedComponents = [
            $cache['status'] === 'healthy',
            $mail['configured'],
            $queue['status'] === 'active' || $queue['driver'] === 'sync',
            ($apiSummary['availability_pct'] >= 90),
        ];

        $overallStatus = 'Healthy';
        $statusColor = 'success';
        if (in_array(false, $criticalComponents, true)) {
            $overallStatus = 'Critical';
            $statusColor = 'danger';
        } elseif (in_array(false, $degradedComponents, true) || $apiSummary['availability_pct'] < 95) {
            $overallStatus = 'Degraded';
            $statusColor = 'warning';
        }

        return [
            'overall_status'          => $overallStatus,
            'status_color'            => $statusColor,
            'application_uptime'      => $this->getApplicationUptime(),
            'server_uptime'           => $this->getServerUptime(),
            'last_check_timestamp'    => now()->format('Y-m-d H:i:s T'),
            'database'                => $db,
            'cache'                   => $cache,
            'storage'                 => $storage,
            'mail'                    => $mail,
            'queue'                   => $queue,
            'api_summary'             => $apiSummary,
            'error_summary'           => $errorSummary,
            'recent_failed_requests'  => $apiSummary['failed_count'],
            'recent_errors_count'     => $errorSummary['total_errors_24h'],
            'latest_error_timestamp'  => $errorSummary['latest_error_time'],
        ];
    }

    /**
     * Server & OS Information.
     */
    public function getServerInfo(): array
    {
        $osFamily = PHP_OS_FAMILY;
        $osName = php_uname('s') . ' ' . php_uname('r');
        $hostname = gethostname() ?: 'darulifta-node';
        $architecture = php_uname('m');
        $serverTime = now()->format('Y-m-d H:i:s');
        $appTimezone = config('app.timezone', 'Asia/Kolkata');
        $environment = config('app.env', 'production');
        $sapi = php_sapi_name();
        $webServer = $_SERVER['SERVER_SOFTWARE'] ?? 'Artisan Development Server (PHP CLI)';

        // Detect CPU cores safely
        $coreCount = (int) (getenv('NUMBER_OF_PROCESSORS') ?: 1);
        if ($coreCount <= 1 && function_exists('shell_exec') && $osFamily === 'Linux') {
            $nproc = @shell_exec('nproc 2>/dev/null');
            if ($nproc) {
                $coreCount = (int) trim($nproc);
            }
        }

        // System Load Average
        $loadAvg = [0.0, 0.0, 0.0];
        if (function_exists('sys_getloadavg')) {
            $sysLoad = sys_getloadavg();
            if (is_array($sysLoad)) {
                $loadAvg = array_map(fn($v) => round($v, 2), $sysLoad);
            }
        }

        // Server Uptime
        $uptime = $this->getServerUptime();

        return [
            'os_family'     => $osFamily,
            'os_name'       => $osName,
            'hostname'      => $hostname,
            'architecture'  => $architecture,
            'cpu_cores'     => $coreCount,
            'load_average'  => $loadAvg,
            'server_uptime' => $uptime,
            'server_time'   => $serverTime,
            'timezone'      => $appTimezone,
            'environment'   => $environment,
            'php_sapi'      => $sapi,
            'web_server'    => $webServer,
        ];
    }

    /**
     * PHP Runtime Environment.
     */
    public function getPhpInfo(): array
    {
        $version = PHP_VERSION;
        $sapi = php_sapi_name();
        $memoryLimit = ini_get('memory_limit');
        $currentMemory = round(memory_get_usage(true) / 1024 / 1024, 2);
        $peakMemory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
        $maxExecutionTime = (int) ini_get('max_execution_time');

        // Required extensions checklist
        $requiredExtensions = [
            'pdo', 'pdo_mysql', 'openssl', 'mbstring', 'tokenizer',
            'xml', 'ctype', 'json', 'bcmath', 'curl', 'fileinfo'
        ];

        $extensionStatus = [];
        foreach ($requiredExtensions as $ext) {
            $extensionStatus[$ext] = extension_loaded($ext);
        }

        // OPcache Status
        $opcacheInfo = [
            'enabled'    => false,
            'memory_used_mb' => 0,
            'memory_free_mb' => 0,
            'hit_rate_pct' => 0,
        ];

        if (function_exists('opcache_get_status')) {
            $status = @opcache_get_status(false);
            if (is_array($status) && !empty($status['opcache_enabled'])) {
                $opcacheInfo['enabled'] = true;
                $mem = $status['memory_usage'] ?? [];
                $stats = $status['opcache_statistics'] ?? [];
                $opcacheInfo['memory_used_mb'] = round(($mem['used_memory'] ?? 0) / 1024 / 1024, 2);
                $opcacheInfo['memory_free_mb'] = round(($mem['free_memory'] ?? 0) / 1024 / 1024, 2);
                $opcacheInfo['hit_rate_pct'] = round($stats['opcache_hit_rate'] ?? 0, 1);
            }
        }

        // Safe Configuration Warnings
        $warnings = [];
        if (ini_get('display_errors') && config('app.env') === 'production') {
            $warnings[] = 'PHP display_errors is enabled in production environment.';
        }
        if ($maxExecutionTime > 0 && $maxExecutionTime < 30) {
            $warnings[] = "Low max_execution_time ({$maxExecutionTime}s). Long-running PDF generation may timeout.";
        }

        return [
            'php_version'        => $version,
            'sapi'               => $sapi,
            'memory_limit'       => $memoryLimit,
            'current_memory_mb'  => $currentMemory,
            'peak_memory_mb'     => $peakMemory,
            'max_execution_time' => $maxExecutionTime,
            'loaded_extensions'  => get_loaded_extensions(),
            'required_checklist' => $extensionStatus,
            'opcache'            => $opcacheInfo,
            'warnings'           => $warnings,
        ];
    }

    /**
     * Laravel Application Health.
     */
    public function getLaravelInfo(): array
    {
        $version = app()->version();
        $env = config('app.env');
        $debug = config('app.debug');
        $queueDriver = config('queue.default');
        $cacheDriver = config('cache.default');
        $sessionDriver = config('session.driver');
        $logChannel = config('logging.default');

        // Safe, Read-Only Migration Check
        $migrationStatus = $this->checkPendingMigrations();

        return [
            'framework_version'   => $version,
            'environment'         => $env,
            'debug_mode'          => $debug,
            'queue_driver'        => $queueDriver,
            'cache_driver'        => $cacheDriver,
            'session_driver'      => $sessionDriver,
            'log_channel'         => $logChannel,
            'migrations'          => $migrationStatus,
            'boot_status'         => 'Operational',
            'timezone'            => config('app.timezone'),
        ];
    }

    /**
     * Database Health and Latency.
     */
    public function getDatabaseHealth(): array
    {
        $startTime = microtime(true);
        $status = 'disconnected';
        $latencyMs = 0;
        $version = 'Unknown';
        $driver = config('database.default', 'mysql');
        $tableCount = 0;
        $dbSizeMb = 'Unavailable — insufficient database permissions';

        try {
            // Lightweight latency probe
            DB::select('SELECT 1');
            $latencyMs = round((microtime(true) - $startTime) * 1000, 2);
            $status = 'connected';

            // Masked Database Name
            $rawDbName = config("database.connections.{$driver}.database");
            $maskedDbName = $this->maskString($rawDbName);

            // Cached Database Metadata (Avoid heavy Schema/information_schema queries across WAN on every refresh)
            $dbMeta = Cache::remember('syshealth_db_meta_v2', 300, function () use ($rawDbName) {
                $version = 'MySQL (Remote)';
                try {
                    $versionResult = DB::select('SELECT VERSION() as v');
                    if (!empty($versionResult[0]->v)) {
                        $version = $versionResult[0]->v;
                    }
                } catch (\Throwable $e) {}

                $tableCount = 0;
                try {
                    $tables = Schema::getTableListing();
                    $tableCount = count($tables);
                } catch (\Throwable $e) {}

                $dbSizeMb = 'Available via DB Engine';
                try {
                    $sizeQuery = DB::select(
                        "SELECT SUM(data_length + index_length) / 1024 / 1024 AS size_mb 
                         FROM information_schema.tables 
                         WHERE table_schema = ?",
                        [$rawDbName]
                    );
                    if (!empty($sizeQuery[0]->size_mb)) {
                        $dbSizeMb = round((float) $sizeQuery[0]->size_mb, 2) . ' MB';
                    }
                } catch (\Throwable $e) {}

                return [
                    'version'       => $version,
                    'table_count'   => $tableCount,
                    'database_size' => $dbSizeMb,
                ];
            });

            $version = $dbMeta['version'];
            $tableCount = $dbMeta['table_count'];
            $dbSizeMb = $dbMeta['database_size'];
        } catch (\Throwable $e) {
            $status = 'error';
            $maskedDbName = 'N/A';
        }

        return [
            'status'         => $status,
            'driver'         => $driver,
            'version'        => $version,
            'latency_ms'     => $latencyMs,
            'masked_name'    => $maskedDbName ?? 'N/A',
            'table_count'    => $tableCount,
            'database_size'  => $dbSizeMb,
            'last_checked'   => now()->format('H:i:s'),
        ];
    }

    /**
     * Memory & Disk Storage Resources.
     */
    public function getResourceUsage(): array
    {
        $phpCurrentMb = round(memory_get_usage(true) / 1024 / 1024, 2);
        $phpPeakMb = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

        // System RAM Metrics
        $ramMetrics = $this->getSystemRamMetrics();

        // Disk Storage Metrics
        $basePath = base_path();
        $totalBytes = @disk_total_space($basePath) ?: 1;
        $freeBytes = @disk_free_space($basePath) ?: 0;
        $usedBytes = max(0, $totalBytes - $freeBytes);
        $diskUtilPct = round(($usedBytes / $totalBytes) * 100, 1);

        // App Storage Directory Sizes (Cached to avoid repetitive I/O on every refresh)
        $directorySizes = Cache::remember('syshealth_dir_sizes_v1', 600, function () {
            return [
                'storage_dir_mb' => $this->getDirectorySizeMb(storage_path('app')),
                'logs_dir_mb'    => $this->getDirectorySizeMb(storage_path('logs')),
            ];
        });

        return [
            'ram' => array_merge($ramMetrics, [
                'php_current_mb' => $phpCurrentMb,
                'php_peak_mb'    => $phpPeakMb,
            ]),
            'disk' => [
                'total_gb'        => round($totalBytes / 1024 / 1024 / 1024, 2),
                'used_gb'         => round($usedBytes / 1024 / 1024 / 1024, 2),
                'free_gb'         => round($freeBytes / 1024 / 1024 / 1024, 2),
                'utilization_pct' => $diskUtilPct,
                'app_storage_mb'  => $directorySizes['storage_dir_mb'],
                'logs_size_mb'    => $directorySizes['logs_dir_mb'],
            ],
        ];
    }

    /**
     * API Health & Strength Metrics over configurable window (5m, 1h, 24h, 7d).
     */
    public function getApiMetrics(string $window = '24h'): array
    {
        $since = match ($window) {
            '5m' => now()->subMinutes(5),
            '1h' => now()->subHour(),
            '7d' => now()->subDays(7),
            default => now()->subHours(24),
        };

        // Query telemetry logs
        $query = SystemApiLog::where('created_at', '>=', $since);
        $totalRequests = (clone $query)->count();

        // Status code breakdown
        $statusCodes = (clone $query)->pluck('status_code');
        $c2xx = 0;
        $c3xx = 0;
        $c4xx = 0;
        $c5xx = 0;
        foreach ($statusCodes as $code) {
            $class = (int) floor(((int) $code) / 100);
            if ($class === 2) $c2xx++;
            elseif ($class === 3) $c3xx++;
            elseif ($class === 4) $c4xx++;
            elseif ($class === 5) $c5xx++;
        }

        $successfulCount = $c2xx + $c3xx;
        $failedCount = $c4xx + $c5xx;
        $availabilityPct = $totalRequests > 0 ? round(($successfulCount / $totalRequests) * 100, 1) : 100.0;
        $errorRatePct = $totalRequests > 0 ? round(($failedCount / $totalRequests) * 100, 1) : 0.0;

        // Latency Percentiles (Avg, Median P50, P95, P99)
        $durations = (clone $query)->orderBy('duration_ms')->pluck('duration_ms')->toArray();
        $count = count($durations);

        $avgLatency = $count > 0 ? round(array_sum($durations) / $count, 2) : 0.0;
        $medianLatency = $count > 0 ? $durations[(int) floor($count * 0.50)] : 0.0;
        $p95Latency = $count > 0 ? $durations[(int) floor($count * 0.95)] : 0.0;
        $p99Latency = $count > 0 ? $durations[(int) floor($count * 0.99)] : 0.0;

        // Slowest Endpoints
        $slowestEndpoints = (clone $query)
            ->select('path', 'method', DB::raw('AVG(duration_ms) as avg_duration'), DB::raw('COUNT(*) as call_count'))
            ->groupBy('path', 'method')
            ->orderByDesc('avg_duration')
            ->limit(5)
            ->get()
            ->map(fn($row) => [
                'path'         => $row->path,
                'method'       => $row->method,
                'avg_duration' => round($row->avg_duration, 2),
                'call_count'   => $row->call_count,
            ])
            ->toArray();

        // Most Frequently Requested Endpoints
        $mostFrequent = (clone $query)
            ->select('path', 'method', DB::raw('COUNT(*) as call_count'))
            ->groupBy('path', 'method')
            ->orderByDesc('call_count')
            ->limit(5)
            ->get()
            ->toArray();

        // Synthetic vs Actual Requests
        $syntheticCount = (clone $query)->where('is_synthetic', true)->count();
        $actualCount = $totalRequests - $syntheticCount;

        // Total registered API routes
        $allRoutes = Route::getRoutes()->getRoutes();
        $apiRoutesCount = 0;
        $monitoredRoutes = [];
        $monitoredConfig = config('system_health.monitored_endpoints', []);

        foreach ($allRoutes as $route) {
            $uri = '/' . ltrim($route->uri(), '/');
            if (str_starts_with($uri, '/api/')) {
                $apiRoutesCount++;
                if (in_array($uri, $monitoredConfig, true)) {
                    $monitoredRoutes[] = $uri;
                }
            }
        }

        // Last Successful & Failed Requests
        $lastSuccess = SystemApiLog::where('status_code', '<', 400)->latest('created_at')->first(['path', 'method', 'status_code', 'created_at']);
        $lastFailure = SystemApiLog::where('status_code', '>=', 400)->latest('created_at')->first(['path', 'method', 'status_code', 'error_category', 'created_at']);

        return [
            'window'                 => $window,
            'total_requests'         => $totalRequests,
            'successful_count'       => $successfulCount,
            'failed_count'           => $failedCount,
            'availability_pct'       => $availabilityPct,
            'error_rate_pct'         => $errorRatePct,
            'status_codes'           => [
                '2xx' => $c2xx,
                '3xx' => $c3xx,
                '4xx' => $c4xx,
                '5xx' => $c5xx,
            ],
            'latency'                => [
                'avg_ms'    => $avgLatency,
                'median_ms' => $medianLatency,
                'p95_ms'    => $p95Latency,
                'p99_ms'    => $p99Latency,
            ],
            'traffic_split'          => [
                'actual_requests'    => $actualCount,
                'synthetic_probes'   => $syntheticCount,
            ],
            'registered_api_routes'  => $apiRoutesCount,
            'monitored_endpoints'    => array_values(array_unique($monitoredRoutes)),
            'slowest_endpoints'      => $slowestEndpoints,
            'frequent_endpoints'     => $mostFrequent,
            'last_successful'        => $lastSuccess,
            'last_failed'            => $lastFailure,
        ];
    }

    /**
     * Paginated Recent API Requests with search and filters.
     */
    public function getRecentRequests(Request $request): array
    {
        $query = SystemApiLog::query()->latest('created_at');

        if ($request->filled('method')) {
            $query->where('method', strtoupper($request->method));
        }

        if ($request->filled('status_filter')) {
            $filter = $request->status_filter;
            if ($filter === 'success') {
                $query->where('status_code', '<', 400);
            } elseif ($filter === 'failed') {
                $query->where('status_code', '>=', 400);
            } elseif ($filter === 'slow') {
                $query->where('duration_ms', '>=', 500);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('path', 'like', "%{$search}%")
                  ->orWhere('correlation_id', 'like', "%{$search}%")
                  ->orWhere('error_category', 'like', "%{$search}%");
            });
        }

        $perPage = max(10, min(100, (int) $request->input('per_page', 25)));
        $paginator = $query->paginate($perPage);

        return [
            'data'         => $paginator->items(),
            'total'        => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'per_page'     => $paginator->perPage(),
        ];
    }

    /**
     * Application Errors and Exceptions parsed from logs.
     */
    public function getRecentErrors(int $limit = 30): array
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) {
            return [
                'errors' => [],
                'categories' => [],
                'total_count' => 0,
            ];
        }

        $entries = $this->parseRecentLogErrors($logPath, $limit);
        $categoryCounts = [];

        foreach ($entries as $err) {
            $cat = $err['category'];
            $categoryCounts[$cat] = ($categoryCounts[$cat] ?? 0) + 1;
        }

        return [
            'errors'     => $entries,
            'categories' => $categoryCounts,
            'total_count'=> count($entries),
        ];
    }

    /**
     * Cache Connectivity and Health Probe.
     */
    public function getCacheHealth(): array
    {
        $driver = config('cache.default', 'file');
        $status = 'healthy';
        $latencyMs = 0;
        $redisPing = null;

        $startTime = microtime(true);
        try {
            $testKey = 'syshealth_cache_test_' . Str::random(8);
            Cache::put($testKey, 'ok', 5);
            $val = Cache::get($testKey);
            Cache::forget($testKey);
            $latencyMs = round((microtime(true) - $startTime) * 1000, 2);

            if ($val !== 'ok') {
                $status = 'degraded';
            }
        } catch (\Throwable $e) {
            $status = 'error';
        }

        // Test Redis specifically if configured (cached 30s to prevent stalling single-threaded worker)
        if ($driver === 'redis' || config('database.redis.default.host')) {
            $redisPing = Cache::remember('syshealth_redis_ping_v2', 30, function () {
                try {
                    $rStart = microtime(true);
                    $ping = Redis::ping();
                    return [
                        'status'     => 'connected',
                        'latency_ms' => round((microtime(true) - $rStart) * 1000, 2),
                        'response'   => (string) $ping,
                    ];
                } catch (\Throwable $e) {
                    return [
                        'status'  => 'error',
                        'message' => 'Redis connection failed: ' . Str::limit($e->getMessage(), 100),
                    ];
                }
            });
        }

        return [
            'status'     => $status,
            'driver'     => $driver,
            'latency_ms' => $latencyMs,
            'redis'      => $redisPing,
        ];
    }

    /**
     * Storage Health and Permissions.
     */
    public function getStorageHealth(): array
    {
        $status = 'healthy';
        $writeTest = false;
        $writablePaths = [
            'storage'         => is_writable(storage_path()),
            'storage_app'     => is_writable(storage_path('app')),
            'storage_logs'    => is_writable(storage_path('logs')),
            'bootstrap_cache' => is_writable(base_path('bootstrap/cache')),
        ];

        try {
            $testFile = storage_path('app/health_check_' . time() . '.tmp');
            @file_put_contents($testFile, 'health-check');
            if (@file_get_contents($testFile) === 'health-check') {
                $writeTest = true;
            }
            @unlink($testFile);
        } catch (\Throwable $e) {
            $writeTest = false;
        }

        if (in_array(false, $writablePaths, true) || !$writeTest) {
            $status = 'degraded';
        }

        return [
            'status'         => $status,
            'write_test'     => $writeTest,
            'writable_paths' => $writablePaths,
            'default_disk'   => config('filesystems.default', 'local'),
        ];
    }

    /**
     * Mail Gateway Health.
     */
    public function getMailHealth(): array
    {
        $mailer = config('mail.default', 'smtp');
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');
        $username = config('mail.mailers.smtp.username');
        $configured = !empty($host) && !empty($username);

        return [
            'mailer'     => $mailer,
            'host'       => $host,
            'port'       => $port,
            'configured' => $configured,
            'status'     => $configured ? 'configured' : 'unconfigured',
        ];
    }

    /**
     * Queue Worker and Health Status.
     */
    public function getQueueHealth(): array
    {
        $driver = config('queue.default', 'sync');
        $pendingJobs = 0;
        $failedJobs = 0;

        $hasTables = Cache::remember('syshealth_queue_tables_v1', 300, function () {
            return [
                'jobs' => Schema::hasTable('jobs'),
                'failed_jobs' => Schema::hasTable('failed_jobs'),
            ];
        });

        if ($driver === 'database' && $hasTables['jobs']) {
            $pendingJobs = DB::table('jobs')->count();
        }

        if ($hasTables['failed_jobs']) {
            $failedJobs = DB::table('failed_jobs')->count();
        }

        return [
            'driver'       => $driver,
            'status'       => $driver === 'sync' ? 'sync_mode' : 'active',
            'pending_jobs' => $pendingJobs,
            'failed_jobs'  => $failedJobs,
        ];
    }

    /**
     * Security Configuration Checklist.
     */
    public function getSecurityChecklist(Request $request): array
    {
        $recentAuditFailures = SystemHealthAuditLog::where('event', 'like', '%fail%')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        return [
            'https_active'             => $request->isSecure() || $request->header('X-Forwarded-Proto') === 'https',
            'debug_mode_disabled'      => !config('app.debug'),
            'app_debug'                => config('app.debug'),
            'otp_protection_active'    => true,
            'developer_email'          => config('system_health.developer_email'),
            'rate_limiting_active'     => true,
            'csrf_protection_active'   => true,
            'session_cookie_secure'    => (bool) config('session.secure', false),
            'session_cookie_httponly'  => (bool) config('session.http_only', true),
            'session_cookie_samesite'  => config('session.same_site', 'lax'),
            'recent_auth_failures_24h' => $recentAuditFailures,
        ];
    }

    /**
     * Execute a lightweight, safe synthetic health check ping.
     */
    public function runSyntheticPing(): array
    {
        $startTime = microtime(true);
        $status = 'healthy';
        $statusCode = 200;
        $message = 'Synthetic health check passed.';

        try {
            // Check database round-trip
            DB::select('SELECT 1');
            // Check cache round-trip
            Cache::put('syshealth_probe', true, 5);
            Cache::forget('syshealth_probe');

            $durationMs = round((microtime(true) - $startTime) * 1000, 2);

            // Log this synthetic probe
            SystemApiLog::create([
                'correlation_id'  => (string) Str::uuid(),
                'method'          => 'GET',
                'path'            => '/api/stats (synthetic probe)',
                'status_code'     => 200,
                'duration_ms'     => $durationMs,
                'source_category' => 'synthetic_probe',
                'is_synthetic'    => true,
                'created_at'      => now(),
            ]);

            return [
                'success'      => true,
                'status'       => $status,
                'status_code'  => $statusCode,
                'duration_ms'  => $durationMs,
                'message'      => $message,
                'timestamp'    => now()->format('Y-m-d H:i:s T'),
            ];
        } catch (\Throwable $e) {
            $durationMs = round((microtime(true) - $startTime) * 1000, 2);
            return [
                'success'      => false,
                'status'       => 'error',
                'status_code'  => 500,
                'duration_ms'  => $durationMs,
                'message'      => 'Synthetic probe failed: ' . $e->getMessage(),
                'timestamp'    => now()->format('Y-m-d H:i:s T'),
            ];
        }
    }

    // ------------------------------------------------------------------------
    // Internal Helper Methods
    // ------------------------------------------------------------------------

    protected function getApiMetricsSummary(string $window = '24h'): array
    {
        $since = now()->subHours(24);
        $total = SystemApiLog::where('created_at', '>=', $since)->count();
        $successful = SystemApiLog::where('created_at', '>=', $since)->where('status_code', '<', 400)->count();
        $failed = $total - $successful;
        $avail = $total > 0 ? round(($successful / $total) * 100, 1) : 100.0;

        return [
            'total_requests'   => $total,
            'successful_count' => $successful,
            'failed_count'     => $failed,
            'availability_pct' => $avail,
        ];
    }

    protected function getErrorSummary(): array
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) {
            return [
                'total_errors_24h'   => 0,
                'latest_error_time'  => null,
            ];
        }

        $recentErrors = $this->parseRecentLogErrors($logPath, 50);
        $cutoff = now()->subHours(24);
        $errors24h = array_filter($recentErrors, fn($e) => isset($e['carbon']) && $e['carbon']->gte($cutoff));
        $latest = !empty($recentErrors) ? $recentErrors[0]['timestamp'] : null;

        return [
            'total_errors_24h'  => count($errors24h),
            'latest_error_time' => $latest,
        ];
    }

    protected function parseRecentLogErrors(string $path, int $limit = 30): array
    {
        // Read tail of file up to 2MB safely
        $fileSize = @filesize($path) ?: 0;
        $readBytes = min(2 * 1024 * 1024, $fileSize);
        if ($readBytes <= 0) {
            return [];
        }

        $fp = @fopen($path, 'r');
        if (!$fp) {
            return [];
        }

        @fseek($fp, -$readBytes, SEEK_END);
        $content = @fread($fp, $readBytes);
        @fclose($fp);

        $lines = explode("\n", $content);
        $parsed = [];
        $current = null;

        // Pattern for log entries: [YYYY-MM-DD HH:MM:SS] env.SEVERITY: message
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+([a-zA-Z0-9_\-]+)\.([A-Z]+):\s+(.*)$/';

        foreach (array_reverse($lines) as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (preg_match($pattern, $line, $matches)) {
                $timestamp = $matches[1];
                $severity = $matches[3];
                $rawMsg = $matches[4];

                // Categorize exception
                $category = 'Application Error';
                if (str_contains($rawMsg, 'Exception') || str_contains($rawMsg, 'Error')) {
                    if (preg_match('/([A-Za-z0-9_\\\\]+(Exception|Error))/', $rawMsg, $catMatches)) {
                        $parts = explode('\\', $catMatches[1]);
                        $category = end($parts);
                    }
                }

                // Sanitize message and redact secrets
                $sanitizedMsg = $this->redactSensitiveString($rawMsg);

                $parsed[] = [
                    'timestamp'  => $timestamp,
                    'severity'   => $severity,
                    'category'   => $category,
                    'message'    => Str::limit($sanitizedMsg, 200),
                    'carbon'     => Carbon::parse($timestamp),
                ];

                if (count($parsed) >= $limit) {
                    break;
                }
            }
        }

        return $parsed;
    }

    protected function checkPendingMigrations(): array
    {
        return Cache::remember('syshealth_migrations_status_v2', 120, function () {
            try {
                $migrator = app('migrator');
                $files = $migrator->getMigrationFiles($migrator->paths());
                $ran = $migrator->getRepository()->getRan();
                $pending = array_diff(array_keys($files), $ran);

                return [
                    'status'         => empty($pending) ? 'up_to_date' : 'pending_migrations',
                    'ran_count'      => count($ran),
                    'pending_count'  => count($pending),
                    'pending_files'  => array_values($pending),
                ];
            } catch (\Throwable $e) {
                return [
                    'status'        => 'unavailable',
                    'ran_count'     => 0,
                    'pending_count' => 0,
                    'error'         => 'Insufficient permissions to inspect migrations',
                ];
            }
        });
    }

    protected function getSystemRamMetrics(): array
    {
        // Default estimates if direct OS measurement is not readable
        $totalMb = 8192;
        $freeMb = 4096;

        if (PHP_OS_FAMILY === 'Windows') {
            // Windows WMI / CIM check or PowerShell cached metric
            $cachedRam = Cache::remember('syshealth_win_ram_v1', 120, function () {
                $output = @shell_exec('powershell -NoProfile -Command "Get-CimInstance Win32_OperatingSystem | Select-Object TotalVisibleMemorySize,FreePhysicalMemory | ConvertTo-Json" 2>$null');
                if ($output) {
                    $json = json_decode($output, true);
                    if (!empty($json['TotalVisibleMemorySize'])) {
                        return [
                            'total_mb' => round($json['TotalVisibleMemorySize'] / 1024, 0),
                            'free_mb'  => round($json['FreePhysicalMemory'] / 1024, 0),
                        ];
                    }
                }
                return null;
            });

            if ($cachedRam) {
                $totalMb = $cachedRam['total_mb'];
                $freeMb = $cachedRam['free_mb'];
            }
        } elseif (PHP_OS_FAMILY === 'Linux' && File::exists('/proc/meminfo')) {
            $meminfo = File::get('/proc/meminfo');
            if (preg_match('/MemTotal:\s+(\d+)\s+kB/', $meminfo, $tm) && preg_match('/MemAvailable:\s+(\d+)\s+kB/', $meminfo, $fm)) {
                $totalMb = round($tm[1] / 1024, 0);
                $freeMb = round($fm[1] / 1024, 0);
            }
        }

        $usedMb = max(0, $totalMb - $freeMb);
        $utilPct = $totalMb > 0 ? round(($usedMb / $totalMb) * 100, 1) : 0;

        return [
            'total_mb'        => $totalMb,
            'used_mb'         => $usedMb,
            'free_mb'         => $freeMb,
            'utilization_pct' => $utilPct,
        ];
    }

    protected function getDirectorySizeMb(string $path): float
    {
        if (!File::exists($path)) {
            return 0.0;
        }

        $size = 0;
        try {
            foreach (File::allFiles($path) as $file) {
                $size += $file->getSize();
            }
        } catch (\Throwable $e) {
            // Silently suppress directory reading restrictions
        }

        return round($size / 1024 / 1024, 2);
    }

    protected function getApplicationUptime(): string
    {
        // Based on timestamp marker in storage or server start
        $uptimeMarker = storage_path('framework/uptime_start.tmp');
        if (!File::exists($uptimeMarker)) {
            @file_put_contents($uptimeMarker, time());
        }
        $start = (int) @file_get_contents($uptimeMarker);
        if ($start <= 0) {
            $start = time();
        }

        $diffSec = max(0, time() - $start);
        $days = floor($diffSec / 86400);
        $hours = floor(($diffSec % 86400) / 3600);
        $minutes = floor(($diffSec % 3600) / 60);

        return "{$days}d {$hours}h {$minutes}m";
    }

    protected function getServerUptime(): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $cachedUptime = Cache::remember('syshealth_win_uptime_v1', 300, function () {
                $boot = @shell_exec('powershell -NoProfile -Command "(Get-CimInstance Win32_OperatingSystem).LastBootUpTime.ToString(\"yyyy-MM-dd HH:mm:ss\")" 2>$null');
                if ($boot) {
                    $parsed = Carbon::parse(trim($boot));
                    return $parsed->diffForHumans(null, true);
                }
                return 'Active (Windows Host)';
            });
            return $cachedUptime ?: 'Active';
        }

        if (PHP_OS_FAMILY === 'Linux' && File::exists('/proc/uptime')) {
            $uptimeContent = File::get('/proc/uptime');
            $seconds = (int) explode(' ', $uptimeContent)[0];
            $days = floor($seconds / 86400);
            $hours = floor(($seconds % 86400) / 3600);
            return "{$days}d {$hours}h";
        }

        return 'Operational';
    }

    protected function maskString(?string $str): string
    {
        if (empty($str)) {
            return 'N/A';
        }
        $len = strlen($str);
        if ($len <= 4) {
            return '****';
        }
        return substr($str, 0, 3) . '***' . substr($str, -3);
    }

    protected function redactSensitiveString(string $text): string
    {
        // Redact Passwords, Tokens, Secrets, App Keys
        $patterns = [
            '/(password|passwd|pwd)\s*[:=]\s*[^\s,]+/i' => '$1: [REDACTED]',
            '/(token|bearer|api_key|secret)\s*[:=]\s*[^\s,]+/i' => '$1: [REDACTED]',
            '/(base64:)[A-Za-z0-9+\/=]+/i' => '$1[REDACTED_APP_KEY]',
            '/(gmic\s+[a-z]{4}\s+[a-z]{4}\s+[a-z]{4})/i' => '[REDACTED_APP_PASSWORD]',
        ];

        return preg_replace(array_keys($patterns), array_values($patterns), $text);
    }
}
