<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device',
        'platform',
        'browser',
        'location',
        'login_at',
        'logout_at',
        'last_activity_at',
        'duration_seconds',
        'is_active',
    ];

    protected $casts = [
        'login_at'         => 'datetime',
        'logout_at'        => 'datetime',
        'last_activity_at' => 'datetime',
        'duration_seconds' => 'integer',
        'is_active'        => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a new login session with device and browser detection.
     */
    public static function logLogin(User $user, ?string $ip = null, ?string $userAgent = null): self
    {
        $ip = $ip ?? request()->ip();
        $userAgent = $userAgent ?? request()->userAgent();

        // Close any previously hanging active sessions for this user
        static::where('user_id', $user->id)
            ->where('is_active', true)
            ->get()
            ->each(function ($session) {
                $session->closeSession();
            });

        $deviceInfo = static::parseUserAgent($userAgent);
        
        $location = $user->state ? ($user->state . ', ' . ($user->country ?? 'India')) : ($user->country ?? 'India');

        return static::create([
            'user_id'          => $user->id,
            'ip_address'       => $ip,
            'user_agent'       => $userAgent,
            'device'           => $deviceInfo['device'],
            'platform'         => $deviceInfo['platform'],
            'browser'          => $deviceInfo['browser'],
            'location'         => $location,
            'login_at'         => now(),
            'last_activity_at' => now(),
            'duration_seconds' => 0,
            'is_active'        => true,
        ]);
    }

    /**
     * Update activity heartbeat on current active session.
     */
    public function recordHeartbeat(): void
    {
        if (!$this->is_active) {
            return;
        }

        $now = now();
        $duration = max(0, $now->diffInSeconds($this->login_at));

        $this->updateQuietly([
            'last_activity_at' => $now,
            'duration_seconds' => $duration,
        ]);
    }

    /**
     * Close the login session upon logout or timeout.
     */
    public function closeSession(): void
    {
        $now = now();
        $end = $this->last_activity_at ?? $now;
        $duration = max(0, $end->diffInSeconds($this->login_at));

        $this->updateQuietly([
            'logout_at'        => $end,
            'duration_seconds' => $duration,
            'is_active'        => false,
        ]);
    }

    /**
     * Parse device, platform and browser from User Agent string.
     */
    public static function parseUserAgent(?string $ua): array
    {
        $ua = $ua ?? '';
        
        // Device detection
        $device = 'Desktop';
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $ua)) {
            $device = 'Tablet';
        } elseif (preg_match('/(mobile|iphone|ipod|blackberry|opera mini|iemobile|wpdesktop)/i', $ua) || preg_match('/android.*mobile/i', $ua)) {
            $device = 'Mobile Device';
        }

        // Platform / OS detection
        $platform = 'Unknown OS';
        if (preg_match('/windows nt 10/i', $ua))     $platform = 'Windows 10/11';
        elseif (preg_match('/windows/i', $ua))       $platform = 'Windows';
        elseif (preg_match('/android/i', $ua))       $platform = 'Android';
        elseif (preg_match('/iphone|ipad|ipod/i', $ua)) $platform = 'iOS';
        elseif (preg_match('/macintosh|mac os x/i', $ua)) $platform = 'macOS';
        elseif (preg_match('/linux/i', $ua))         $platform = 'Linux';

        // Browser detection
        $browser = 'Web Browser';
        if (preg_match('/edg/i', $ua))            $browser = 'Edge';
        elseif (preg_match('/chrome/i', $ua))     $browser = 'Chrome';
        elseif (preg_match('/firefox/i', $ua))    $browser = 'Firefox';
        elseif (preg_match('/safari/i', $ua))     $browser = 'Safari';
        elseif (preg_match('/opera|opr/i', $ua))  $browser = 'Opera';

        return [
            'device'   => $device,
            'platform' => $platform,
            'browser'  => $browser,
        ];
    }
}
