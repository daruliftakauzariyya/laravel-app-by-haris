@extends('layouts.auth')
@section('title', 'Register')

@push('styles')
<!-- intl-tel-input CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23/build/css/intlTelInput.css">
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

    /* intl-tel-input Integration with Clean Non-Overlapping Styling */
    .iti-wrapper { width: 100%; position: relative; }
    .iti { width: 100%; position: relative; display: block; }
    .iti input#phone, .iti__tel-input {
        width: 100% !important;
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid var(--auth-border) !important;
        border-radius: 12px !important;
        padding-top: 0.85rem !important;
        padding-bottom: 0.85rem !important;
        padding-right: 1.15rem !important;
        padding-left: 98px !important;
        font-size: 0.92rem !important;
        color: #ffffff !important;
        font-family: 'Inter', sans-serif !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .iti input#phone:focus, .iti__tel-input:focus {
        outline: none !important;
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
    }
    .iti--separate-dial-code .iti__selected-country {
        background-color: transparent !important;
        padding-left: 14px !important;
    }
    .iti__selected-dial-code {
        color: #94a3b8 !important;
        font-size: 0.92rem !important;
        font-weight: 500 !important;
        margin-left: 6px !important;
    }
    .iti__arrow {
        border-top-color: #94a3b8 !important;
    }
    .iti__arrow--up {
        border-bottom-color: #94a3b8 !important;
    }
    .iti__dropdown-content { 
        z-index: 9999; 
        color: #0f172a; 
        border-radius: 12px; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    }

    .password-wrapper {
        position: relative;
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
                <h1 class="auth-card-title">Create Account</h1>
                <p class="auth-card-desc">Join Darul Ifta Kauzariyya Portal to submit questions and explore fatwas.</p>
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

            {{-- Registration Form --}}
            <form method="POST" action="{{ route('register') }}" id="registrationForm">
                @csrf
                
                <div class="mb-3">
                    <label class="glass-form-label">Full Name</label>
                    <input type="text" name="name" class="glass-form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Your full name" required autofocus>
                    @error('name')<div class="text-warning small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="glass-form-label">Email Address</label>
                    <input type="email" name="email" class="glass-form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $email ?? (str_contains($loginId, '@') ? $loginId : '')) }}" placeholder="name@example.com" required>
                    @error('email')<div class="text-warning small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="glass-form-label">Mobile Number</label>
                    <div class="iti-wrapper">
                        <input type="tel" id="phone" name="mobile"
                                class="glass-form-control @error('mobile') is-invalid @enderror"
                                value="{{ old('mobile', $mobile ?? (!str_contains($loginId, '@') ? $loginId : '')) }}"
                                placeholder="Mobile Number" required>
                    </div>
                    <input type="hidden" name="country_code" id="selected_country_code" value="{{ old('country_code', '+91') }}">
                    @error('mobile')<div class="text-warning small mt-1">{{ $message }}</div>@enderror
                    @error('country_code')<div class="text-warning small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="glass-form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="reg-password" class="glass-form-control @error('password') is-invalid @enderror"
                               placeholder="Min. 8 characters" required style="padding-right: 44px;">
                        <button type="button" class="password-toggle-btn" onclick="toggleRegPassword()" aria-label="Toggle password visibility">
                            <i class="bi bi-eye" id="passwordToggleIcon"></i>
                        </button>
                    </div>
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

            {{-- Google OAuth Button --}}
            <a href="{{ route('auth.google') }}" id="btnGoogleAuth" onclick="handleGoogleButtonClick(event)" class="btn-google-glass">
                <svg width="18" height="18" viewBox="0 0 18 18">
                    <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.616z"/>
                    <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                    <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.175 0 7.55 0 9s.347 2.825.957 4.039l3.007-2.332z"/>
                    <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.166 6.656 3.58 9 3.58z"/>
                </svg>
                <span>Sign up with Google</span>
            </a>

            <p class="glass-footer-text">
                Already have an account? <a href="{{ route('login') }}">Back to Join</a>
            </p>

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

        function updatePhonePadding() {
            const selectedCountry = document.querySelector('.iti__selected-country');
            if (selectedCountry && phoneInput) {
                const width = selectedCountry.offsetWidth;
                if (width > 0) {
                    phoneInput.style.setProperty('padding-left', (width + 12) + 'px', 'important');
                }
            }
        }

        phoneInput.addEventListener('countrychange', function () {
            const dialCode = '+' + itiInstance.getSelectedCountryData().dialCode;
            hiddenCode.value = dialCode;
            updatePhonePadding();
        });

        setTimeout(updatePhonePadding, 100);
        setTimeout(updatePhonePadding, 400);

        document.getElementById('registrationForm').addEventListener('submit', function (e) {
            const dialCode = '+' + itiInstance.getSelectedCountryData().dialCode;
            hiddenCode.value = dialCode;
            
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Creating Account...`;
        });
    });

    function toggleRegPassword() {
        const passInput = document.getElementById('reg-password');
        const icon = document.getElementById('passwordToggleIcon');
        if (passInput.type === 'password') {
            passInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

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
        const btn = document.getElementById('btnGoogleAuth');
        if (btn) {
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Signing in...`;
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
                window.location.href = data.redirect || "{{ route('user.dashboard') }}";
            } else {
                alert(data.message || 'Google sign-in failed. Please try again.');
                if (btn) {
                    btn.innerHTML = `<span>Sign up with Google</span>`;
                }
            }
        })
        .catch(err => {
            alert('Network error during Google sign-in. Please try again.');
            if (btn) {
                btn.innerHTML = `<span>Sign up with Google</span>`;
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
