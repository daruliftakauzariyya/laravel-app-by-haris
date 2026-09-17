@extends('layouts.auth')
@section('title', 'Sign In')

@php
    $portalStats = \App\Http\Controllers\Api\PublicStatsController::getStatsData();
    $statCards = $portalStats['cards'] ?? [];
@endphp

@push('styles')
<style>
    :root {
        --auth-primary: #2563eb;
        --auth-primary-hover: #1d4ed8;
        --auth-gold: #c9a84c;
        --auth-gold-light: #fef3c7;
    }

    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        background: #050b14;
    }

    /* ── Fullscreen Container with Balanced Side Padding ── */
    .auth-fullscreen-wrap {
        width: 100%;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        background-color: #050b14;
        background-image: 
            linear-gradient(135deg, rgba(5, 11, 20, 0.88) 0%, rgba(2, 6, 12, 0.94) 100%),
            url('{{ asset("assets/img/dark-fiqh-library.jpg") }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        padding: 2.5rem 3.5rem;
        overflow-x: hidden;
    }

    .auth-container-inner {
        width: 100%;
        max-width: 1380px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 3.5rem;
    }

    /* ── Left Hero Section ── */
    .auth-hero-col {
        flex: 1.2;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 1.5rem;
        color: #ffffff;
        position: relative;
        z-index: 2;
    }

    .auth-logo-wrap {
        display: block;
        margin-bottom: 0.5rem;
    }

    .auth-logo-link {
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        transition: transform 0.2s ease;
    }

    .auth-logo-link:hover {
        transform: scale(1.03);
    }

    .auth-logo-img {
        height: 52px;
        width: 175px;
        max-width: 240px;
        object-fit: contain;
        display: block;
        filter: brightness(0) invert(1) drop-shadow(0 2px 10px rgba(0, 0, 0, 0.5));
    }

    .auth-hero-main {
        max-width: 560px;
    }

    .auth-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(201, 168, 76, 0.16);
        border: 1px solid rgba(201, 168, 76, 0.38);
        color: #fce7b2;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        padding: 5px 14px;
        border-radius: 9999px;
        margin-bottom: 1.25rem;
    }

    .auth-hero-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        font-size: 2.75rem;
        line-height: 1.15;
        letter-spacing: -0.5px;
        color: #ffffff;
        margin-bottom: 1rem;
        text-transform: uppercase;
    }

    .auth-hero-subtitle {
        font-size: 1.05rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.82);
        line-height: 1.6;
        margin-bottom: 1.75rem;
    }

    /* ── Real Numbers Stats Grid ── */
    .auth-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        margin-bottom: 2rem;
    }

    .stat-mini-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 18px;
        padding: 1.05rem 1.2rem;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-mini-card:hover {
        background: rgba(255, 255, 255, 0.14);
        transform: translateY(-2px);
        border-color: rgba(201, 168, 76, 0.45);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.3);
    }

    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .stat-icon-gold {
        background: rgba(201, 168, 76, 0.2);
        color: #fce7b2;
        border: 1px solid rgba(201, 168, 76, 0.4);
    }

    .stat-icon-blue {
        background: rgba(37, 99, 235, 0.2);
        color: #93c5fd;
        border: 1px solid rgba(37, 99, 235, 0.4);
    }

    .stat-icon-green {
        background: rgba(34, 197, 94, 0.2);
        color: #86efac;
        border: 1px solid rgba(34, 197, 94, 0.4);
    }

    .stat-icon-purple {
        background: rgba(168, 85, 247, 0.2);
        color: #d8b4fe;
        border: 1px solid rgba(168, 85, 247, 0.4);
    }

    .stat-card-number {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.35rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.15;
        margin-bottom: 2px;
    }

    .stat-card-label {
        font-size: 0.74rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.72);
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .back-home-link {
        color: rgba(255, 255, 255, 0.65);
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .back-home-link:hover {
        color: #ffffff;
        transform: translateX(-4px);
    }

    /* ── Right Form Section (Glassmorphic Card) ── */
    .auth-form-col {
        flex: 0.95;
        display: flex;
        justify-content: flex-end;
        position: relative;
        z-index: 3;
    }

    .glass-auth-card {
        width: 100%;
        max-width: 440px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(28px) saturate(190%);
        -webkit-backdrop-filter: blur(28px) saturate(190%);
        border-radius: 28px;
        border: 1px solid rgba(255, 255, 255, 0.24);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
        padding: 2.5rem 2.25rem;
        color: #ffffff;
        transition: all 0.3s ease;
    }

    .glass-form-label {
        font-size: 0.86rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.92);
        margin-bottom: 0.45rem;
        display: block;
    }

    .glass-form-control {
        width: 100%;
        background: #ffffff !important;
        border: none !important;
        border-radius: 12px !important;
        padding: 0.85rem 1.15rem;
        font-size: 0.95rem;
        font-weight: 500;
        color: #0f172a !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        transition: all 0.2s ease;
    }

    .glass-form-control:focus {
        outline: none !important;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.38), 0 4px 12px rgba(0, 0, 0, 0.2) !important;
    }

    .glass-form-control::placeholder {
        color: #94a3b8;
        font-size: 0.9rem;
    }

    .btn-auth-solid {
        background: var(--auth-primary);
        color: #ffffff;
        border: none;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        font-size: 0.92rem;
        padding: 0.88rem 1.25rem;
        border-radius: 12px;
        width: 100%;
        transition: all 0.2s ease;
        box-shadow: 0 4px 18px rgba(37, 99, 235, 0.35);
    }

    .btn-auth-solid:hover, .btn-auth-solid:focus {
        background: var(--auth-primary-hover);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.5);
    }

    .btn-auth-outline {
        background: rgba(255, 255, 255, 0.14);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.38);
        font-weight: 600;
        font-size: 0.92rem;
        padding: 0.82rem 1.25rem;
        border-radius: 12px;
        width: 100%;
        transition: all 0.2s ease;
    }

    .btn-auth-outline:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.65);
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
        border-bottom: 1px solid rgba(255, 255, 255, 0.25);
    }

    .divider-simbile span {
        padding: 0 12px;
        font-size: 0.82rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.7);
    }

    .btn-google-glass {
        width: 100%;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.8rem 1.25rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-google-glass:hover {
        background: rgba(255, 255, 255, 0.22);
        border-color: rgba(255, 255, 255, 0.6);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .glass-footer-text {
        font-size: 0.86rem;
        color: rgba(255, 255, 255, 0.75);
        text-align: center;
        margin-top: 1.5rem;
        margin-bottom: 0;
    }

    .glass-footer-text a {
        color: #ffffff;
        font-weight: 700;
        text-decoration: underline;
        transition: opacity 0.2s;
    }

    .user-pill-glass {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 18px;
        padding: 1.15rem;
        margin-bottom: 1.25rem;
        text-align: center;
        position: relative;
    }

    .user-avatar-preview {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #60a5fa;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
    }

    .user-avatar-initial {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        font-weight: 700;
        border: 3px solid rgba(255, 255, 255, 0.6);
    }

    .otp-inputs-grid {
        display: flex;
        gap: 8px;
        justify-content: center;
        margin: 18px 0;
    }

    .otp-digit {
        width: 44px;
        height: 52px;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 700;
        border: none;
        border-radius: 10px;
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    .otp-digit:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.38);
    }

    .otp-digit.filled {
        background: #f0fdf4;
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
        font-size: 1.1rem;
    }

    .password-toggle-btn:hover {
        color: #0f172a;
    }

    .fade-step {
        animation: fadeInStep 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
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

    /* ── Mobile Balanced Styling ── */
    @media (max-width: 1199.98px) {
        .auth-fullscreen-wrap {
            padding: 2rem 2rem;
        }
        .auth-container-inner {
            gap: 2rem;
        }
        .auth-hero-title {
            font-size: 2.35rem;
        }
    }

    @media (max-width: 991.98px) {
        .auth-fullscreen-wrap {
            padding: 1.5rem 1.25rem 3rem;
            align-items: flex-start;
        }
        .auth-container-inner {
            flex-direction: column;
            gap: 1.5rem;
        }
        .auth-hero-col {
            flex: none;
            width: 100%;
            text-align: center;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        .auth-logo-link {
            margin-bottom: 1.25rem;
            display: inline-block;
        }
        .auth-logo-img {
            max-height: 48px;
            margin: 0 auto;
        }
        .auth-hero-main {
            max-width: 100%;
        }
        .auth-hero-eyebrow {
            font-size: 0.72rem;
            padding: 4px 12px;
            margin-bottom: 0.85rem;
        }
        .auth-hero-title {
            font-size: 1.85rem;
            margin-bottom: 0.65rem;
        }
        .auth-hero-subtitle {
            font-size: 0.92rem;
            margin-bottom: 1.25rem;
        }
        .auth-stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            width: 100%;
            max-width: 480px;
            margin: 0 auto 1rem;
        }
        .stat-mini-card {
            padding: 0.75rem 0.9rem;
            gap: 10px;
            border-radius: 14px;
        }
        .stat-icon-wrap {
            width: 36px;
            height: 36px;
            font-size: 1.15rem;
            border-radius: 10px;
        }
        .stat-card-number {
            font-size: 1.15rem;
        }
        .stat-card-label {
            font-size: 0.68rem;
        }
        .auth-form-col {
            width: 100%;
            justify-content: center;
        }
        .glass-auth-card {
            max-width: 460px;
            padding: 1.85rem 1.4rem;
            border-radius: 22px;
        }
        .back-home-link {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .auth-fullscreen-wrap {
            padding: 1.25rem 0.85rem 2.5rem;
        }
        .auth-logo-img {
            max-height: 44px;
        }
        .auth-hero-title {
            font-size: 1.55rem;
        }
        .auth-stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        .stat-mini-card {
            padding: 0.65rem 0.75rem;
            gap: 8px;
        }
        .stat-icon-wrap {
            width: 32px;
            height: 32px;
            font-size: 1rem;
        }
        .stat-card-number {
            font-size: 1.05rem;
        }
        .stat-card-label {
            font-size: 0.65rem;
        }
        .glass-auth-card {
            padding: 1.6rem 1.15rem;
            border-radius: 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="auth-fullscreen-wrap">
    <div class="auth-container-inner">
        
        {{-- Left Hero Side: Official Logo, Title, Real Numbers Cards --}}
        <div class="auth-hero-col">
            <div class="auth-logo-wrap">
                <a href="{{ route('home') }}" class="auth-logo-link" title="Darul Ifta Kauzariyya">
                    <img src="{{ asset('assets/logo/logoblueNew.svg') }}" 
                         alt="Darul Ifta Kauzariyya Logo" 
                         class="auth-logo-img"
                         width="175"
                         height="52"
                         onerror="this.onerror=null; this.src='{{ asset('assets/logo/Darul-Ifta-Logo.png') }}';">
                </a>
            </div>

            <div class="auth-hero-main">
                <span class="auth-hero-eyebrow">
                    <i class="bi bi-book-half"></i> Islamic Jurisprudence Portal
                </span>
                <h1 class="auth-hero-title">
                    Authentic<br>Guidance
                </h1>
                <p class="auth-hero-subtitle">
                    Where classical scholarly jurisprudence meets verified everyday verdicts for the global Ummah.
                </p>

                {{-- Dynamic Public Numbers Stats Grid --}}
                <div class="auth-stats-grid">
                    @foreach($statCards as $card)
                    <div class="stat-mini-card">
                        <div class="stat-icon-wrap stat-icon-{{ $card['color'] ?? 'blue' }}">
                            <i class="bi {{ $card['icon'] ?? 'bi-bar-chart' }}"></i>
                        </div>
                        <div>
                            <div class="stat-card-number">{{ $card['formatted'] }}</div>
                            <div class="stat-card-label">{{ $card['label'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div>
                <a href="{{ route('home') }}" class="back-home-link">
                    <i class="bi bi-arrow-left"></i>
                    <span>Return to Kauzariyya Home</span>
                </a>
            </div>
        </div>

        {{-- Right Side: Floating Glassmorphism Form Card --}}
        <div class="auth-form-col">
            <div class="glass-auth-card">

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
                        <div class="mb-4">
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

                    <a href="{{ route('auth.google') }}" class="btn-google-glass">
                        <svg width="18" height="18" viewBox="0 0 18 18">
                            <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.616z"/>
                            <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                            <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.175 0 7.55 0 9s.347 2.825.957 4.039l3.007-2.332z"/>
                            <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.166 6.656 3.58 9 3.58z"/>
                        </svg>
                        <span>Sign in with Google</span>
                    </a>

                    <p class="glass-footer-text">
                        Are you new? <a href="{{ route('register') }}">Create an Account</a>
                    </p>
                </div>

                {{-- =========================================================================
                     STEP 2: USER IDENTIFIED & METHOD CHOICE (Password vs Email OTP)
                     ========================================================================= --}}
                <div id="stepChooseMethod" class="fade-step d-none">
                    {{-- User Profile Header --}}
                    <div class="user-pill-glass">
                        <button type="button" id="btnSwitchAccount" class="btn btn-sm btn-link text-white-50 text-decoration-none position-absolute top-0 start-0 m-2 p-1" title="Use another account">
                            <i class="bi bi-arrow-left me-1"></i><span class="small">Change</span>
                        </button>

                        <div class="mb-2 d-flex justify-content-center">
                            <div id="userAvatarContainer">
                                {{-- Injected dynamically --}}
                            </div>
                        </div>
                        <h6 class="fw-bold text-white mb-0 fs-6" id="userDisplayName">--</h6>
                        <div class="text-white-50 small" id="userDisplayEmail">--</div>
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
                window.location.href = '{{ route("register") }}?login_id=' + encodeURIComponent(loginId);
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
</script>
@endpush
