@extends('layouts.public')
@section('title', 'Mufti Portfolio — ' . $user->name)
@section('content')
<div class="container py-5">
    {{-- Hero Section: Model Profile Card + Academic Impact --}}
    <div class="mb-5">
        <div class="row g-4 align-items-center">
            {{-- Left Column: Model Profile Card --}}
            <div class="col-lg-4 col-md-5">
                <div class="model-profile-card-wrapper">
                    <div class="model-profile-card">
                        {{-- Background Image / Photo --}}
                        @if($user->avatar)
                            <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="model-card-bg">
                        @else
                            <div class="model-card-bg-fallback">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        @endif

                        {{-- Gradient Overlay --}}
                        <div class="model-card-overlay"></div>

                        {{-- Card Content --}}
                        <div class="model-card-content">
                            {{-- Name with Verified Blue Tick --}}
                            <div class="model-name mb-3">
                                {{ $user->name }}
                                <span class="verified-tick" title="Verified Scholar">
                                    <i class="bi bi-patch-check-fill"></i>
                                </span>
                            </div>

                            {{-- Bottom Actions Row --}}
                            <div class="model-actions-row">
                                <a href="{{ route('contact') }}" class="btn-white-pill flex-grow-1">
                                    <i class="bi bi-envelope-fill me-2"></i> Get In Touch
                                </a>
                                <button type="button" class="btn-dark-circle" onclick="navigator.clipboard?.writeText(window.location.href); alert('Scholar profile link copied to clipboard!');" title="Share / Bookmark">
                                    <i class="bi bi-bookmark-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Single Unified Academic Impact & Scholar Overview Card --}}
            <div class="col-lg-8 col-md-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
                    {{-- Header --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h4 class="fw-bold mb-1 text-dark">Academic Impact & Contributions</h4>
                            <p class="text-muted small mb-0">Verified Islamic scholarly contributions and public readership analytics.</p>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill fw-bold">
                            <i class="bi bi-patch-check-fill me-1"></i> Verified Mufti
                        </span>
                    </div>

                    {{-- Specialization Bio --}}
                    <p class="text-muted small mb-4" style="line-height: 1.65; font-size: 0.95rem;">
                        Islamic Scholar specializing in <strong class="text-dark">{{ $user->madhab?->name ? $user->madhab->name . ' Jurisprudence' : 'Islamic Jurisprudence' }}</strong> & authentic fatwa research.
                    </p>

                    {{-- 4 Primary Impact Stat Boxes --}}
                    <div class="row g-3 text-center mb-4">
                        <div class="col-6 col-lg-3">
                            <div class="bg-light rounded-4 p-3 border h-100 transition-up">
                                <div class="h2 fw-bold text-primary mb-1">{{ number_format($totalAnswers) }}</div>
                                <div class="text-muted x-small fw-bold text-uppercase tracking-wider">Questions Answered</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="bg-light rounded-4 p-3 border h-100 transition-up">
                                <div class="h2 fw-bold text-success mb-1">{{ number_format($totalTopics) }}</div>
                                <div class="text-muted x-small fw-bold text-uppercase tracking-wider">Topics Covered</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="bg-light rounded-4 p-3 border h-100 transition-up">
                                <div class="h2 fw-bold text-danger mb-1">{{ number_format($totalLikes) }} <span style="font-size: 1rem; color: #fbbf24;">★</span></div>
                                <div class="text-muted x-small fw-bold text-uppercase tracking-wider">Appreciation/Likes</div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="bg-light rounded-4 p-3 border h-100 transition-up">
                                <div class="h2 fw-bold text-info mb-1">{{ number_format($totalViews) }}</div>
                                <div class="text-muted x-small fw-bold text-uppercase tracking-wider">Public Reads</div>
                            </div>
                        </div>
                    </div>

                    {{-- Verified Scholar Credibility Banner --}}
                    <div class="p-3 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25 d-flex align-items-center gap-3 mb-4">
                        <div class="bg-success text-white p-2 rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="small text-muted mb-0">
                            <strong class="text-success">Verified Contributor:</strong> This scholar is an accredited contributor to Darul Ifta's archive of Islamic jurisprudence.
                        </div>
                    </div>

                    {{-- Footer & Quick Action --}}
                    <div class="pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="small text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $user->country ?? 'Global' }}</span>
                            <span class="text-muted">·</span>
                            <span class="small text-muted"><i class="bi bi-book-half text-success me-1"></i>{{ $user->madhab?->name ?? 'Hanafi' }} School of Thought</span>
                        </div>
                        <a href="#published-fatwas" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                            Browse All Fatwas <i class="bi bi-arrow-down ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4" id="published-fatwas">
        <div class="col-lg-8">
            <h5 class="fw-bold mb-4 d-flex align-items-center">
                <i class="bi bi-journal-check text-primary me-2 h4 mb-0"></i>
                Published Fatwas ({{ $fatwas->total() }})
            </h5>

            @if($fatwas->isEmpty())
                <div class="card border-0 shadow-sm p-5 text-center">
                    <i class="bi bi-inbox h1 text-muted opacity-25"></i>
                    <p class="mb-0">No published fatwas found for this scholar.</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($fatwas as $fatwa)
                        @include('partials.fatwa-card', ['fatwa' => $fatwa, 'colClass' => 'col-12 col-md-6'])
                    @endforeach
                </div>
                <div class="mt-5">{{ $fatwas->links() }}</div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="sidebar-widget">
                <span class="widget-title">Knowledge Streams</span>
                <ul class="cat-list" style="padding-left: 0; list-style: none;">
                    @foreach($allCategories as $cat)
                    @php
                        $isCurrentOrChildSelected = (request('category') == $cat->slug || request('category') == $cat->id || $cat->children->pluck('slug')->contains(request('category')) || $cat->children->pluck('id')->contains(request('category')));
                        $catSlug = $cat->slug ?: $cat->id;
                    @endphp
                    <li class="mb-3">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <a href="{{ route('fatwa.archive', ['category' => $catSlug]) }}" 
                               class="flex-grow-1 d-flex justify-content-between align-items-center text-decoration-none {{ (request('category') == $cat->slug || request('category') == $cat->id) ? 'active text-success fw-bold' : 'text-dark' }}" style="font-weight: 600; font-size: 0.95rem;">
                                <span>
                                    @if($cat->children->isNotEmpty())
                                        <i class="bi bi-chevron-{{ $isCurrentOrChildSelected ? 'down' : 'right' }} me-1 small text-muted"></i>
                                    @else
                                        <i class="bi bi-circle me-1 small text-muted" style="font-size: 0.5rem; vertical-align: middle;"></i>
                                    @endif
                                    {{ $cat->name }}
                                </span>
                                <span class="cat-count px-2 py-0.5 rounded-pill bg-light text-muted small border" style="font-size: 0.75rem;">
                                    {{ $cat->questions_count }}
                                </span>
                            </a>
                        </div>
                        
                        @if($cat->children->isNotEmpty())
                            <ul class="sub-cat-list ps-3 mt-2 {{ $isCurrentOrChildSelected ? 'd-block' : 'd-none' }}" style="list-style: none; padding-left: 1rem;">
                                @foreach($cat->children as $child)
                                @php
                                    $childSlug = $child->slug ?: $child->id;
                                @endphp
                                <li class="py-1">
                                    <a href="{{ route('fatwa.archive', ['category' => $childSlug]) }}" 
                                       class="d-flex justify-content-between align-items-center text-decoration-none {{ (request('category') == $child->slug || request('category') == $child->id) ? 'active text-success fw-bold' : 'text-muted' }}" style="font-size: 0.85rem; font-weight: 500;">
                                        <span>
                                            <i class="bi bi-arrow-return-right me-1 text-muted" style="font-size: 0.75rem;"></i>
                                            {{ $child->name }}
                                        </span>
                                        <span class="badge bg-light text-muted rounded-pill border px-2" style="font-size: 0.7rem; font-weight: normal;">
                                            {{ $child->questions_count }}
                                        </span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* ── Model Profile Card ── */
    .model-profile-card-wrapper {
        max-width: 380px;
        margin: 0 auto;
    }

    .model-profile-card {
        position: relative;
        background: #0f172a;
        border: 8px solid #ffffff;
        border-radius: 36px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        min-height: 480px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        color: #ffffff;
    }

    .model-card-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 1;
    }

    .model-card-bg-fallback {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at 50% 30%, #1e293b 0%, #0f172a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }

    .model-card-bg-fallback i {
        font-size: 8rem;
        color: rgba(255, 255, 255, 0.08);
    }

    .model-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.1) 0%, rgba(15, 23, 42, 0.6) 45%, rgba(15, 23, 42, 0.95) 85%, #0f172a 100%);
        z-index: 2;
    }

    .model-card-content {
        position: relative;
        z-index: 3;
        padding: 24px 22px 22px;
    }

    .model-name {
        font-size: 1.55rem;
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        letter-spacing: -0.3px;
    }

    .model-name .verified-tick {
        color: #38bdf8;
        font-size: 1.25rem;
        margin-left: 8px;
        display: inline-flex;
        align-items: center;
    }

    .model-bio {
        font-size: 0.88rem;
        line-height: 1.45;
        color: rgba(255, 255, 255, 0.82);
        margin-bottom: 20px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .model-stats-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        margin-bottom: 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    .model-stat-col {
        flex: 1;
        text-align: center;
    }

    .model-stat-col .stat-val {
        font-size: 1.15rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.2;
    }

    .model-stat-col .stat-lbl {
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.6);
        text-transform: capitalize;
        margin-top: 2px;
    }

    .model-stat-divider {
        width: 1px;
        height: 28px;
        background: rgba(255, 255, 255, 0.18);
    }

    .model-actions-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-white-pill {
        background: #ffffff;
        color: #0f172a !important;
        font-weight: 700;
        font-size: 0.92rem;
        border-radius: 50px;
        padding: 12px 20px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-white-pill:hover {
        background: #f1f5f9;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    .btn-dark-circle {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .btn-dark-circle:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: scale(1.05);
    }

    .transition-up { transition: transform 0.2s ease; }
    .transition-up:hover { transform: translateY(-3px); }
    .x-small { font-size: 0.75rem; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .hover-green:hover { color: #1a7a4a !important; }

    /* Sidebar Widgets */
    .sidebar-widget {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border: 1px solid #edf2f7;
    }
    .widget-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #4A6372;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #1a7a4a;
        display: inline-block;
        width: 100%;
    }
    .cat-list { list-style: none; padding: 0; margin: 0; }
    .cat-list li { margin-bottom: 12px; }
    .cat-list a {
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-decoration: none;
        color: #4a5568;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .cat-list a:hover { color: #1a7a4a; }
    .cat-count { background: #edf2f7; padding: 2px 10px; border-radius: 20px; font-size: 0.75rem; color: #718096; }
</style>
@endpush
@endsection
