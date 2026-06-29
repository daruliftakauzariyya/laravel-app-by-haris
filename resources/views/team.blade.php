@extends('layouts.public')
@section('title', 'Meet the Muftis — Our Scholar Team')

@section('content')
<div class="team-hero" style="
    background: url('{{ asset('images/team-hero-bg.png') }}') center center / cover no-repeat;
    position: relative;
    overflow: hidden;
">
    {{-- Dark overlay --}}
    <div style="
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(10,40,22,0.82) 0%, rgba(5,20,12,0.72) 100%);
        backdrop-filter: blur(1px);
    "></div>

    {{-- Arabic geometric top accent --}}
    <div class="hero-geo-accent"></div>

    <div class="container position-relative py-5 text-center text-white">
        {{-- Arabic calligraphy label --}}
        <div class="hero-bismillah-label">&#x202B;فَتَاوَى الْعُلَمَاء&#x202C;</div>

        <h1 class="fw-black display-3 mb-3" style="letter-spacing:-1px; text-shadow: 0 4px 24px rgba(0,0,0,0.5);">
            Meet the Muftis
        </h1>

        {{-- Gold decorative divider --}}
        <div class="hero-divider">
            <span></span><span class="diamond">&#9670;</span><span></span>
        </div>

        <p class="lead opacity-85 mx-auto mt-3" style="max-width: 680px; font-size:1.1rem; line-height:1.7; text-shadow:0 2px 8px rgba(0,0,0,0.4);">
            A dedicated team of qualified scholars committed to providing authentic Islamic guidance and jurisprudential clarity for the global community.
        </p>
    </div>

    {{-- Wavy bottom edge --}}
    <div class="hero-wave-bottom">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#f8f9fa"/>
        </svg>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4 justify-content-center">
        @forelse($muftis as $mufti)
            <div class="col-6 col-md-6 col-lg-4 col-xl-3">
                <div class="scholar-premium-card">
                    {{-- Image Container --}}
                    <div class="scholar-visual">
                        <img src="{{ $mufti->avatar ? asset($mufti->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($mufti->name).'&background=1a7a4a&color=fff&size=512' }}" 
                             class="scholar-image" alt="{{ $mufti->name }}">
                        <div class="verified-ribbon">
                            <i class="bi bi-patch-check-fill"></i>
                        </div>
                    </div>

                    {{-- Content Area --}}
                    <div class="scholar-details">
                        <div class="details-top">
                            <h5 class="scholar-name-heading">{{ $mufti->name }}</h5>
                            <p class="scholar-tagline small text-muted">
                                <i class="bi bi-geo-alt me-1"></i>{{ $mufti->country ?? 'Scholar Faculty' }}
                            </p>
                        </div>

                        <div class="scholar-impact-grid">
                            <div class="impact-node">
                                <span class="node-val">{{ number_format($mufti->published_fatwas_count) }}</span>
                                <span class="node-lbl">Fatwas</span>
                            </div>
                            <div class="impact-node border-start">
                                <span class="node-val">{{ number_format($mufti->mufti_answers_count) }}</span>
                                <span class="node-lbl">Impact</span>
                            </div>
                        </div>

                        <a href="{{ route('fatwa.mufti.archive', $mufti) }}" class="btn-premium-portfolio">
                            View Portfolio <i class="bi bi-arrow-right"></i>
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

    {{-- Pagination removed: all muftis shown at once --}}
</div>

<style>
    .fw-black { font-weight: 900; }

    /* ── Hero ── */
    .team-hero {
        min-height: 380px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding-bottom: 60px; /* room for wave */
    }

    .hero-bismillah-label {
        display: inline-block;
        font-family: 'Georgia', 'Times New Roman', serif;
        font-size: 1.05rem;
        letter-spacing: 3px;
        color: #d4af37;
        border: 1px solid rgba(212,175,55,0.35);
        border-radius: 100px;
        padding: 0.35rem 1.4rem;
        margin-bottom: 1.25rem;
        backdrop-filter: blur(4px);
        background: rgba(212,175,55,0.08);
    }

    .hero-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin: 0.75rem auto;
    }
    .hero-divider span:not(.diamond) {
        display: block;
        width: 80px;
        height: 1px;
        background: linear-gradient(to right, transparent, #d4af37, transparent);
    }
    .hero-divider .diamond {
        color: #d4af37;
        font-size: 0.65rem;
        line-height: 1;
    }

    .hero-geo-accent {
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 15% 50%, rgba(212,175,55,0.06) 0%, transparent 55%),
            radial-gradient(circle at 85% 30%, rgba(26,122,74,0.15) 0%, transparent 55%);
        pointer-events: none;
    }

    .hero-wave-bottom {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        line-height: 0;
    }
    .hero-wave-bottom svg {
        display: block;
        width: 100%;
        height: 60px;
    }
    
    .scholar-premium-card {
        aspect-ratio: 95 / 150;
        background: #fff;
        border-radius: 2.25rem;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        display: flex;
        flex-direction: column;
    }
    .scholar-premium-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 60px rgba(0,0,0,0.1);
    }

    .scholar-visual {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }
    .scholar-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }
    .scholar-premium-card:hover .scholar-image {
        transform: scale(1.05);
    }

    .verified-ribbon {
        position: absolute;
        bottom: 45%;
        right: 1.5rem;
        background: #fff;
        color: #10b981;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        z-index: 5;
    }

    .scholar-details {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 2.5rem 1.75rem 1.5rem;
        background: linear-gradient(to top, 
            rgba(0,0,0,1) 0%, 
            rgba(0,0,0,0.85) 45%, 
            rgba(0,0,0,0.4) 75%, 
            rgba(0,0,0,0) 100%);
        z-index: 3;
    }

    .scholar-name-heading {
        font-weight: 900;
        font-size: 1.15rem;
        color: #ffffff;
        margin-bottom: 2px;
        letter-spacing: -0.5px;
        line-height: 1.1;
    }
    .scholar-tagline {
        font-weight: 700;
        color: rgba(255,255,255,0.7);
        font-size: 0.75rem;
    }

    .scholar-impact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(5px);
        border-radius: 1rem;
        padding: 0.6rem 0;
        margin: 1rem 0;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .impact-node {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .node-val {
        font-size: 1.2rem;
        font-weight: 900;
        color: #ffffff;
        line-height: 1;
    }
    .node-lbl {
        font-size: 0.65rem;
        font-weight: 700;
        color: rgba(255,255,255,0.6);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 3px;
    }

    .btn-premium-portfolio {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        background: #ffffff;
        color: #1a202c;
        text-decoration: none;
        padding: 0.7rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 800;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .btn-premium-portfolio:hover {
        background: #1a202c;
        color: #fff;
        transform: scale(1.03);
    }
</style>
@endsection
