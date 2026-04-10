<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PortalVisitorLog extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a visit for the given IP address and URL.
     * Uses a fast, cached IP lookup to get Country and Region.
     */
    public static function logVisit($ip, $url, $userId = null)
    {
        // Log all visits, but skip geo-lookup for local/testing IPs
        $isLocal = in_array($ip, ['127.0.0.1', '::1']);

        $geoData = $isLocal ? null : Cache::remember("ip_geo_{$ip}", 86400, function () use ($ip) {
            try {
                // Free API, rate limited but usually sufficient for general tracking
                $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}");
                if ($response->successful() && $response->json('status') === 'success') {
                    return [
                        'country' => $response->json('country'),
                        'country_code' => $response->json('countryCode'),
                        'region' => $response->json('regionName'),
                        'city' => $response->json('city'),
                    ];
                }
            } catch (\Exception $e) {
                // Ignore timeout/connection errors
            }
            return null;
        });

        $userAgent = request()->userAgent();
        $deviceInfo = self::parseUserAgent($userAgent);

        self::create([
            'ip_address'       => $ip,
            'user_id'          => $userId,
            'page'             => $url,
            'user_agent'       => substr($userAgent, 0, 500),
            'device_type'      => $deviceInfo['device'],
            'operating_system' => $deviceInfo['os'],
            'browser'          => $deviceInfo['browser'],
            'country'          => $geoData['country'] ?? 'Unknown',
            'country_code'     => $geoData['country_code'] ?? null,
            'region'           => $geoData['region'] ?? null,
            'city'             => $geoData['city'] ?? null,
        ]);
    }

    /** Helper to parse user agent for specific required info */
    private static function parseUserAgent($ua)
    {
        if (!$ua) return ['device' => 'Web', 'os' => 'Other', 'browser' => 'Other'];
        
        $ua = strtolower($ua);
        $device = 'Web'; // Default
        $os = 'Other';
        $browser = 'Other';

        // 1. Device Type
        if (preg_match('/tablet|ipad|playbook|silk/i', $ua)) {
            $device = 'Tab';
        } elseif (preg_match('/mobile|iphone|ipod|android|blackberry|opera mini|windows phone/i', $ua)) {
            $device = 'Mobile';
        }

        // 2. Operating System
        if (str_contains($ua, 'windows')) $os = 'Windows';
        elseif (str_contains($ua, 'android')) $os = 'Android';
        elseif (str_contains($ua, 'iphone') || str_contains($ua, 'ipad') || str_contains($ua, 'ios')) $os = 'iOS';
        elseif (str_contains($ua, 'ubuntu')) $os = 'Ubuntu';
        elseif (str_contains($ua, 'linux')) $os = 'Linux';
        elseif (str_contains($ua, 'macintosh') || str_contains($ua, 'mac os x')) $os = 'macOS';

        // 3. Browser
        if (str_contains($ua, 'edg')) $browser = 'Edge';
        elseif (str_contains($ua, 'chrome')) $browser = 'Chrome';
        elseif (str_contains($ua, 'safari')) $browser = 'Safari';
        elseif (str_contains($ua, 'firefox')) $browser = 'Firefox';
        elseif (str_contains($ua, 'opera') || str_contains($ua, 'opr')) $browser = 'Opera';

        return ['device' => $device, 'os' => $os, 'browser' => $browser];
    }
    /**
     * Calculate total spent time in portal by summing intervals between logs.
     * Intervals > 15 minutes are ignored (assumed session break).
     */
    public static function getTotalSpentTime($userId)
    {
        $logs = self::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get(['created_at']);

        if ($logs->count() < 2) return 0;

        $totalSeconds = 0;
        $prevTime = $logs->first()->created_at;

        foreach ($logs as $log) {
            $currentTime = $log->created_at;
            $diff = $currentTime->diffInSeconds($prevTime);

            // If diff is less than 15 minutes (900 seconds), assume continuous activity
            if ($diff > 0 && $diff < 900) {
                $totalSeconds += $diff;
            }
            $prevTime = $currentTime;
        }

        return $totalSeconds;
    }

    /**
     * Method 1: Count unique IP addresses in the last 15 minutes.
     */
    public static function getLiveUsersCountByIp()
    {
        return self::where('created_at', '>=', now()->subMinutes(15))
            ->distinct('ip_address')
            ->count('ip_address');
    }

    /**
     * Method 2: Count unique identities (User ID if logged in, otherwise IP).
     */
    public static function getLiveUsersCountByIdentity()
    {
        $logs = self::where('created_at', '>=', now()->subMinutes(15))
            ->select('ip_address', 'user_id')
            ->get();

        return $logs->groupBy(function ($log) {
            return $log->user_id ?? $log->ip_address;
        })->count();
    }
}
