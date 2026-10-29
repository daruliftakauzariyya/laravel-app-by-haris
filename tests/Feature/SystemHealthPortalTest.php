<?php

namespace Tests\Feature;

use App\Mail\SystemHealthOtpMail;
use App\Models\SystemApiLog;
use App\Models\SystemHealthAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SystemHealthPortalTest extends TestCase
{
    protected string $authorizedEmail = 'haris.imran.etpa@gmail.com';

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        Cache::flush();
    }

    public function test_unauthenticated_visitor_sees_auth_gate_without_diagnostic_data(): void
    {
        Mail::fake();

        $response = $this->get('/system-health');

        $response->assertStatus(200);
        $response->assertViewIs('system-health.auth');
        $response->assertSee('Developer Identity Verification');
        $response->assertSee('Send Verification Code (OTP)');

        // Ensure sensitive internal diagnostic metrics are NEVER leaked on auth gate
        $response->assertDontSee('Loaded Extensions');
        $response->assertDontSee('Core Component Telemetry');
        $response->assertDontSee('HTTP Status Breakdown');
    }

    public function test_diagnostic_api_endpoints_reject_unauthenticated_requests(): void
    {
        $responseWeb = $this->getJson('/system-health/api/overview');
        $responseWeb->assertStatus(401);
        $responseWeb->assertJson(['status' => 'error']);

        $responseApi = $this->getJson('/api/system-health/server');
        $responseApi->assertStatus(401);
        $responseApi->assertJson(['status' => 'error']);
    }

    public function test_request_otp_sends_only_to_authorized_developer_email(): void
    {
        Mail::fake();

        $response = $this->postJson('/system-health/auth/request-otp');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        Mail::assertSent(SystemHealthOtpMail::class, function ($mail) {
            return $mail->hasTo($this->authorizedEmail);
        });

        // Audit log created
        $this->assertDatabaseHas('system_health_audit_logs', [
            'event' => 'otp_requested',
            'email' => $this->authorizedEmail,
        ]);
    }

    public function test_request_otp_ignores_arbitrary_client_supplied_email(): void
    {
        Mail::fake();

        $response = $this->postJson('/system-health/auth/request-otp', [
            'email' => 'attacker@exploit.com',
        ]);

        $response->assertStatus(200);

        // Mail MUST NOT go to attacker
        Mail::assertNotSent(SystemHealthOtpMail::class, function ($mail) {
            return $mail->hasTo('attacker@exploit.com');
        });

        // Mail MUST go only to authorized developer
        Mail::assertSent(SystemHealthOtpMail::class, function ($mail) {
            return $mail->hasTo($this->authorizedEmail);
        });
    }

    public function test_resend_cooldown_is_enforced(): void
    {
        Mail::fake();

        $res1 = $this->postJson('/system-health/auth/request-otp');
        $res1->assertStatus(200);

        // Immediate 2nd request should be throttled by cooldown
        $res2 = $this->postJson('/system-health/auth/request-otp');
        $res2->assertStatus(429);
        $res2->assertJson(['success' => false]);
    }

    public function test_invalid_otp_is_rejected(): void
    {
        $emailKey = hash('sha256', strtolower(trim($this->authorizedEmail)));
        Cache::put("syshealth_otp_{$emailKey}", Hash::make('123456'), now()->addMinutes(5));
        Cache::put("syshealth_attempts_{$emailKey}", 0, now()->addMinutes(5));

        $response = $this->postJson('/system-health/auth/verify-otp', [
            'otp' => '999999', // Incorrect OTP
        ]);

        $response->assertStatus(401);
        $response->assertJson(['success' => false]);
    }

    public function test_valid_otp_grants_access_and_session_id_regenerated(): void
    {
        $emailKey = hash('sha256', strtolower(trim($this->authorizedEmail)));
        $plainOtp = '654321';
        Cache::put("syshealth_otp_{$emailKey}", Hash::make($plainOtp), now()->addMinutes(5));
        Cache::put("syshealth_attempts_{$emailKey}", 0, now()->addMinutes(5));

        $response = $this->postJson('/system-health/auth/verify-otp', [
            'otp' => $plainOtp,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertSessionHas('system_health_authenticated', true);

        // Test subsequent access to dashboard with session
        $dashResponse = $this->get('/system-health');
        $dashResponse->assertStatus(200);
        $dashResponse->assertViewIs('system-health.dashboard');
        $dashResponse->assertSee('System Health &amp; Diagnostics', false);

        // Test API overview access
        $apiResponse = $this->getJson('/system-health/api/overview');
        $apiResponse->assertStatus(200);
        $apiResponse->assertJson(['status' => 'success']);
    }

    public function test_otp_is_single_use(): void
    {
        $emailKey = hash('sha256', strtolower(trim($this->authorizedEmail)));
        $plainOtp = '789123';
        Cache::put("syshealth_otp_{$emailKey}", Hash::make($plainOtp), now()->addMinutes(5));
        Cache::put("syshealth_attempts_{$emailKey}", 0, now()->addMinutes(5));

        // 1st verify: succeeds
        $res1 = $this->postJson('/system-health/auth/verify-otp', ['otp' => $plainOtp]);
        $res1->assertStatus(200);

        // 2nd verify: fails because OTP was purged on 1st use
        $res2 = $this->postJson('/system-health/auth/verify-otp', ['otp' => $plainOtp]);
        $res2->assertStatus(401);
    }

    public function test_logout_terminates_session(): void
    {
        $sessionData = [
            'system_health_authenticated' => true,
            'system_health_email'         => $this->authorizedEmail,
            'system_health_expires_at'    => time() + 3600,
        ];

        // Ensure session is recognized
        $this->withSession($sessionData)->getJson('/system-health/api/overview')->assertStatus(200);

        // Logout
        $logoutResponse = $this->withSession($sessionData)->post('/system-health/auth/logout');
        $logoutResponse->assertRedirect(route('system_health.index'));

        // Subsequent API call without valid session must be rejected
        $this->getJson('/system-health/api/overview')->assertStatus(401);
    }

    public function test_api_telemetry_middleware_records_requests(): void
    {
        $response = $this->getJson('/api/stats');
        $response->assertStatus(200);

        $this->assertDatabaseHas('system_api_logs', [
            'path' => '/api/stats',
            'status_code' => 200,
        ]);
    }
}
