@extends('layouts.auth')
@section('title', 'Register')

@php
    $portalStats = \App\Http\Controllers\Api\PublicStatsController::getStatsData();
    $statCards = $portalStats['cards'] ?? [];
@endphp

@push('styles')
<!-- intl-tel-input CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23/build/css/intlTelInput.css">
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
        margin-bottom: 2rem;
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
        max-width: 460px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(28px) saturate(190%);
        -webkit-backdrop-filter: blur(28px) saturate(190%);
        border-radius: 28px;
        border: 1px solid rgba(255, 255, 255, 0.24);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
        padding: 2.25rem 2.25rem;
        color: #ffffff;
        transition: all 0.3s ease;
    }

    .glass-form-label {
        font-size: 0.84rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.92);
        margin-bottom: 0.35rem;
        display: block;
    }

    .glass-form-control {
        width: 100%;
        background: #ffffff !important;
        border: none !important;
        border-radius: 12px !important;
        padding: 0.78rem 1.15rem;
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

    .btn-auth-solid {
        background: var(--auth-primary);
        color: #ffffff;
        border: none;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        font-size: 0.92rem;
        padding: 0.85rem 1.25rem;
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

    .divider-simbile {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 1.25rem 0 1rem;
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
        margin-top: 1.25rem;
        margin-bottom: 0;
    }

    .glass-footer-text a {
        color: #ffffff;
        font-weight: 700;
        text-decoration: underline;
        transition: opacity 0.2s;
    }

    /* intl-tel-input integration */
    .iti-wrapper { width: 100%; }
    .iti { width: 100%; }
    .iti__tel-input {
        width: 100% !important;
        background: #ffffff !important;
        border: none !important;
        border-radius: 12px !important;
        padding: 0.78rem 1.15rem 0.78rem 52px !important;
        font-size: 0.95rem !important;
        color: #0f172a !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }
    .iti__tel-input:focus {
        outline: none !important;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.38) !important;
    }
    .iti__dropdown-content { z-index: 9999; color: #0f172a; }

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
        
        {{-- Left Hero Side: Brand, Community Vision, Numbers Cards --}}
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
                    <i class="bi bi-person-plus-fill"></i> Free Registration
                </span>
                <h1 class="auth-hero-title">
                    Join Our<br>Community
                </h1>
                <p class="auth-hero-subtitle">
                    Create your personal account to submit queries, receive authentic fatwas, and bookmark classical rulings.
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

                <div class="mb-3">
                    <h4 class="fw-bold text-white mb-1">Create Account</h4>
                    <p class="text-white-50 small mb-0">Join Darul Ifta Kauzariyya Portal</p>
                </div>

                <form method="POST" action="{{ route('register') }}" id="registrationForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="glass-form-label">Full Name</label>
                        <input type="text" name="name" class="glass-form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="Your full name" required autofocus>
                        @error('name')<div class="text-warning small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="glass-form-label">Email ID</label>
                        <input type="email" name="email" class="glass-form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', str_contains($loginId, '@') ? $loginId : '') }}" placeholder="your@email.com" required>
                        @error('email')<div class="text-warning small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="glass-form-label">Mobile Number</label>
                        <div class="iti-wrapper">
                            <input type="tel" id="phone" name="mobile"
                                   class="glass-form-control @error('mobile') is-invalid @enderror"
                                   value="{{ old('mobile', !str_contains($loginId, '@') ? $loginId : '') }}"
                                   placeholder="Mobile Number" required>
                        </div>
                        <input type="hidden" name="country_code" id="selected_country_code" value="{{ old('country_code', '+91') }}">
                        @error('mobile')<div class="text-warning small mt-1">{{ $message }}</div>@enderror
                        @error('country_code')<div class="text-warning small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="glass-form-label">Password</label>
                        <input type="password" name="password" id="reg-password" class="glass-form-control @error('password') is-invalid @enderror"
                               placeholder="Min. 8 characters" required>
                        @error('password')<div class="text-warning small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn-auth-solid d-flex align-items-center justify-content-center gap-2" id="submitBtn">
                        <span>Create Account</span>
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
                    <span>Sign up with Google</span>
                </a>

                <p class="glass-footer-text">
                    Already have an account? <a href="{{ route('login') }}">Sign In</a>
                </p>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- intl-tel-input JS -->
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23/build/js/intlTelInput.min.js"></script>
<script>
    let itiInstance;
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('phone');
        const hiddenCode = document.getElementById('selected_country_code');

        itiInstance = window.intlTelInput(phoneInput, {
            utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@23/build/js/utils.js',
            initialCountry: 'auto',
            geoIpLookup: function(callback) {
                fetch('https://ipapi.co/json')
                    .then(r => r.json())
                    .then(data => callback(data.country_code))
                    .catch(() => callback('in'));
            },
            separateDialCode: true,
            searchPlaceholder: 'Search country or code...',
            countrySearch: true,
            formatOnDisplay: false,
        });

        phoneInput.addEventListener('countrychange', function () {
            const dialCode = '+' + itiInstance.getSelectedCountryData().dialCode;
            hiddenCode.value = dialCode;
        });

        document.getElementById('registrationForm').addEventListener('submit', function (e) {
            const dialCode = '+' + itiInstance.getSelectedCountryData().dialCode;
            hiddenCode.value = dialCode;
            
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Creating Account...`;
        });
    });
</script>
@endpush
