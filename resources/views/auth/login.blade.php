@extends('layouts.auth')
@section('title', 'Join')

@php
    $portalStats = \App\Http\Controllers\Api\PublicStatsController::getStatsData();
    $statCards = $portalStats['cards'] ?? [];
@endphp

@push('styles')
<style>
    :root {
        --auth-bg: #080c14;
        --auth-primary: #2563eb;
        --auth-primary-hover: #1d4ed8;
        --auth-gold: #f59e0b;
        --auth-text-main: #f8fafc;
        --auth-text-muted: #94a3b8;
        --auth-border: rgba(255, 255, 255, 0.1);
    }

    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: var(--auth-bg);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: var(--auth-text-main);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* ── Fullscreen Center Container with Subtle Lighting & Kauzariyya BG Art ── */
    .auth-fullscreen-wrap {
        width: 100%;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        background-color: var(--auth-bg);
        padding: 3rem 1.5rem;
        overflow-x: hidden;
    }

    .auth-fullscreen-wrap::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url('{{ asset("assets/img/slider/kauzariyya.jpg") }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0.12;
        filter: grayscale(35%) contrast(115%);
        pointer-events: none;
        z-index: 1;
    }

    .auth-fullscreen-wrap::after {
        content: '';
        position: absolute;
        inset: 0;
        background: 
            radial-gradient(circle at 50% 12%, rgba(37, 99, 235, 0.22) 0%, transparent 55%),
            radial-gradient(circle at 50% 88%, rgba(201, 168, 76, 0.1) 0%, transparent 45%),
            radial-gradient(circle at 50% 50%, rgba(8, 12, 20, 0.72) 0%, #080c14 100%);
        pointer-events: none;
        z-index: 1;
    }

    .auth-centered-box {
        width: 100%;
        max-width: 420px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .auth-logo-center {
        margin-bottom: 2rem;
        text-align: center;
    }

    .auth-logo-link {
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .auth-logo-link:hover {
        opacity: 0.92;
        transform: translateY(-1px);
    }

    .auth-logo-img {
        height: 76px;
        width: auto;
        max-width: 280px;
        object-fit: contain;
        display: block;
        filter: brightness(0) invert(1) drop-shadow(0 4px 18px rgba(0, 0, 0, 0.6));
    }

    /* ── Borderless Clean Form Container ── */
    .auth-form-wrap {
        width: 100%;
        background: transparent;
        border: none;
        box-shadow: none;
        padding: 0;
    }

    .auth-card-header {
        margin-bottom: 1.75rem;
    }

    .auth-card-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.85rem;
        font-weight: 700;
        letter-spacing: -0.03em;
        color: #ffffff;
        margin-bottom: 0.4rem;
    }

    .auth-card-desc {
        font-size: 0.9rem;
        color: var(--auth-text-muted);
        margin-bottom: 0;
        line-height: 1.5;
        font-weight: 400;
    }

    .glass-form-label {
        font-size: 0.82rem;
        font-weight: 500;
        color: #cbd5e1;
        margin-bottom: 0.45rem;
        display: block;
        letter-spacing: 0.01em;
    }

    .glass-form-control {
        width: 100%;
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid var(--auth-border) !important;
        border-radius: 12px !important;
        padding: 0.85rem 1.15rem;
        font-size: 0.92rem;
        font-family: 'Inter', sans-serif;
        font-weight: 400;
        color: #ffffff !important;
        transition: all 0.2s ease;
    }

    .glass-form-control:focus {
        outline: none !important;
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
    }

    .glass-form-control::placeholder {
        color: #64748b;
        font-size: 0.88rem;
    }

    .btn-auth-solid {
        background: var(--auth-primary);
        color: #ffffff;
        border: none;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 600;
        letter-spacing: 0.01em;
        font-size: 0.92rem;
        padding: 0.85rem 1.25rem;
        border-radius: 12px;
        width: 100%;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    }

    .btn-auth-solid:hover, .btn-auth-solid:focus {
        background: var(--auth-primary-hover);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
    }

    .btn-auth-outline {
        background: rgba(255, 255, 255, 0.04);
        color: #e2e8f0;
        border: 1px solid var(--auth-border);
        font-weight: 500;
        font-size: 0.88rem;
        padding: 0.82rem 1.25rem;
        border-radius: 12px;
        width: 100%;
        transition: all 0.2s ease;
    }

    .btn-auth-outline:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.25);
    }

    .divider-simbile {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 1.5rem 0 1.25rem;
    }

    .divider-simbile::before,
    .divider-simbile::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .divider-simbile span {
        padding: 0 14px;
        font-size: 0.76rem;
        font-weight: 500;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .btn-google-glass {
        width: 100%;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--auth-border);
        color: #f1f5f9;
        font-weight: 500;
        font-size: 0.9rem;
        padding: 0.82rem 1.25rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-google-glass:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .glass-footer-text {
        font-size: 0.86rem;
        color: #64748b;
        text-align: center;
        margin-top: 1.6rem;
        margin-bottom: 0;
    }

    .glass-footer-text a {
        color: #93c5fd;
        font-weight: 500;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .glass-footer-text a:hover {
        color: #bfdbfe;
        text-decoration: underline;
    }

    .back-home-link {
        color: #64748b;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .back-home-link:hover {
        color: #94a3b8;
        transform: translateX(-3px);
    }

    .pwa-install-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #94a3b8;
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0.32rem 0.75rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        outline: none;
    }

    .pwa-install-btn:hover {
        background: rgba(37, 99, 235, 0.15);
        border-color: rgba(37, 99, 235, 0.35);
        color: #93c5fd;
        transform: translateY(-1px);
    }

    .pwa-install-btn i {
        font-size: 0.85rem;
        color: #f59e0b;
    }

    /* ── Step 2 User Greeting & Profile (Borderless & Minimal) ── */
    .user-pill-glass {
        background: transparent;
        border: none;
        box-shadow: none;
        padding: 0;
        margin-bottom: 1rem;
        text-align: center;
        position: relative;
    }

    .arabic-greeting {
        font-family: 'Amiri', 'Scheherazade New', 'Noto Naskh Arabic', serif;
        font-size: 1.65rem;
        font-weight: 700;
        color: #f59e0b;
        letter-spacing: 0.02em;
        margin-bottom: 0.2rem;
        text-shadow: 0 2px 12px rgba(245, 158, 11, 0.28);
    }

    .user-greeting-name {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.45rem;
        font-weight: 700;
        color: #ffffff;
        letter-spacing: -0.025em;
        margin-bottom: 0.35rem;
        line-height: 1.3;
    }

    .user-role-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 0.28rem 0.85rem;
        border-radius: 9999px;
        margin-bottom: 0.65rem;
        backdrop-filter: blur(8px);
        transition: all 0.2s ease;
    }

    .user-role-pill.role-admin {
        background: rgba(220, 38, 38, 0.18);
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, 0.45);
        box-shadow: 0 2px 10px rgba(220, 38, 38, 0.2);
    }

    .user-role-pill.role-mufti {
        background: rgba(16, 185, 129, 0.18);
        color: #6ee7b7;
        border: 1px solid rgba(16, 185, 129, 0.45);
        box-shadow: 0 2px 10px rgba(16, 185, 129, 0.2);
    }

    .user-role-pill.role-questioner {
        background: rgba(59, 130, 246, 0.18);
        color: #93c5fd;
        border: 1px solid rgba(59, 130, 246, 0.45);
        box-shadow: 0 2px 10px rgba(59, 130, 246, 0.2);
    }

    .user-avatar-preview {
        width: 92px;
        height: 92px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #3b82f6;
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
    }

    .user-avatar-initial {
        width: 92px;
        height: 92px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        font-weight: 700;
        border: 3px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
    }

    .user-step2-instruction {
        font-size: 0.88rem;
        color: var(--auth-text-muted);
        line-height: 1.55;
        margin-top: 1.15rem;
        margin-bottom: 1.5rem;
        text-align: center;
        font-weight: 400;
    }

    /* ── Step 3 OTP Digits ── */
    .otp-inputs-grid {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin: 18px 0;
    }

    .otp-digit {
        width: 44px;
        height: 50px;
        text-align: center;
        font-family: 'Plus Jakarta Sans', monospace;
        font-size: 1.4rem;
        font-weight: 700;
        border: 1px solid var(--auth-border);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.06);
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        transition: all 0.2s ease;
    }

    .otp-digit:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.1);
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
    }

    .otp-digit.filled {
        background: rgba(34, 197, 94, 0.1);
        border-color: rgba(34, 197, 94, 0.4);
        color: #86efac;
    }

    .password-toggle-btn {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #64748b;
        cursor: pointer;
        padding: 4px;
        z-index: 10;
        font-size: 1.05rem;
        transition: color 0.2s ease;
    }

    .password-toggle-btn:hover {
        color: #ffffff;
    }

    .fade-step {
        animation: fadeInStep 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes fadeInStep {
        from {
            opacity: 0;
            transform: translateY(6px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 576px) {
        .auth-fullscreen-wrap {
            padding: 2.25rem 1.25rem 3rem;
        }
        .auth-logo-center {
            width: 100%;
            margin-bottom: 2rem;
        }
        .auth-logo-link {
            width: 100%;
            justify-content: center;
        }
        .auth-logo-img {
            width: 65vw !important;
            max-width: 65vw !important;
            height: auto !important;
            max-height: none !important;
        }
        .auth-card-title {
            font-size: 1.65rem;
        }
    }
</style>
@endpush

@section('content')
<div class="auth-fullscreen-wrap">
    <div class="auth-centered-box">
        
        {{-- Brand Logo Centered --}}
        <div class="auth-logo-center">
            <a href="{{ route('home') }}" class="auth-logo-link" title="Darul Ifta Kauzariyya">
                <img src="{{ asset('assets/logo/logoblueNew.svg') }}" 
                     alt="Darul Ifta Kauzariyya Logo" 
                     class="auth-logo-img"
                     width="260"
                     height="76"
                     onerror="this.onerror=null; this.src='{{ asset('assets/logo/Darul-Ifta-Logo.png') }}';">
            </a>
        </div>

        {{-- Clean Borderless Form Container --}}
        <div class="auth-form-wrap">

            {{-- Form Header --}}
            <div class="auth-card-header text-center">
                <h1 class="auth-card-title">Join</h1>
                <p class="auth-card-desc">Enter your email or mobile to continue.</p>
            </div>

            {{-- Flash Alerts --}}
            @if(session('error'))
                <div class="alert alert-danger py-2 px-3 small d-flex align-items-center mb-3 rounded-3 border-0 bg-danger text-white">
                    <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning py-2 px-3 small d-flex align-items-center mb-3 rounded-3 border-0 bg-warning text-dark">
                    <i class="bi bi-shield-lock-fill me-2 flex-shrink-0"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if(session('status'))
                <div class="alert alert-success py-2 px-3 small d-flex align-items-center mb-3 rounded-3 border-0 bg-success text-white">
                    <i class="bi bi-check-circle-fill me-2 flex-shrink-0"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- Dynamic Alert --}}
            <div id="dynamicAlert" class="alert d-none py-2 px-3 small d-flex align-items-center mb-3 rounded-3 border-0 text-white" role="alert">
                <i id="dynamicAlertIcon" class="bi me-2 flex-shrink-0"></i>
                <span id="dynamicAlertMessage"></span>
            </div>

            {{-- =========================================================================
                 STEP 1: ENTER EMAIL / IDENTIFIER
                 ========================================================================= --}}
            <div id="stepIdentify" class="fade-step">
                <form id="formIdentify" onsubmit="return false;">
                    <div class="mb-3">
                        <label class="glass-form-label">Email or Mobile Number</label>
                        <input type="text" id="inputLoginId" class="glass-form-control"
                               placeholder="Enter your email or mobile" value="{{ old('login_id') }}" required autofocus autocomplete="username">
                        <div class="invalid-feedback d-block mt-1 small text-warning d-none" id="loginIdFeedback">Please enter your email or mobile number.</div>
                    </div>

                    <button type="submit" id="btnContinue" class="btn-auth-solid d-flex align-items-center justify-content-center gap-2">
                        <span>Continue</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <div class="divider-simbile">
                    <span>or</span>
                </div>

                <a href="{{ route('auth.google') }}" id="btnGoogleAuth" onclick="handleGoogleButtonClick(event)" class="btn-google-glass">
                    <svg width="18" height="18" viewBox="0 0 18 18">
                        <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.616z"/>
                        <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                        <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.175 0 7.55 0 9s.347 2.825.957 4.039l3.007-2.332z"/>
                        <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.166 6.656 3.58 9 3.58z"/>
                    </svg>
                    <span>Sign in with Google</span>
                </a>
            </div>

            {{-- =========================================================================
                 STEP 2: USER IDENTIFIED & METHOD CHOICE (Password vs Email OTP)
                 ========================================================================= --}}
            <div id="stepChooseMethod" class="fade-step d-none">
                {{-- User Profile Header (Borderless Clean) --}}
                <div class="user-pill-glass">
                    <div class="d-flex align-items-center justify-content-start mb-1">
                        <button type="button" id="btnSwitchAccount" class="btn btn-sm btn-link text-white-50 text-decoration-none p-0 d-inline-flex align-items-center gap-1" title="Use another account">
                            <i class="bi bi-arrow-left"></i><span class="small">Change</span>
                        </button>
                    </div>

                    {{-- 1. Arabic Greeting --}}
                    <div class="arabic-greeting">السلام عليكم</div>

                    {{-- 2. User Name & Role Badge --}}
                    <h2 class="user-greeting-name" id="userDisplayName">--</h2>
                    <div class="d-flex justify-content-center">
                        <span id="userDisplayRole" class="user-role-pill role-questioner">
                            <i class="bi bi-person-fill"></i>
                            <span>Questioner</span>
                        </span>
                    </div>
                    <div class="d-none" id="userDisplayEmail">--</div>

                    {{-- 3. Profile Image (Large) --}}
                    <div class="d-flex justify-content-center my-2">
                        <div id="userAvatarContainer">
                            {{-- Injected dynamically --}}
                        </div>
                    </div>

                    {{-- 4. Guidance Text --}}
                    <p class="user-step2-instruction">
                        You can log in either by entering your password or by pressing the "Send OTP" button and using the OTP received in your email inbox.
                    </p>
                </div>

                {{-- Method 1: Password Form --}}
                <form id="formPasswordLogin" method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="login_id" id="hiddenPasswordLoginId">

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="glass-form-label mb-0">Password</label>
                            <a href="{{ route('password.request') }}" class="small text-white-50 text-decoration-none">Forgot password?</a>
                        </div>
                        <div class="position-relative">
                            <input type="password" name="password" id="inputPassword" class="glass-form-control pe-5"
                                   placeholder="Enter your password" required autocomplete="current-password">
                            <button type="button" class="password-toggle-btn" id="btnTogglePassword" tabindex="-1">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMeCheckbox" checked>
                        <label class="form-check-label small text-white-50" for="rememberMeCheckbox">
                            Keep me signed in
                        </label>
                    </div>

                    <button type="submit" id="btnSignInPassword" class="btn-auth-solid d-flex align-items-center justify-content-center gap-2">
                        <span>Sign In</span>
                    </button>
                </form>

                <div class="divider-simbile">
                    <span>or</span>
                </div>

                {{-- Method 2: Email OTP Button --}}
                <button type="button" id="btnRequestOtp" class="btn-auth-outline d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-envelope-paper"></i>
                    <span>Send Login Code to Email</span>
                </button>
            </div>

            {{-- =========================================================================
             STEP 3: 6-DIGIT OTP ENTRY FIELD
             ========================================================================= --}}
            <div id="stepOtp" class="fade-step d-none">
                <div class="text-center mb-3">
                    <span class="badge bg-primary px-3 py-1.5 rounded-pill mb-2">
                        <i class="bi bi-envelope-check-fill me-1"></i> Code Sent
                    </span>
                    <p class="small text-white-50 mb-0">
                        We sent a 6-digit code to<br>
                        <strong class="text-white" id="otpTargetEmail">your email</strong>
                    </p>
                </div>

                {{-- 6-Digit OTP Form --}}
                <form id="formOtpVerify" onsubmit="return false;">
                    <div class="otp-inputs-grid" id="otpInputsGroup">
                        <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]*" autofocus>
                        <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" maxlength="1" class="otp-digit" inputmode="numeric" pattern="[0-9]*">
                    </div>

                    <button type="submit" id="btnVerifyOtp" class="btn-auth-solid d-flex align-items-center justify-content-center gap-2 mt-3">
                        <span>Verify & Sign In</span>
                    </button>
                </form>

                {{-- Timer & Resend --}}
                <div class="text-center mt-3">
                    <div id="otpTimerWrapper" class="small text-white-50">
                        Resend code in <strong class="text-white" id="otpCountdown">45</strong>s
                    </div>
                    <button type="button" id="btnResendOtp" class="btn btn-link text-white fw-bold small p-0 text-decoration-none d-none">
                        <i class="bi bi-arrow-repeat me-1"></i> Resend OTP Code
                    </button>
                </div>

                <div class="text-center mt-4 pt-2 border-top border-white-50">
                    <button type="button" id="btnBackToPassword" class="btn btn-link text-white-50 small text-decoration-none p-0">
                        <i class="bi bi-arrow-left me-1"></i> Back to Password Sign In
                    </button>
                </div>
            </div>

        </div>

        {{-- Bottom Navigation & PWA Install Action --}}
        <div class="auth-bottom-nav d-flex align-items-center justify-content-center gap-3 mt-4 flex-wrap">
            <a href="{{ route('home') }}" class="back-home-link">
                <i class="bi bi-arrow-left"></i>
                <span>Return to Kauzariyya Home</span>
            </a>

            <button type="button" id="btnPwaInstall" class="pwa-install-btn" title="Install Kauzariyya Web App" aria-label="Install App">
                <i class="bi bi-phone"></i>
                <span>Install App</span>
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stepIdentify = document.getElementById('stepIdentify');
    const stepChooseMethod = document.getElementById('stepChooseMethod');
    const stepOtp = document.getElementById('stepOtp');

    const formIdentify = document.getElementById('formIdentify');
    const inputLoginId = document.getElementById('inputLoginId');
    const btnContinue = document.getElementById('btnContinue');
    const loginIdFeedback = document.getElementById('loginIdFeedback');

    const userAvatarContainer = document.getElementById('userAvatarContainer');
    const userDisplayName = document.getElementById('userDisplayName');
    const userDisplayEmail = document.getElementById('userDisplayEmail');
    const btnSwitchAccount = document.getElementById('btnSwitchAccount');

    const formPasswordLogin = document.getElementById('formPasswordLogin');
    const hiddenPasswordLoginId = document.getElementById('hiddenPasswordLoginId');
    const inputPassword = document.getElementById('inputPassword');
    const btnSignInPassword = document.getElementById('btnSignInPassword');
    const btnTogglePassword = document.getElementById('btnTogglePassword');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');

    const btnRequestOtp = document.getElementById('btnRequestOtp');

    const formOtpVerify = document.getElementById('formOtpVerify');
    const otpTargetEmail = document.getElementById('otpTargetEmail');
    const otpDigits = Array.from(document.querySelectorAll('.otp-digit'));
    const btnVerifyOtp = document.getElementById('btnVerifyOtp');
    const otpTimerWrapper = document.getElementById('otpTimerWrapper');
    const otpCountdown = document.getElementById('otpCountdown');
    const btnResendOtp = document.getElementById('btnResendOtp');
    const btnBackToPassword = document.getElementById('btnBackToPassword');

    const dynamicAlert = document.getElementById('dynamicAlert');
    const dynamicAlertIcon = document.getElementById('dynamicAlertIcon');
    const dynamicAlertMessage = document.getElementById('dynamicAlertMessage');

    let currentUser = null;
    let countdownInterval = null;

    function showAlert(type, message) {
        const bgClass = type === 'danger' ? 'bg-danger' : (type === 'success' ? 'bg-success' : 'bg-warning text-dark');
        dynamicAlert.className = `alert ${bgClass} py-2 px-3 small d-flex align-items-center mb-3 rounded-3 border-0 text-white fade-step`;
        dynamicAlertIcon.className = type === 'danger' ? 'bi bi-exclamation-triangle-fill me-2 flex-shrink-0' :
                                     (type === 'success' ? 'bi bi-check-circle-fill me-2 flex-shrink-0' : 'bi bi-shield-lock-fill me-2 flex-shrink-0');
        dynamicAlertMessage.textContent = message;
        dynamicAlert.classList.remove('d-none');
    }

    function hideAlert() {
        dynamicAlert.classList.add('d-none');
    }

    btnTogglePassword.addEventListener('click', function() {
        const isPass = inputPassword.type === 'password';
        inputPassword.type = isPass ? 'text' : 'password';
        togglePasswordIcon.className = isPass ? 'bi bi-eye-slash' : 'bi bi-eye';
    });

    formIdentify.addEventListener('submit', function(e) {
        e.preventDefault();
        const loginId = inputLoginId.value.trim();
        if (!loginId) {
            inputLoginId.classList.add('is-invalid');
            return;
        }
        inputLoginId.classList.remove('is-invalid');
        hideAlert();

        btnContinue.disabled = true;
        btnContinue.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Checking...';

        fetch('{{ route("login.check") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ login_id: loginId })
        })
        .then(res => res.json())
        .then(data => {
            btnContinue.disabled = false;
            btnContinue.innerHTML = '<span>Continue</span> <i class="bi bi-arrow-right"></i>';

            if (data.exists) {
                currentUser = data;
                showChooseMethodStep(data);
            } else {
                const param = loginId.includes('@') ? ('email=' + encodeURIComponent(loginId)) : ('mobile=' + encodeURIComponent(loginId));
                window.location.href = '{{ route("register") }}?' + param + '&login_id=' + encodeURIComponent(loginId);
            }
        })
        .catch(err => {
            btnContinue.disabled = false;
            btnContinue.innerHTML = '<span>Continue</span> <i class="bi bi-arrow-right"></i>';
            showAlert('danger', 'Unable to check account. Please verify your connection.');
        });
    });

    function showChooseMethodStep(user) {
        hideAlert();
        stepIdentify.classList.add('d-none');
        stepOtp.classList.add('d-none');
        stepChooseMethod.classList.remove('d-none');

        if (user.avatar) {
            userAvatarContainer.innerHTML = `<img src="${user.avatar}" alt="${user.name}" class="user-avatar-preview">`;
        } else {
            userAvatarContainer.innerHTML = `<div class="user-avatar-initial">${user.initial || 'U'}</div>`;
        }

        userDisplayName.textContent = user.name;
        userDisplayEmail.textContent = user.email;

        // Populate User Role Badge (Admin, Mufti, Questioner)
        const userDisplayRole = document.getElementById('userDisplayRole');
        if (userDisplayRole) {
            const rawRole = (user.role_raw || user.role || 'questioner').toLowerCase();
            if (rawRole.includes('admin')) {
                userDisplayRole.className = 'user-role-pill role-admin';
                userDisplayRole.innerHTML = '<i class="bi bi-shield-lock-fill"></i><span>Admin</span>';
            } else if (rawRole.includes('mufti')) {
                userDisplayRole.className = 'user-role-pill role-mufti';
                userDisplayRole.innerHTML = '<i class="bi bi-mortarboard-fill"></i><span>Mufti</span>';
            } else {
                userDisplayRole.className = 'user-role-pill role-questioner';
                userDisplayRole.innerHTML = '<i class="bi bi-person-fill"></i><span>Questioner</span>';
            }
        }

        hiddenPasswordLoginId.value = user.email;
        inputPassword.value = '';
        inputPassword.focus();
    }

    btnSwitchAccount.addEventListener('click', function() {
        hideAlert();
        stepChooseMethod.classList.add('d-none');
        stepOtp.classList.add('d-none');
        stepIdentify.classList.remove('d-none');
        inputLoginId.focus();
    });

    formPasswordLogin.addEventListener('submit', function(e) {
        e.preventDefault();
        hideAlert();

        const pwd = inputPassword.value;
        if (!pwd) {
            inputPassword.classList.add('is-invalid');
            return;
        }

        btnSignInPassword.disabled = true;
        btnSignInPassword.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Signing In...';

        fetch('{{ route("login") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                login_id: hiddenPasswordLoginId.value,
                password: pwd,
                remember: document.getElementById('rememberMeCheckbox').checked
            })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(result => {
            btnSignInPassword.disabled = false;
            btnSignInPassword.innerHTML = '<span>Sign In</span>';

            if (result.status === 200 && result.body.success) {
                showAlert('success', result.body.message || 'Login successful!');
                window.location.href = result.body.redirect_url || '/';
            } else {
                showAlert('danger', result.body.message || 'Incorrect password. Please try again.');
                inputPassword.value = '';
                inputPassword.focus();
            }
        })
        .catch(err => {
            btnSignInPassword.disabled = false;
            btnSignInPassword.innerHTML = '<span>Sign In</span>';
            showAlert('danger', 'An unexpected error occurred. Please try again.');
        });
    });

    btnRequestOtp.addEventListener('click', function() {
        if (!currentUser || !currentUser.email) return;

        hideAlert();
        btnRequestOtp.disabled = true;
        btnRequestOtp.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sending Code...';

        fetch('{{ route("login.otp.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ login_id: currentUser.email })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(result => {
            btnRequestOtp.disabled = false;
            btnRequestOtp.innerHTML = '<i class="bi bi-envelope-paper"></i> <span>Send Login Code to Email</span>';

            if (result.status === 200 && result.body.success) {
                showOtpStep(result.body.email || currentUser.email);
            } else {
                showAlert('danger', result.body.message || 'Could not send OTP. Please try password login.');
            }
        })
        .catch(err => {
            btnRequestOtp.disabled = false;
            btnRequestOtp.innerHTML = '<i class="bi bi-envelope-paper"></i> <span>Send Login Code to Email</span>';
            showAlert('danger', 'Unable to send OTP. Please try signing in with your password.');
        });
    });

    function showOtpStep(email) {
        hideAlert();
        stepIdentify.classList.add('d-none');
        stepChooseMethod.classList.add('d-none');
        stepOtp.classList.remove('d-none');

        otpTargetEmail.textContent = email;

        otpDigits.forEach(d => {
            d.value = '';
            d.classList.remove('filled');
        });
        otpDigits[0].focus();

        startOtpCountdown(45);
    }

    function startOtpCountdown(seconds) {
        if (countdownInterval) clearInterval(countdownInterval);

        let remaining = seconds;
        otpCountdown.textContent = remaining;
        otpTimerWrapper.classList.remove('d-none');
        btnResendOtp.classList.add('d-none');

        countdownInterval = setInterval(() => {
            remaining--;
            otpCountdown.textContent = remaining;
            if (remaining <= 0) {
                clearInterval(countdownInterval);
                otpTimerWrapper.classList.add('d-none');
                btnResendOtp.classList.remove('d-none');
            }
        }, 1000);
    }

    btnResendOtp.addEventListener('click', function() {
        btnResendOtp.disabled = true;
        btnResendOtp.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';
        hideAlert();

        fetch('{{ route("login.otp.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ login_id: currentUser.email })
        })
        .then(res => res.json())
        .then(data => {
            btnResendOtp.disabled = false;
            btnResendOtp.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Resend OTP Code';
            if (data.success) {
                showAlert('success', 'A new 6-digit code has been sent to your email.');
                startOtpCountdown(45);
            } else {
                showAlert('danger', data.message || 'Failed to resend code.');
            }
        })
        .catch(err => {
            btnResendOtp.disabled = false;
            btnResendOtp.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Resend OTP Code';
            showAlert('danger', 'Unable to resend code. Please try again.');
        });
    });

    btnBackToPassword.addEventListener('click', function() {
        if (countdownInterval) clearInterval(countdownInterval);
        showChooseMethodStep(currentUser);
    });

    otpDigits.forEach((digitInput, index) => {
        digitInput.addEventListener('input', function(e) {
            const val = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = val;

            if (val) {
                digitInput.classList.add('filled');
                if (index < otpDigits.length - 1) {
                    otpDigits[index + 1].focus();
                }
            } else {
                digitInput.classList.remove('filled');
            }

            const fullCode = getEnteredOtp();
            if (fullCode.length === 6) {
                submitOtpVerification(fullCode);
            }
        });

        digitInput.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !digitInput.value && index > 0) {
                otpDigits[index - 1].focus();
            } else if (e.key === 'ArrowLeft' && index > 0) {
                otpDigits[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < otpDigits.length - 1) {
                otpDigits[index + 1].focus();
            }
        });

        digitInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
            const numbersOnly = pasteData.replace(/[^0-9]/g, '').slice(0, 6);

            if (numbersOnly.length > 0) {
                numbersOnly.split('').forEach((num, i) => {
                    if (otpDigits[i]) {
                        otpDigits[i].value = num;
                        otpDigits[i].classList.add('filled');
                    }
                });

                const lastFilledIndex = Math.min(numbersOnly.length - 1, otpDigits.length - 1);
                otpDigits[lastFilledIndex].focus();

                if (numbersOnly.length === 6) {
                    submitOtpVerification(numbersOnly);
                }
            }
        });
    });

    function getEnteredOtp() {
        return otpDigits.map(d => d.value.trim()).join('');
    }

    formOtpVerify.addEventListener('submit', function() {
        const fullCode = getEnteredOtp();
        if (fullCode.length !== 6) {
            showAlert('warning', 'Please enter all 6 digits of your verification code.');
            return;
        }
        submitOtpVerification(fullCode);
    });

    function submitOtpVerification(code) {
        hideAlert();
        btnVerifyOtp.disabled = true;
        btnVerifyOtp.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Verifying...';

        fetch('{{ route("login.otp.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: currentUser.email,
                otp: code
            })
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(result => {
            btnVerifyOtp.disabled = false;
            btnVerifyOtp.innerHTML = '<span>Verify & Sign In</span>';

            if (result.status === 200 && result.body.success) {
                showAlert('success', result.body.message || 'Verification successful! Redirecting...');
                window.location.href = result.body.redirect_url || '/';
            } else {
                showAlert('danger', result.body.message || 'Invalid or expired OTP. Please try again.');
                otpDigits.forEach(d => {
                    d.value = '';
                    d.classList.remove('filled');
                });
                otpDigits[0].focus();
            }
        })
        .catch(err => {
            btnVerifyOtp.disabled = false;
            btnVerifyOtp.innerHTML = '<span>Verify & Sign In</span>';
            showAlert('danger', 'Verification error. Please check your internet connection.');
        });
    }
});

// ── Google Identity Services (GSI) Popup & One-Tap Handler ──
let googleTokenClient = null;

function initGoogleSignIn() {
    const clientId = "{{ config('services.google.client_id') }}";
    if (!clientId || typeof google === 'undefined' || !google.accounts) return;

    try {
        // 1. Initialize Google ID (One-Tap & ID Token)
        google.accounts.id.initialize({
            client_id: clientId,
            callback: handleGoogleIdTokenResponse,
            auto_select: false,
            cancel_on_tap_outside: true
        });

        // 2. Initialize OAuth 2.0 Token Client for Popup on Button Click
        if (google.accounts.oauth2) {
            googleTokenClient = google.accounts.oauth2.initTokenClient({
                client_id: clientId,
                scope: 'email profile https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
                callback: handleGoogleAccessTokenResponse,
            });
        }
    } catch (err) {
        console.warn('Google Auth Init Warning:', err);
    }
}

function handleGoogleButtonClick(e) {
    if (e) e.preventDefault();
    if (googleTokenClient) {
        googleTokenClient.requestAccessToken({ prompt: 'select_account' });
    } else if (typeof google !== 'undefined' && google.accounts && google.accounts.id) {
        google.accounts.id.prompt();
    } else {
        window.location.href = "{{ route('auth.google') }}";
    }
}

function handleGoogleIdTokenResponse(response) {
    if (response && response.credential) {
        sendGoogleAuthToServer({ credential: response.credential });
    }
}

function handleGoogleAccessTokenResponse(tokenResponse) {
    if (tokenResponse && tokenResponse.access_token) {
        sendGoogleAuthToServer({ access_token: tokenResponse.access_token });
    } else if (tokenResponse && tokenResponse.error) {
        console.error('Google Sign-in Error:', tokenResponse);
    }
}

function sendGoogleAuthToServer(payload) {
    const dynamicAlert = document.getElementById('dynamicAlert');
    const dynamicAlertMsg = document.getElementById('dynamicAlertMessage');
    const dynamicAlertIcon = document.getElementById('dynamicAlertIcon');

    if (dynamicAlert) {
        dynamicAlert.className = 'alert py-2 px-3 small d-flex align-items-center mb-3 rounded-3 border-0 bg-primary text-white';
        dynamicAlertMsg.innerText = 'Signing in with Google...';
        dynamicAlertIcon.className = 'spinner-border spinner-border-sm me-2 flex-shrink-0';
        dynamicAlert.classList.remove('d-none');
    }

    fetch("{{ route('auth.google.token') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (dynamicAlert) {
                dynamicAlert.className = 'alert py-2 px-3 small d-flex align-items-center mb-3 rounded-3 border-0 bg-success text-white';
                dynamicAlertMsg.innerText = data.message || 'Signed in! Redirecting...';
                dynamicAlertIcon.className = 'bi bi-check-circle-fill me-2 flex-shrink-0';
            }
            window.location.href = data.redirect || "{{ route('user.dashboard') }}";
        } else {
            if (dynamicAlert) {
                dynamicAlert.className = 'alert py-2 px-3 small d-flex align-items-center mb-3 rounded-3 border-0 bg-danger text-white';
                dynamicAlertMsg.innerText = data.message || 'Google sign-in failed. Please try again.';
                dynamicAlertIcon.className = 'bi bi-exclamation-triangle-fill me-2 flex-shrink-0';
            } else {
                alert(data.message || 'Google sign-in failed.');
            }
        }
    })
    .catch(err => {
        if (dynamicAlert) {
            dynamicAlert.className = 'alert py-2 px-3 small d-flex align-items-center mb-3 rounded-3 border-0 bg-danger text-white';
            dynamicAlertMsg.innerText = 'Network error during Google sign-in. Please try again.';
            dynamicAlertIcon.className = 'bi bi-exclamation-triangle-fill me-2 flex-shrink-0';
        }
    });
}

window.addEventListener('load', function() {
    initGoogleSignIn();
    initPwaInstall();
});

// ── PWA Installation Logic ──
let deferredPwaPrompt = null;

function initPwaInstall() {
    const pwaBtn = document.getElementById('btnPwaInstall');
    if (!pwaBtn) return;

    // Register Service Worker if supported
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(function(err) {
            console.log('SW registration error:', err);
        });
    }

    // Check if already in standalone mode
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
    if (isStandalone) {
        pwaBtn.style.display = 'none';
        return;
    }

    // Capture install prompt
    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPwaPrompt = e;
        pwaBtn.style.display = 'inline-flex';
    });

    window.addEventListener('appinstalled', function() {
        deferredPwaPrompt = null;
        pwaBtn.style.display = 'none';
    });

    pwaBtn.addEventListener('click', async function() {
        if (deferredPwaPrompt) {
            deferredPwaPrompt.prompt();
            const { outcome } = await deferredPwaPrompt.userChoice;
            if (outcome === 'accepted') {
                pwaBtn.style.display = 'none';
            }
            deferredPwaPrompt = null;
        } else {
            // iOS or unsupported fallback hint
            const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            if (isIos) {
                alert('To install the app on iOS:\n1. Tap the Share button (square with arrow)\n2. Select "Add to Home Screen"');
            } else {
                alert('To install the app:\nUse your browser menu (⋮) and tap "Install app" or "Add to Home screen".');
            }
        }
    });
}
</script>
@endpush
