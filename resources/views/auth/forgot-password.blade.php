@extends('layouts.auth')
@section('title', 'Forgot Password')

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
                <h1 class="auth-card-title">Reset Password</h1>
                <p class="auth-card-desc">Enter your email or mobile to receive recovery OTP code.</p>
            </div>

            {{-- Flash Messages --}}
            @if(session('status'))
                <div class="alert alert-success py-2 px-3 small d-flex align-items-center mb-3 rounded-3 border-0 bg-success text-white">
                    <i class="bi bi-check-circle-fill me-2 flex-shrink-0"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

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

            {{-- Forgot Password Form --}}
            <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
                @csrf
                <div class="mb-4">
                    <label class="glass-form-label">Email Address or Mobile Number</label>
                    <input type="text" name="login_id" class="glass-form-control @error('login_id') is-invalid @enderror"
                           value="{{ old('login_id') }}" placeholder="name@example.com or +91..." required autofocus>
                    @error('login_id')
                        <div class="text-warning small mt-1">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn-auth-solid d-flex align-items-center justify-content-center gap-2" id="submitBtn">
                    <span>Send Recovery OTP</span>
                    <i class="bi bi-arrow-right"></i>
                </button>

                <p class="glass-footer-text">
                    Remember your password? <a href="{{ route('login') }}">Back to Sign In</a>
                </p>
            </form>

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
        const form = document.getElementById('forgotPasswordForm');
        const btn = document.getElementById('submitBtn');
        if (form && btn) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sending OTP...';
            });
        }
        initPwaInstall();
    });

    // ── PWA Installation Logic ──
    let deferredPwaPrompt = null;

    function initPwaInstall() {
        const pwaBtn = document.getElementById('btnPwaInstall');
        if (!pwaBtn) return;

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(function(err) {
                console.log('SW registration error:', err);
            });
        }

        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
        if (isStandalone) {
            pwaBtn.style.display = 'none';
            return;
        }

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
