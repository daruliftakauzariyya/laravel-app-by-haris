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
        // Don't log localhost/testing IPs
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            return;
        }

        // Cache the IP lookup for 24 hours to avoid rate limits
        $geoData = Cache::remember("ip_geo_{$ip}", 86400, function () use ($ip) {
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
}
