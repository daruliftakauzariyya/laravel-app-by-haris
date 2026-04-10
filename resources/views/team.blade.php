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
</div>

<style>
    .fw-black { font-weight: 900; }
    
    .scholar-premium-card {
        aspect-ratio: 95 / 155;
        background: #fff;
        border-radius: 3rem;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 15px 40px rgba(0,0,0,0.04);
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
        right: 1.75rem;
        background: #fff;
        color: #10b981;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        z-index: 5;
    }

    .scholar-details {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 3rem 1.75rem 1.75rem;
        background: linear-gradient(to top, 
            rgba(0,0,0,0.95) 0%, 
            rgba(0,0,0,0.75) 40%, 
            rgba(0,0,0,0.4) 70%, 
            rgba(0,0,0,0) 100%);
        z-index: 3;
    }

    .scholar-name-heading {
        font-weight: 900;
        font-size: 1.4rem;
        color: #ffffff;
        margin-bottom: 2px;
        letter-spacing: -0.8px;
        line-height: 1.1;
    }
    .scholar-tagline {
        font-weight: 600;
        color: rgba(255,255,255,0.85);
    }

    .scholar-impact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(5px);
        border-radius: 1.25rem;
        padding: 0.75rem 0;
        margin: 1.25rem 0;
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
        background: #1a202c;
        color: #fff;
        text-decoration: none;
        padding: 0.8rem;
        border-radius: 100px;
        font-size: 0.85rem;
        font-weight: 700;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .btn-premium-portfolio:hover {
        background: #1a7a4a;
        color: #fff;
        transform: scale(1.03);
    }
</style>
@endsection
