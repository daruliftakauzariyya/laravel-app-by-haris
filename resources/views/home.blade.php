@extends('layouts.public')

@section('title', 'Home')

@section('content')

{{-- Hero Slider Section --}}
<section class="hero-slider-wrapper">
    <div id="heroCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner h-100">
            <div class="carousel-item active h-100">
                <div class="slider-image" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ asset('assets/img/slider/center.png') }}');"></div>
                <div class="carousel-caption">
                    <p class="slider-arabic">بسم الله الرحمن الرحيم</p>
                    <h1 class="display-3 fw-bold">Darul Ifta Kauzariyya</h1>
                    <p class="lead d-none d-md-block">Authentic Scholarly Guidance Rooted in Quran and Sunnah.</p>
                </div>
            </div>
            <div class="carousel-item h-100">
                <div class="slider-image" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ asset('assets/img/slider/library.png') }}');"></div>
                <div class="carousel-caption">
                    <p class="slider-arabic">طلب العلم فريضة على كل مسلم</p>
                    <h1 class="display-3 fw-bold">Scholar Research Hub</h1>
                    <p class="lead d-none d-md-block">Illuminating the path of knowledge for the global Ummah.</p>
                </div>
            </div>
            <div class="carousel-item h-100">
                <div class="slider-image" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ asset('assets/img/slider/quran.png') }}');"></div>
                <div class="carousel-caption">
                    <p class="slider-arabic">فاسألوا أهل الذكر إن كنتم لا تعلمون</p>
                    <h1 class="display-3 fw-bold">Ask & Enlighten</h1>
                    <p class="lead d-none d-md-block">Direct access to qualified Muftis for your modern queries.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search Floating Box --}}
    <div class="hero-search-overlay">
        <div class="container">
            <div class="search-glass-card mx-auto">
                <form action="{{ route('fatwa.archive') }}" method="GET" id="hero-search-form">
                    <div class="hero-search-box-v2">
                        <div class="search-main">
                            <i class="bi bi-search text-brand me-2"></i>
                            <input type="text" name="search" class="form-control border-0 shadow-none bg-transparent" 
                                   placeholder="Search fatwas, topics or references..." value="{{ request('search') }}">
                        </div>
                        <div class="search-divider d-none d-md-block"></div>
                        <div class="search-filters">
                            <select name="madhab" class="form-select border-0 shadow-none bg-transparent">
                                <option value="">All Madhabs</option>
                                <option value="Shafii">Shafi'i</option>
                                <option value="Hanafi">Hanafi</option>
                                <option value="Maliki">Maliki</option>
                                <option value="Hanbali">Hanbali</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-brand px-4 py-2 rounded-pill fw-bold">Search</button>
                    </div>
                </form>
                {{-- Quick Access Tags --}}
                <div class="search-tags mt-3 justify-content-center">
                    <span class="tag-label me-2">Quick:</span>
                    <a href="{{ route('fatwa.archive', ['search' => 'salah']) }}" class="search-tag-v2">Salah</a>
                    <a href="{{ route('fatwa.archive', ['search' => 'zakat']) }}" class="search-tag-v2">Zakat</a>
                    <a href="{{ route('fatwa.archive', ['search' => 'marriage']) }}" class="search-tag-v2">Marriage</a>
                </div>

                <div class="d-flex justify-content-center gap-2 mt-4">
                    <a href="{{ route('question.create') }}" class="btn btn-warning fw-bold px-4">
                        <i class="bi bi-pencil-square me-2"></i>Ask a Question
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
 </div>
</section>

{{-- Stats Section --}}
<section class="py-5">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-4 h-100 border-top border-4 border-success">
                    <div style="font-size:2rem; color:#1a7a4a;"><i class="bi bi-question-circle"></i></div>
                    <h3 class="fw-bold text-green mt-2">{{ number_format($stats['total']) }}</h3>
                    <p class="text-muted mb-0 small fw-bold">Total Questions</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-4 h-100 border-top border-4 border-primary">
                    <div style="font-size:2rem; color:#0d6efd;"><i class="bi bi-patch-check"></i></div>
                    <h3 class="fw-bold mt-2" style="color:#0d6efd;">{{ number_format($stats['published']) }}</h3>
                    <p class="text-muted mb-0 small fw-bold">Published Fatwas</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-4 h-100 border-top border-4 border-warning">
                    <div style="font-size:2rem; color:#c9a84c;"><i class="bi bi-person-badge"></i></div>
                    <h3 class="fw-bold mt-2" style="color:#c9a84c;">{{ number_format($stats['muftis']) }}</h3>
                    <p class="text-muted mb-0 small fw-bold">Total Muftis</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-4 h-100 border-top border-4 border-info">
                    <div style="font-size:2rem; color:#0dcaf0;"><i class="bi bi-eye"></i></div>
                    <h3 class="fw-bold mt-2" style="color:#0dcaf0;">{{ number_format($stats['visitors']) }}</h3>
                    <p class="text-muted mb-0 small fw-bold">Portal Visitors</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Team Section --}}
<section class="faculty-section-v2 py-5">
    <div class="split-bg">
        <div class="bg-top"></div>
        <div class="bg-bottom"></div>
    </div>
    
    <div class="container position-relative" style="z-index: 2;">
        <div class="text-center mb-5">
            <span class="faculty-eyebrow">Meet the Scholars</span>
            <h2 class="faculty-heading">Our Faculty <span class="text-brand-gold">&</span> Administration</h2>
            <p class="faculty-subheading mb-4">Guiding the institution with wisdom and authentic knowledge.</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach([
                ['name' => 'Muhammad Ibrahim',  'role' => 'Principal — KAUZARIYYA', 'img' => 'https://new.daruliftakauzariyya.com/assets/img/team/principal.png'],
                ['name' => 'Mufti Rajeeb Qasimi','role' => 'HOD — Darul Ifta',      'img' => 'https://new.daruliftakauzariyya.com/assets/img/team/hod.png'],
                ['name' => 'Mufti Abid Ibrahim', 'role' => 'Professor Kauzariyya',  'img' => 'https://new.daruliftakauzariyya.com/assets/img/team/professor.png'],
                ['name' => 'Ansaf',              'role' => 'Master — Kauzariyya',   'img' => 'https://new.daruliftakauzariyya.com/assets/img/team/master.png'],
            ] as $member)
            <div class="col-6 col-md-3">
                <div class="faculty-card-modern">
                    <div class="faculty-photo-wrapper">
                        <img src="{{ $member['img'] }}" 
                             alt="{{ $member['name'] }}" 
                             class="faculty-photo"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($member['name']) }}&background=E5E7EB&color=295A75&size=300&bold=true'">
                    </div>
                    <div class="faculty-content text-center">
                        <h5 class="m-0 fw-bold">{{ $member['name'] }}</h5>
                        <p class="small opacity-75 mb-0 mt-1">{{ $member['role'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Latest Fatwas --}}
@if($latestFatwas->count())
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1 text-green"><i class="bi bi-journal-bookmark me-2"></i>Recent Public Fatwas</h2>
                <p class="text-muted mb-0 small">Browse the latest Islamic rulings from our scholars</p>
            </div>
            <a href="{{ route('fatwa.archive') }}" class="btn btn-outline-success btn-sm px-4 d-none d-md-inline-flex">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($latestFatwas as $fatwa)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('fatwa.show', $fatwa) }}" class="text-decoration-none">
                    <div class="fatwa-card h-100">
                        {{-- Thumbnail --}}
                        <div class="fatwa-thumb">
                            <img src="{{ asset($fatwa->featured_image ?: 'assets/img/fatwa-placeholder.jpg') }}"
                                 alt="Fatwa"
                                 onerror="this.src='https://placehold.co/400x180/1a7a4a/fff?text=Fatwa'">
                            <span class="madhab-badge">{{ $fatwa->question->madhab }}</span>
                        </div>

                        {{-- Card Body --}}
                        <div class="fatwa-body">
                            <div class="fatwa-meta">
                                <span><i class="bi bi-folder2 me-1"></i>{{ $fatwa->question->category->name ?? 'General' }}</span>
                                <span>{{ $fatwa->published_at?->format('d M Y') }}</span>
                            </div>

                            <h6 class="fatwa-title">
                                {{ Str::limit(strip_tags($fatwa->question->title ?: $fatwa->question->body), 75) }}
                            </h6>

                            <p class="fatwa-excerpt">
                                {{ Str::limit(strip_tags($fatwa->muftiAnswer->summary ?: $fatwa->muftiAnswer->full_answer), 95) }}
                            </p>

                            <div class="fatwa-footer">
                                <span class="fatwa-author">
                                    <i class="bi bi-person-circle me-1"></i>{{ $fatwa->muftiAnswer->mufti->name ?? 'Darul Ifta' }}
                                </span>
                                <span class="read-more-btn">Read More <i class="bi bi-arrow-right"></i></span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5 d-md-none">
            <a href="{{ route('fatwa.archive') }}" class="btn btn-outline-success px-5">View Full Archive</a>
        </div>
    </div>
</section>
@endif

{{-- How It Works --}}
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center fw-bold mb-5 text-green">Process of Getting a Fatwa</h2>
        <div class="row g-4 text-center">
            @foreach([
                ['bi-person-plus', '1', 'Register', 'Create your scholar-verified profile.'],
                ['bi-pencil-square', '2', 'Ask Question', 'Submit your query anonymously if preferred.'],
                ['bi-person-badge', '3', 'Expert Review', 'A qualified scholar reviews and answers.'],
                ['bi-check-circle', '4', 'Certification', 'Final verification by the head of institution.'],
            ] as [$icon, $step, $title, $desc])
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width:60px;height:60px;background:linear-gradient(135deg,#1a7a4a,#c9a84c); color:#fff; font-size:1.5rem;">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <span class="badge bg-secondary mb-2 mx-auto">Phase {{ $step }}</span>
                    <h6 class="fw-bold">{{ $title }}</h6>
                    <p class="small text-muted mb-0">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@push('styles')
<style>
    .transition-all { transition: all 0.3s ease; }
    .hover-shadow:hover { transform: translateY(-5px); box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important; }

    /* ============================================
       Faculty Section — Modernized Profile Cards
       ============================================ */
    .faculty-section-v2 {
        position: relative;
        padding: 100px 0;
        overflow: hidden;
    }
    .split-bg {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        z-index: 1;
        display: flex;
        flex-direction: column;
    }
    .bg-top { flex: 1; background: #ffffff; }
    .bg-bottom { flex: 1; background: #222222; }

    .faculty-eyebrow {
        display: inline-block;
        background: rgba(41, 90, 117, 0.08);
        color: var(--brand-blue);
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        padding: 5px 15px;
        border-radius: 50px;
        margin-bottom: 15px;
    }
    .faculty-heading {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1a202c;
    }
    .text-brand-gold { color: var(--brand-gold); }
    .faculty-subheading { color: #718096; max-width: 600px; margin: 0 auto; }

    .faculty-card-modern {
        background: var(--brand-blue);
        border-radius: 20px;
        padding: 25px;
        color: #fff;
        height: 100%;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .faculty-card-modern:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.3);
    }
    .faculty-photo-wrapper {
        width: 100%;
        aspect-ratio: 1/1;
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 25px;
        padding: 15px;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
    }
    .faculty-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 12px;
    }
    .faculty-content h5 {
        font-size: 1.15rem;
        letter-spacing: 0.5px;
    }
    .faculty-content p {
        font-size: 0.85rem;
        font-weight: 500;
    }

    @media(max-width: 768px) {
        .faculty-heading { font-size: 1.8rem; }
        .faculty-card-modern { padding: 15px; }
        .faculty-photo-wrapper { margin-bottom: 15px; padding: 10px; }
        .faculty-section-v2 { padding: 60px 0; }
    }
    .x-small { font-size: 0.75rem; }

    /* ============================================
       Hero Slider & Search Overlay
       ============================================ */
    .hero-slider-wrapper {
        position: relative;
        width: 100%;
        height: 500px;
        overflow: hidden;
    }
    .slider-image {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        transition: transform 6s ease-in-out;
    }
    .carousel-item.active .slider-image {
        transform: scale(1.1);
    }
    .carousel-caption {
        bottom: 40%;
        z-index: 5;
        text-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }
    .slider-arabic {
        color: var(--brand-gold);
        font-family: 'Amiri', serif;
        font-size: 1.35rem;
        margin-bottom: 1rem;
        letter-spacing: 2px;
    }

    .hero-search-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding-bottom: 40px;
        z-index: 10;
    }
    .search-glass-card {
        max-width: 850px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    }
    .hero-search-box-v2 {
        display: flex;
        align-items: center;
        background: #fff;
        border-radius: 50px;
        padding: 8px 8px 8px 25px;
        gap: 0;
    }
    .search-main { flex: 1; display: flex; align-items: center; }
    .search-divider { width: 1px; height: 30px; background: #eee; margin: 0 15px; }
    .search-filters { width: 180px; }
    
    .search-tag-v2 {
        font-size: 0.75rem;
        color: #fff;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        padding: 4px 15px;
        border-radius: 20px;
        text-decoration: none;
        transition: 0.2s;
    }
    .search-tag-v2:hover { background: var(--brand-gold); border-color: var(--brand-gold); color: #fff; }

    @media (max-width: 768px) {
        .hero-slider-wrapper { height: 75vh; min-height: 480px; }
        .carousel-caption h1 { font-size: 2rem !important; }
        .hero-search-box-v2 { flex-direction: column; border-radius: 15px; padding: 15px; gap: 10px; }
        .search-filters { width: 100%; }
        .hero-search-box-v2 .btn { width: 100%; border-radius: 10px; }
        .search-glass-card { padding: 20px; margin: 0 10px; }
        .hero-search-overlay { padding-bottom: 30px; }
    }

    /* ============================================
       Fatwa Card — Clean Box Model Design
       ============================================ */
    .fatwa-card {
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #edf2f7;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        display: flex;
        flex-direction: column;
    }
    .fatwa-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(26, 122, 74, 0.12);
        border-color: #c6e9d8;
    }

    /* Thumbnail area */
    .fatwa-thumb {
        position: relative;
        width: 100%;
        height: 180px;
        overflow: hidden;
        background: #f0f4f8;
    }
    .fatwa-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .fatwa-card:hover .fatwa-thumb img { transform: scale(1.04); }

    /* Madhab floating badge */
    .madhab-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #1a7a4a;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        letter-spacing: 0.5px;
    }

    /* Card Body */
    .fatwa-body {
        padding: 18px 20px 16px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    /* Meta row: category + date */
    .fatwa-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        color: #a0aec0;
        margin-bottom: 10px;
        font-weight: 500;
    }
    .fatwa-meta span:first-child { color: #1a7a4a; font-weight: 600; }

    /* Title */
    .fatwa-title {
        font-size: 0.98rem;
        font-weight: 800;
        color: #1a202c;
        line-height: 1.45;
        margin-bottom: 10px;
        flex-grow: 1;
    }

    /* Excerpt */
    .fatwa-excerpt {
        font-size: 0.83rem;
        color: #718096;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    /* Footer row */
    .fatwa-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f1f5f9;
        padding-top: 12px;
        margin-top: auto;
    }
    .fatwa-author { font-size: 0.8rem; color: #4a5568; font-weight: 600; }
    .read-more-btn {
        font-size: 0.8rem;
        font-weight: 700;
        color: #1a7a4a;
        border: 1.5px solid #1a7a4a;
        padding: 4px 14px;
        border-radius: 20px;
        transition: 0.2s;
    }
    .fatwa-card:hover .read-more-btn { background: #1a7a4a; color: #fff; }
</style>
@endpush

@endsection
