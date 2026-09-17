@extends('layouts.auth')
@section('title', 'Reset Password')

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

    .auth-bottom-nav {
        margin-top: 2rem;
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

    @media (max-width: 576px) {
        .auth-fullscreen-wrap {
            padding: 2rem 1rem 3rem;
        }
        .auth-logo-img {
            max-height: 56px;
        }
        .auth-card-title {
            font-size: 1.6rem;
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
                <h1 class="auth-card-title">New Password</h1>
                <p class="auth-card-desc">Enter the OTP sent to <strong class="text-white">{{ $email }}</strong> and choose a new password.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success py-2 px-3 small d-flex align-items-center mb-3 rounded-3 border-0 bg-success text-white">
                    <i class="bi bi-check-circle-fill me-2 flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger py-2 px-3 small d-flex align-items-center mb-3 rounded-3 border-0 bg-danger text-white">
                    <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-3">
                    <label class="glass-form-label">Recovery OTP</label>
                    <input type="text" name="otp" class="glass-form-control text-center fw-bold @error('otp') is-invalid @enderror" 
                           placeholder="6-digit OTP" maxlength="6" style="letter-spacing: 6px; font-size: 1.15rem;" required autofocus>
                    @error('otp')<div class="text-warning small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="glass-form-label">New Password</label>
                    <input type="password" name="password" class="glass-form-control @error('password') is-invalid @enderror" 
                           placeholder="At least 8 characters" required>
                    @error('password')<div class="text-warning small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="glass-form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="glass-form-control" 
                           placeholder="Repeat new password" required>
                </div>
                
                <button type="submit" class="btn-auth-solid d-flex align-items-center justify-content-center gap-2">
                    <span>Update & Sign In</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <p class="glass-footer-text">
                Back to <a href="{{ route('login') }}">Sign In</a>
            </p>

        </div>

        {{-- Bottom Navigation --}}
        <div class="auth-bottom-nav text-center">
            <a href="{{ route('home') }}" class="back-home-link">
                <i class="bi bi-arrow-left"></i>
                <span>Return to Kauzariyya Home</span>
            </a>
        </div>

    </div>
</div>
@endsection
