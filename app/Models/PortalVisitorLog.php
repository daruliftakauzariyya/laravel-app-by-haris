<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PortalVisitorLog extends Model
{
    protected $guarded = [];

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

        self::create([
            'ip_address' => $ip,
            'page' => $url,
            'user_agent' => substr(request()->userAgent(), 0, 500),
            'country' => $geoData['country'] ?? 'Unknown',
            'country_code' => $geoData['country_code'] ?? null,
            'region' => $geoData['region'] ?? null,
            'city' => $geoData['city'] ?? null,
        ]);
    }
}
