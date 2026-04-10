@extends('layouts.public')
@section('title', 'Meet the Muftis — Our Scholar Team')

@section('content')
<div class="team-hero py-5 mb-5" style="background: linear-gradient(135deg, #1a7a4a 0%, #0d4a2d 100%);">
    <div class="container py-4 text-center text-white">
        <h1 class="fw-black display-4 mb-3">Meet the Muftis</h1>
        <p class="lead opacity-75 mx-auto" style="max-width: 700px;">
            A dedicated team of qualified scholars committed to providing authentic Islamic guidance and jurisprudential clarity for the global community.
        </p>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4 justify-content-center">
        @forelse($muftis as $mufti)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="mufti-card h-100 shadow-sm border-0 bg-white">
                    <div class="p-4 text-center">
                        <div class="mufti-avatar-frame mx-auto mb-3">
                            <img src="{{ $mufti->avatar ? asset($mufti->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($mufti->name).'&background=1a7a4a&color=fff' }}" 
                                 class="mufti-img" alt="{{ $mufti->name }}">
                            <div class="verified-badge">
                                <i class="bi bi-patch-check-fill"></i>
                            </div>
                        </div>
                        
                        <h5 class="fw-bold mb-1 text-dark">{{ $mufti->name }}</h5>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-geo-alt me-1"></i>{{ $mufti->country ?? 'Scholar' }}
                        </p>

                        <div class="stats-mini-grid mb-4">
                            <div class="stat-mini">
                                <span class="stat-val">{{ number_format($mufti->published_fatwas_count) }}</span>
                                <span class="stat-lbl text-uppercase">Fatwas</span>
                            </div>
                            <div class="stat-mini border-start">
                                <span class="stat-val">{{ number_format($mufti->mufti_answers_count) }}</span>
                                <span class="stat-lbl text-uppercase">Impact</span>
                            </div>
                        </div>

                        <a href="{{ route('fatwa.mufti.archive', $mufti) }}" class="btn btn-outline-brand btn-sm w-100 rounded-pill py-2 fw-bold">
                            View Portfolio
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="opacity-25 pb-3">
                    <i class="bi bi-people display-1"></i>
                </div>
                <h4 class="text-muted">Our scholars team is currently being prepared.</h4>
            </div>
        @endforelse
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .mufti-card { 
        border-radius: 20px; 
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05) !important;
    }
    .mufti-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }

    .mufti-avatar-frame {
        width: 110px;
        height: 140px;
        position: relative;
        padding: 5px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .mufti-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }
    .verified-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #fff;
        color: #1a7a4a;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .stats-mini-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        background: #f8f9fa;
        border-radius: 12px;
        padding: 10px 0;
    }
    .stat-mini {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .stat-val {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--brand-blue);
        line-height: 1;
        margin-bottom: 2px;
    }
    .stat-lbl {
        font-size: 0.6rem;
        font-weight: 700;
        color: #8898aa;
        letter-spacing: 0.5px;
    }
    .btn-outline-brand {
        border-color: var(--brand-blue);
        color: var(--brand-blue);
    }
    .btn-outline-brand:hover {
        background-color: var(--brand-blue);
        color: #fff;
    }
</style>
@endsection
