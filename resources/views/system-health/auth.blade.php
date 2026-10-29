@extends('system-health.layout')

@section('title', 'System Health Verification Gate')

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100 p-3" style="background: radial-gradient(circle at 50% 20%, #1e293b 0%, #0b1120 70%);">
    <div style="max-width: 480px; width: 100%;">
        <!-- Header Branding -->
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-3" style="background: rgba(2, 132, 199, 0.1); border: 1px solid rgba(2, 132, 199, 0.3);">
                <i class="bi bi-shield-lock-fill text-info" style="font-size: 2.2rem;"></i>
            </div>
            <h2 class="h4 fw-bold text-white mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif;">System Health & Diagnostics</h2>
            <p class="text-light small mb-0" style="color: #cbd5e1 !important; font-size: 0.85rem;">Infrastructure Monitoring &bull; API Diagnostics &bull; Application Health</p>
        </div>

        <!-- Security Card -->
        <div class="sh-card shadow-lg p-4" style="background: #111827; border: 1px solid #334155;">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom" style="border-color: #334155 !important;">
                <span class="pulse-badge pulse-degraded">
                    <span class="pulse-dot"></span> Restricted Access
                </span>
                <span class="text-light small fw-medium" style="color: #cbd5e1 !important;"><i class="bi bi-cpu me-1"></i> Developer Portal</span>
            </div>

            <!-- Notice & Identity Box -->
            <div class="p-3 rounded-3 mb-3" style="background: #1e293b; border: 1px solid #475569;">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-shield-check text-success mt-1" style="font-size: 1.1rem;"></i>
                    <div>
                        <div class="text-white fw-bold small mb-1">Developer Identity Verification</div>
                        <div class="text-light" style="font-size: 0.84rem; line-height: 1.4; color: #cbd5e1 !important;">
                            Authorized Developer Email:
                            <span class="text-info fw-bold mono">{{ $maskedEmail }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Alerts -->
            <div id="alertBox" class="alert d-none small py-2 px-3 mb-3 rounded-3" role="alert"></div>

            <!-- Step 1: Prominent Send OTP Button -->
            <div class="mb-3">
                <button type="button" id="btnMainSendOtp" class="btn btn-primary w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow" style="font-size: 1rem; background: #0284c7; border-color: #0284c7;">
                    <i class="bi bi-send-fill fs-5"></i>
                    <span id="btnMainSendOtpText">{{ $hasActiveOtp ? 'Code Sent (Click to Resend)' : 'Send Verification Code (OTP)' }}</span>
                </button>
            </div>

            <!-- Step 2: OTP Entry Form (Appears after Send OTP succeeds) -->
            <div id="otpEntryContainer" class="{{ $hasActiveOtp ? '' : 'd-none' }} pt-2 border-top" style="border-color: #334155 !important;">
                <form id="otpVerifyForm">
                    @csrf
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label text-light small text-uppercase fw-bold mb-0" style="color: #f1f5f9 !important; letter-spacing: 0.5px;">Enter 6-Digit OTP</label>
                            <span id="countdownTimer" class="small text-warning mono fw-bold"><i class="bi bi-clock me-1"></i>Expires in: <span id="timerText">05:00</span></span>
                        </div>

                        <!-- 6-digit Split Inputs -->
                        <div class="d-flex justify-content-between gap-2" id="otpInputGroup">
                            <input type="text" maxlength="1" class="form-control text-center text-white mono fw-bold otp-digit" style="height: 52px; font-size: 1.5rem; background: #0f172a !important; border: 2px solid #475569 !important;" inputmode="numeric" autocomplete="one-time-code">
                            <input type="text" maxlength="1" class="form-control text-center text-white mono fw-bold otp-digit" style="height: 52px; font-size: 1.5rem; background: #0f172a !important; border: 2px solid #475569 !important;" inputmode="numeric">
                            <input type="text" maxlength="1" class="form-control text-center text-white mono fw-bold otp-digit" style="height: 52px; font-size: 1.5rem; background: #0f172a !important; border: 2px solid #475569 !important;" inputmode="numeric">
                            <input type="text" maxlength="1" class="form-control text-center text-white mono fw-bold otp-digit" style="height: 52px; font-size: 1.5rem; background: #0f172a !important; border: 2px solid #475569 !important;" inputmode="numeric">
                            <input type="text" maxlength="1" class="form-control text-center text-white mono fw-bold otp-digit" style="height: 52px; font-size: 1.5rem; background: #0f172a !important; border: 2px solid #475569 !important;" inputmode="numeric">
                            <input type="text" maxlength="1" class="form-control text-center text-white mono fw-bold otp-digit" style="height: 52px; font-size: 1.5rem; background: #0f172a !important; border: 2px solid #475569 !important;" inputmode="numeric">
                        </div>
                    </div>

                    <!-- Verify Button -->
                    <button type="submit" id="btnVerify" class="btn btn-success w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 mb-2 shadow" style="background: #10b981; border-color: #10b981; font-size: 1rem;">
                        <i class="bi bi-box-arrow-in-right fs-5"></i>
                        <span>Verify & Access Diagnostics</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4">
            <p class="small mb-0" style="font-size: 0.82rem; color: #cbd5e1 !important;">
                <i class="bi bi-shield-lock me-1 text-info"></i> Single-use cryptographic OTP verification &bull; Audit logged with IP telemetry
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const alertBox = document.getElementById('alertBox');
const btnMainSendOtp = document.getElementById('btnMainSendOtp');
const btnRequestOtp = document.getElementById('btnRequestOtp');
const btnVerify = document.getElementById('btnVerify');
const cooldownText = document.getElementById('cooldownText');
const countdownTimer = document.getElementById('countdownTimer');
const timerText = document.getElementById('timerText');
const digits = Array.from(document.querySelectorAll('.otp-digit'));

let cooldownInterval = null;
let expirationInterval = null;

function showAlert(message, type = 'info') {
    if (!alertBox) return;
    alertBox.className = `alert alert-${type} small py-2 px-3 mb-3 rounded-3`;
    alertBox.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'} me-1"></i> ${message}`;
    alertBox.classList.remove('d-none');
}

function hideAlert() {
    if (alertBox) alertBox.classList.add('d-none');
}

// Auto-advance inputs and handle backspace / paste
digits.forEach((digit, idx) => {
    digit.addEventListener('input', (e) => {
        const val = e.target.value.replace(/\D/g, '');
        e.target.value = val;
        if (val && idx < digits.length - 1) {
            digits[idx + 1].focus();
        }
        checkAutoSubmit();
    });

    digit.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && idx > 0) {
            digits[idx - 1].focus();
        }
    });

    digit.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
        pasted.split('').forEach((char, i) => {
            if (digits[i]) digits[i].value = char;
        });
        if (digits[pasted.length - 1]) digits[pasted.length - 1].focus();
        checkAutoSubmit();
    });
});

function getOtpCode() {
    return digits.map(d => d.value).join('');
}

function checkAutoSubmit() {
    if (getOtpCode().length === 6) {
        document.getElementById('otpVerifyForm').dispatchEvent(new Event('submit'));
    }
}

// Trigger Send / Resend OTP Action
async function triggerSendOtp() {
    if (btnMainSendOtp) {
        btnMainSendOtp.disabled = true;
        btnMainSendOtp.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending Code to Email...';
    }
    if (btnRequestOtp) {
        btnRequestOtp.disabled = true;
    }
    hideAlert();

    try {
        const response = await fetch("{{ route('system_health.request_otp') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            showAlert(data.message || 'Verification code dispatched to your authorized developer email.', 'success');
            const container = document.getElementById('otpEntryContainer');
            if (container) {
                container.classList.remove('d-none');
            }
            startCooldown(data.cooldown_seconds || 60);
            startExpiration(data.expires_in_seconds || 300);
            setTimeout(() => digits[0]?.focus(), 150);
        } else {
            showAlert(data.message || 'Unable to dispatch verification code.', 'danger');
            if (data.cooldown_remaining) {
                startCooldown(data.cooldown_remaining);
            } else {
                resetSendButton(false);
            }
        }
    } catch (err) {
        showAlert('Network error communicating with authentication gateway.', 'danger');
        resetSendButton(false);
    }
}

// Attach listener to Send OTP buttons
btnMainSendOtp?.addEventListener('click', triggerSendOtp);
btnRequestOtp?.addEventListener('click', triggerSendOtp);

// Cooldown handler
function startCooldown(seconds) {
    clearInterval(cooldownInterval);
    if (btnRequestOtp) btnRequestOtp.classList.add('d-none');
    if (cooldownText) cooldownText.classList.remove('d-none');

    let rem = seconds;
    const updateCooldownUI = () => {
        if (btnMainSendOtp) {
            btnMainSendOtp.disabled = true;
            btnMainSendOtp.classList.add('disabled');
            btnMainSendOtp.innerHTML = `<i class="bi bi-clock-history me-1"></i>Code Dispatched &bull; Resend in ${rem}s`;
        }
        if (cooldownText) cooldownText.textContent = `Resend in ${rem}s`;
    };
    updateCooldownUI();

    cooldownInterval = setInterval(() => {
        rem--;
        if (rem <= 0) {
            clearInterval(cooldownInterval);
            resetSendButton(true);
        } else {
            updateCooldownUI();
        }
    }, 1000);
}

function resetSendButton(isResend = false) {
    if (btnMainSendOtp) {
        btnMainSendOtp.disabled = false;
        btnMainSendOtp.classList.remove('disabled');
        btnMainSendOtp.innerHTML = `<i class="bi bi-send-fill me-1"></i><span>${isResend ? 'Resend Verification Code (OTP)' : 'Click Here to Send OTP to Email'}</span>`;
    }
    if (btnRequestOtp) {
        btnRequestOtp.classList.remove('d-none');
        btnRequestOtp.disabled = false;
        btnRequestOtp.innerHTML = `<i class="bi bi-arrow-repeat me-1"></i>Resend Code`;
    }
    if (cooldownText) cooldownText.classList.add('d-none');
}

// Expiration Countdown
function startExpiration(seconds) {
    clearInterval(expirationInterval);
    if (countdownTimer) countdownTimer.classList.remove('d-none');

    let rem = seconds;
    const update = () => {
        const mins = String(Math.floor(rem / 60)).padStart(2, '0');
        const secs = String(rem % 60).padStart(2, '0');
        if (timerText) timerText.textContent = `${mins}:${secs}`;
    };
    update();

    expirationInterval = setInterval(() => {
        rem--;
        if (rem <= 0) {
            clearInterval(expirationInterval);
            if (timerText) timerText.textContent = '00:00 (Expired)';
            showAlert('Verification code has expired. Please click Send OTP to request a new code.', 'warning');
            resetSendButton(true);
        } else {
            update();
        }
    }, 1000);
}

// Verify Form Submit
document.getElementById('otpVerifyForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const otp = getOtpCode();

    if (otp.length !== 6) {
        showAlert('Please enter all 6 digits of your verification code.', 'warning');
        return;
    }

    if (btnVerify) {
        btnVerify.disabled = true;
        btnVerify.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying Identity...';
    }
    hideAlert();

    try {
        const response = await fetch("{{ route('system_health.verify_otp') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ otp })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            showAlert('Authentication successful. Establishing diagnostic session...', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 500);
        } else {
            showAlert(data.message || 'Verification failed.', 'danger');
            if (btnVerify) {
                btnVerify.disabled = false;
                btnVerify.innerHTML = '<i class="bi bi-box-arrow-in-right me-1"></i><span>Verify & Access Diagnostics</span>';
            }
            digits.forEach(d => d.value = '');
            digits[0]?.focus();
        }
    } catch (err) {
        showAlert('Network communication error during verification.', 'danger');
        if (btnVerify) {
            btnVerify.disabled = false;
            btnVerify.innerHTML = '<i class="bi bi-box-arrow-in-right me-1"></i><span>Verify & Access Diagnostics</span>';
        }
    }
});

// Auto-trigger initialization on page load
const initialDispatched = {{ $hasActiveOtp ? 'true' : 'false' }};
const initialCooldown = {{ (int) ($cooldownRemaining ?? 0) }};
const initialExpiry = {{ (int) ($expiresInSeconds ?? 300) }};

if (initialDispatched) {
    if (initialCooldown > 0) {
        startCooldown(initialCooldown);
    }
    if (initialExpiry > 0) {
        startExpiration(initialExpiry);
    }
}
setTimeout(() => {
    digits[0]?.focus();
}, 200);
</script>
@endpush

