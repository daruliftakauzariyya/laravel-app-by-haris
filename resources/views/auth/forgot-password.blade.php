@extends('layouts.auth')
@section('title', 'Forgot Password')

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
        
        {{-- Left Hero Side: Brand, Account Recovery, Numbers Cards --}}
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
                    <i class="bi bi-shield-lock"></i> Account Security
                </span>
                <h1 class="auth-hero-title">
                    Account<br>Recovery
                </h1>
                <p class="auth-hero-subtitle">
                    Regain secure access to your Darul Ifta Kauzariyya account to continue asking and tracking your queries.
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

                <div class="text-center mb-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                         style="width:54px; height:54px; background: rgba(37, 99, 235, 0.25); border: 1px solid rgba(255, 255, 255, 0.35);">
                        <i class="bi bi-key-fill text-white fs-4"></i>
                    </div>
                    <h4 class="fw-bold text-white mb-1">Reset Password</h4>
                    <p class="text-white-50 small mb-0">Enter your email or mobile to receive recovery OTP</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success small py-2 mb-3 bg-success text-white border-0 rounded-3">
                        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="glass-form-label">Email or Mobile Number</label>
                        <input type="text" name="login_id" class="glass-form-control @error('login_id') is-invalid @enderror"
                               value="{{ old('login_id') }}" placeholder="name@example.com" required autofocus>
                        @error('login_id')
                            <div class="text-warning small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-auth-solid">
                        <span>Send Recovery OTP</span>
                    </button>

                    <p class="glass-footer-text">
                        Remember your password? <a href="{{ route('login') }}">Back to Sign In</a>
                    </p>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection
