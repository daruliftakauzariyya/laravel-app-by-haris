<?php

return [
    /*
    |--------------------------------------------------------------------------
    | System Health & Diagnostics Portal Configuration
    |--------------------------------------------------------------------------
    |
    | Governs access control, developer verification, telemetry, and health check
    | thresholds for the Darul Ifta Kauzariyya developer diagnostic interface.
    |
    */

    'enabled' => env('SYSTEM_HEALTH_ENABLED', true),

    // Authorized developer email (Strict single-identity access control)
    'developer_email' => env('SYSTEM_HEALTH_DEVELOPER_EMAIL', 'haris.imran.etpa@gmail.com'),

    // Security & OTP Parameters
    'otp_expiration_minutes'    => (int) env('SYSTEM_HEALTH_OTP_EXPIRATION', 5),
    'max_failed_attempts'       => (int) env('SYSTEM_HEALTH_MAX_FAILED_ATTEMPTS', 5),
    'resend_cooldown_seconds'   => (int) env('SYSTEM_HEALTH_RESEND_COOLDOWN', 60),
    'session_lifetime_minutes'  => (int) env('SYSTEM_HEALTH_SESSION_LIFETIME', 60),
    'ip_rate_limit_per_15min'   => (int) env('SYSTEM_HEALTH_IP_RATE_LIMIT', 10),

    // Data Retention
    'telemetry_retention_days'  => (int) env('SYSTEM_HEALTH_RETENTION_DAYS', 30),

    // Health Status Thresholds
    'thresholds' => [
        'api_latency_degraded_ms'  => 500,
        'api_latency_critical_ms'  => 2000,
        'api_error_degraded_pct'   => 5.0,
        'api_error_critical_pct'   => 15.0,
        'disk_degraded_pct'        => 85.0,
        'disk_critical_pct'        => 95.0,
        'memory_degraded_pct'      => 85.0,
        'memory_critical_pct'      => 95.0,
    ],

    // Key Monitored API Endpoints for synthetic probing and status classification
    'monitored_endpoints' => [
        '/api/stats',
        '/api/public/stats',
        '/api/public/search-filters',
        '/api/public/madhabs',
        '/api/public/popular-categories',
        '/api/fatwas',
        '/api/categories',
    ],
];

