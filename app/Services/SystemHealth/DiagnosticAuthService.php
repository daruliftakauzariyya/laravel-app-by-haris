<?php

namespace App\Services\SystemHealth;

use App\Mail\SystemHealthOtpMail;
use App\Models\SystemHealthAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DiagnosticAuthService
{
    /**
     * Get the configured authorized developer email.
     * Prevents arbitrary or client-specified recipient substitution.
     */
    public function getAuthorizedEmail(): string
    {
        return (string) config('system_health.developer_email', 'haris.imran.etpa@gmail.com');
    }

    /**
     * Check if an active, unexpired OTP currently exists.
     */
    public function hasActiveOtp(): bool
    {
        $email = $this->getAuthorizedEmail();
        $emailKey = hash('sha256', strtolower(trim($email)));
        $otpKey = "syshealth_otp_{$emailKey}";
        return Cache::has($otpKey);
    }

    /**
     * Get remaining cooldown seconds for resending OTP.
     */
    public function getCooldownRemaining(): int
    {
        $email = $this->getAuthorizedEmail();
        $emailKey = hash('sha256', strtolower(trim($email)));
        $cooldownKey = "syshealth_cooldown_{$emailKey}";
        if (Cache::has($cooldownKey)) {
            return max(0, (int) Cache::get($cooldownKey) - time());
        }
        return 0;
    }

    /**
     * Get remaining expiry seconds for current active OTP.
     */
    public function getExpiryRemaining(): int
    {
        $email = $this->getAuthorizedEmail();
        $emailKey = hash('sha256', strtolower(trim($email)));
        $expiryTimeKey = "syshealth_expiry_time_{$emailKey}";
        if (Cache::has($expiryTimeKey)) {
            return max(0, (int) Cache::get($expiryTimeKey) - time());
        }
        return 0;
    }

    /**
     * Automatically trigger OTP when access is initiated,
     * without generating duplicate OTPs on page reloads if an unexpired OTP exists.
     */
    public function initiateAccess(Request $request): array
    {
        $email = $this->getAuthorizedEmail();
        $emailKey = hash('sha256', strtolower(trim($email)));
        $cooldownKey = "syshealth_cooldown_{$emailKey}";
        $otpKey = "syshealth_otp_{$emailKey}";
        $expiryTimeKey = "syshealth_expiry_time_{$emailKey}";

        // If an unexpired OTP already exists and cooldown is active, return existing state without duplicate email
        if (Cache::has($otpKey)) {
            $remainingCooldown = 0;
            if (Cache::has($cooldownKey)) {
                $remainingCooldown = max(0, (int) Cache::get($cooldownKey) - time());
            }
            $remainingExpiry = 300;
            if (Cache::has($expiryTimeKey)) {
                $remainingExpiry = max(0, (int) Cache::get($expiryTimeKey) - time());
            }

            return [
                'success' => true,
                'message' => 'Verification code previously dispatched to authorized developer address.',
                'expires_in_seconds' => $remainingExpiry,
                'cooldown_seconds' => $remainingCooldown,
                'cooldown_remaining' => $remainingCooldown,
                'masked_email' => $this->maskEmail($email),
                'reused' => true,
            ];
        }

        // Otherwise generate and dispatch fresh OTP
        return $this->requestOtp($request);
    }

    /**
     * Request and dispatch an OTP to the authorized developer email.
     */
    public function requestOtp(Request $request): array
    {
        $email = $this->getAuthorizedEmail();
        $emailKey = hash('sha256', strtolower(trim($email)));
        $cooldownKey = "syshealth_cooldown_{$emailKey}";
        $otpKey = "syshealth_otp_{$emailKey}";
        $attemptsKey = "syshealth_attempts_{$emailKey}";
        $expiryTimeKey = "syshealth_expiry_time_{$emailKey}";
        $ipThrottleKey = 'syshealth_ip_req_' . md5($request->ip());

        $cooldownSeconds = (int) config('system_health.resend_cooldown_seconds', 60);
        $ttlMinutes = (int) config('system_health.otp_expiration_minutes', 5);
        $ipRateLimit = (int) config('system_health.ip_rate_limit_per_15min', 10);

        // 1. IP Rate Limiting Check
        $currentIpHits = (int) Cache::get($ipThrottleKey, 0);
        if ($currentIpHits >= $ipRateLimit) {
            $this->logAudit('rate_limit_exceeded', $email, $request, ['reason' => 'IP throttle limit reached']);
            return [
                'success' => false,
                'message' => 'Too many verification attempts from this IP address. Please try again later.',
                'cooldown_remaining' => 900,
            ];
        }

        // 2. Resend Cooldown Check
        if (Cache::has($cooldownKey)) {
            $remaining = (int) Cache::get($cooldownKey) - time();
            if ($remaining > 0) {
                return [
                    'success' => false,
                    'message' => "Please wait {$remaining} seconds before requesting a new verification code.",
                    'cooldown_remaining' => $remaining,
                ];
            }
        }

        // 3. Generate Cryptographically Secure 6-Digit OTP
        try {
            $plainOtp = sprintf('%06d', random_int(100000, 999999));
        } catch (\Throwable $e) {
            $plainOtp = sprintf('%06d', mt_rand(100000, 999999));
        }

        // 4. Invalidate Previous OTP and Store Hashed OTP in Cache
        Cache::forget($otpKey);
        Cache::forget($attemptsKey);
        Cache::forget($expiryTimeKey);

        $hashedOtp = Hash::make($plainOtp);
        Cache::put($otpKey, $hashedOtp, now()->addMinutes($ttlMinutes));
        Cache::put($attemptsKey, 0, now()->addMinutes($ttlMinutes));
        Cache::put($expiryTimeKey, time() + ($ttlMinutes * 60), now()->addMinutes($ttlMinutes));
        Cache::put($cooldownKey, time() + $cooldownSeconds, now()->addSeconds($cooldownSeconds));
        Cache::put($ipThrottleKey, $currentIpHits + 1, now()->addMinutes(15));

        // 5. Deliver Email via Laravel Mail Mechanism
        try {
            Mail::to($email)->send(new SystemHealthOtpMail(
                $plainOtp,
                $request->ip(),
                $request->userAgent() ?? 'Unknown Device',
                $ttlMinutes
            ));

            $this->logAudit('otp_requested', $email, $request, [
                'expires_in_minutes' => $ttlMinutes,
                'mail_sent' => true,
            ]);

            return [
                'success' => true,
                'message' => 'A secure single-use verification code has been dispatched to the authorized developer identity.',
                'expires_in_seconds' => $ttlMinutes * 60,
                'cooldown_seconds' => $cooldownSeconds,
                'masked_email' => $this->maskEmail($email),
            ];
        } catch (\Throwable $e) {
            Log::error('System Health OTP Mail Delivery Failed: ' . $e->getMessage(), [
                'exception' => $e,
                'email' => $email,
            ]);

            $this->logAudit('otp_delivery_failed', $email, $request, [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Unable to deliver verification email. Please verify SMTP gateway configuration.',
                'cooldown_remaining' => 0,
            ];
        }
    }

    /**
     * Verify the user-submitted 6-digit OTP code securely.
     */
    public function verifyOtp(string $otp, Request $request): array
    {
        $email = $this->getAuthorizedEmail();
        $emailKey = hash('sha256', strtolower(trim($email)));
        $otpKey = "syshealth_otp_{$emailKey}";
        $attemptsKey = "syshealth_attempts_{$emailKey}";
        $expiryTimeKey = "syshealth_expiry_time_{$emailKey}";
        $maxAttempts = (int) config('system_health.max_failed_attempts', 5);
        $sessionLifetime = (int) config('system_health.session_lifetime_minutes', 60);

        $otp = preg_replace('/\D/', '', $otp);

        if (strlen($otp) !== 6) {
            return [
                'success' => false,
                'message' => 'The verification code must be exactly 6 digits.',
            ];
        }

        // Check if OTP exists
        if (!Cache::has($otpKey)) {
            $this->logAudit('otp_verify_expired', $email, $request);
            return [
                'success' => false,
                'message' => 'The verification code has expired or does not exist. Please request a new one.',
            ];
        }

        $attempts = (int) Cache::get($attemptsKey, 0) + 1;
        Cache::put($attemptsKey, $attempts, now()->addMinutes(10));

        if ($attempts > $maxAttempts) {
            Cache::forget($otpKey);
            Cache::forget($attemptsKey);
            Cache::forget($expiryTimeKey);
            $this->logAudit('otp_locked_exceeded', $email, $request, ['attempts' => $attempts]);
            return [
                'success' => false,
                'message' => 'Maximum verification attempts exceeded. For security, this code has been permanently invalidated.',
            ];
        }

        $storedHash = Cache::get($otpKey);

        if (Hash::check($otp, $storedHash)) {
            // Success: Invalidate OTP immediately (single use)
            Cache::forget($otpKey);
            Cache::forget($attemptsKey);
            Cache::forget($expiryTimeKey);

            // Session ID regeneration to protect against session fixation
            $request->session()->regenerate();

            $expiresAt = time() + ($sessionLifetime * 60);
            $request->session()->put('system_health_authenticated', true);
            $request->session()->put('system_health_email', $email);
            $request->session()->put('system_health_authenticated_at', now()->toIso8601String());
            $request->session()->put('system_health_expires_at', $expiresAt);

            $this->logAudit('otp_verified_success', $email, $request, [
                'session_expires_at' => date('Y-m-d H:i:s', $expiresAt),
            ]);

            return [
                'success' => true,
                'message' => 'Identity verified. Diagnostic session established.',
                'expires_at' => $expiresAt,
            ];
        }

        $this->logAudit('otp_verify_failed', $email, $request, [
            'attempt' => $attempts,
            'max_attempts' => $maxAttempts,
        ]);

        $remainingAttempts = max(0, $maxAttempts - $attempts);

        return [
            'success' => false,
            'message' => "Invalid verification code. {$remainingAttempts} attempt(s) remaining.",
            'remaining_attempts' => $remainingAttempts,
        ];
    }

    /**
     * Check if current session possesses valid, unexpired diagnostic access.
     */
    public function isAuthenticated(Request $request): bool
    {
        if (!$request->hasSession()) {
            return false;
        }

        $authenticated = $request->session()->get('system_health_authenticated', false);
        $expiresAt = $request->session()->get('system_health_expires_at', 0);

        if ($authenticated && $expiresAt > time()) {
            return true;
        }

        // Expired session cleanup
        if ($authenticated && $expiresAt <= time()) {
            $this->logout($request, true);
        }

        return false;
    }

    /**
     * Explicitly terminate diagnostic session.
     */
    public function logout(Request $request, bool $isExpired = false): void
    {
        $email = $this->getAuthorizedEmail();

        $this->logAudit($isExpired ? 'session_expired' : 'diagnostic_logout', $email, $request);

        if ($request->hasSession()) {
            $request->session()->forget([
                'system_health_authenticated',
                'system_health_email',
                'system_health_authenticated_at',
                'system_health_expires_at',
            ]);
            $request->session()->regenerate();
        }
    }

    /**
     * Mask developer email for UI presentation without revealing full identity.
     */
    public function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '***@***.com';
        }

        $name = $parts[0];
        $domain = $parts[1];

        $len = strlen($name);
        if ($len <= 3) {
            $maskedName = substr($name, 0, 1) . str_repeat('*', max(1, $len - 1));
        } else {
            $maskedName = substr($name, 0, 2) . str_repeat('*', $len - 4) . substr($name, -2);
        }

        return $maskedName . '@' . $domain;
    }

    /**
     * Record security audit log entry.
     */
    protected function logAudit(string $event, string $email, Request $request, array $details = []): void
    {
        try {
            SystemHealthAuditLog::create([
                'event'      => $event,
                'email'      => $email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent() ?? 'Unknown',
                'details'    => $details,
            ]);
        } catch (\Throwable $e) {
            Log::warning("SystemHealth audit log recording failed: {$e->getMessage()}");
        }
    }
}

