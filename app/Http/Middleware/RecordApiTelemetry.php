<?php

namespace App\Http\Middleware;

use App\Models\SystemApiLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RecordApiTelemetry
{
    /**
     * Handle incoming API request and measure execution latency.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $correlationId = (string) ($request->header('X-Correlation-ID') ?: Str::uuid());

        /** @var Response $response */
        $response = $next($request);

        $durationMs = round((microtime(true) - $startTime) * 1000, 2);

        // Append correlation and duration headers
        $response->headers->set('X-Correlation-ID', $correlationId);
        $response->headers->set('X-Response-Time-Ms', (string) $durationMs);

        // Record telemetry asynchronously or safely in background
        $this->recordTelemetry($request, $response, $durationMs, $correlationId);

        return $response;
    }

    /**
     * Safely persist telemetry record to database.
     */
    protected function recordTelemetry(Request $request, Response $response, float $durationMs, string $correlationId): void
    {
        try {
            $path = '/' . ltrim($request->path(), '/');
            $statusCode = $response->getStatusCode();
            $isSynthetic = $request->header('X-System-Health-Probe') === '1' || str_contains($path, 'synthetic-ping');

            // Categorize source
            $sourceCategory = 'public_api';
            $userAgent = $request->userAgent() ?? '';
            if (str_contains($path, '/app/') || str_contains(strtolower($userAgent), 'okhttp') || str_contains(strtolower($userAgent), 'dart')) {
                $sourceCategory = 'mufti_mobile_app';
            } elseif ($isSynthetic) {
                $sourceCategory = 'synthetic_probe';
            } elseif (str_contains(strtolower($userAgent), 'mozilla') || str_contains(strtolower($userAgent), 'chrome')) {
                $sourceCategory = 'web_browser';
            }

            // Categorize error if not 2xx/3xx
            $errorCategory = null;
            if ($statusCode >= 400 && $statusCode < 500) {
                if ($statusCode === 401 || $statusCode === 403) {
                    $errorCategory = 'Authentication / Authorization';
                } elseif ($statusCode === 404) {
                    $errorCategory = 'Resource Not Found (404)';
                } elseif ($statusCode === 422) {
                    $errorCategory = 'Validation Error (422)';
                } elseif ($statusCode === 429) {
                    $errorCategory = 'Rate Limit Throttled (429)';
                } else {
                    $errorCategory = "Client Error ({$statusCode})";
                }
            } elseif ($statusCode >= 500) {
                $errorCategory = "Server Error ({$statusCode})";
            }

            // Normalize path to replace dynamic IDs with {id} for aggregation
            $normalizedPath = preg_replace('#/\d+#', '/{id}', $path);

            SystemApiLog::create([
                'correlation_id'  => $correlationId,
                'method'          => strtoupper($request->method()),
                'path'            => Str::limit($normalizedPath, 250),
                'status_code'     => $statusCode,
                'duration_ms'     => $durationMs,
                'source_category' => $sourceCategory,
                'is_synthetic'    => $isSynthetic,
                'error_category'  => $errorCategory,
                'ip_address'      => $this->anonymizeIp($request->ip()),
                'created_at'      => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently suppress telemetry persistence failure to never break the user's API call
        }
    }

    /**
     * Privacy-conscious IP anonymization (masks last octet / blocks).
     */
    protected function anonymizeIp(?string $ip): ?string
    {
        if (empty($ip)) {
            return null;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            if (count($parts) === 4) {
                return $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.0';
            }
        } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            if (count($parts) >= 4) {
                return $parts[0] . ':' . $parts[1] . ':' . $parts[2] . '::0';
            }
        }

        return '127.0.0.1';
    }
}

