@extends('layouts.public')

@section('title', 'Home')

@section('content')

{{-- Hero Section --}}
<section style="background: linear-gradient(135deg, #1a7a4a 0%, #145e38 60%, #0d3d24 100%); color:#fff; padding: 70px 0 80px;">
    <div class="container text-center">
        <p class="mb-2" style="color:#c9a84c; letter-spacing:3px; font-size:.85rem; font-weight:600;">بسم الله الرحمن الرحيم</p>
        <h1 class="display-4 fw-bold mb-3">Darul Ifta Kauzariyya</h1>
        <p class="lead mb-5" style="color:rgba(255,255,255,.85); max-width:600px; margin: 0 auto 30px;">
            Ask your Islamic questions and receive authentic Fatwa answers from qualified Muftis based on Quran and Sunnah.
        </p>

        {{-- Search Bar --}}
        <div class="hero-search-wrapper mx-auto">
            <form action="{{ route('fatwa.archive') }}" method="GET" id="hero-search-form">
                <div class="hero-search-box">
                    <div class="search-icon"><i class="bi bi-search"></i></div>
                    <input type="text" name="search" id="hero-search-input"
                           class="search-input"
                           placeholder="Search fatwas — Questions, Answers, Categories, References..."
                           value="{{ request('search') }}"
                           autocomplete="off">
                    <div class="search-divider"></div>
                    <select name="madhab" class="search-select">
                        <option value="">All Madhabs</option>
                        <option value="Shafii"  {{ request('madhab') == 'Shafii'  ? 'selected' : '' }}>Shafi'i</option>
                        <option value="Hanafi"  {{ request('madhab') == 'Hanafi'  ? 'selected' : '' }}>Hanafi</option>
                        <option value="Maliki"  {{ request('madhab') == 'Maliki'  ? 'selected' : '' }}>Maliki</option>
                        <option value="Hanbali" {{ request('madhab') == 'Hanbali' ? 'selected' : '' }}>Hanbali</option>
                    </select>
                    <button type="submit" class="search-btn">Search</button>
                </div>
                {{-- Search Tags / Shortcut filters --}}
                <div class="search-tags">
                    <span class="tag-label">Quick:</span>
                    <a href="{{ route('fatwa.archive', ['madhab' => 'Shafii']) }}" class="search-tag">Shafi'i</a>
                    <a href="{{ route('fatwa.archive', ['madhab' => 'Hanafi']) }}" class="search-tag">Hanafi</a>
                    <a href="{{ route('fatwa.archive', ['search' => 'salah']) }}" class="search-tag">Salah</a>
                    <a href="{{ route('fatwa.archive', ['search' => 'zakat']) }}" class="search-tag">Zakat</a>
                    <a href="{{ route('fatwa.archive', ['search' => 'marriage']) }}" class="search-tag">Marriage</a>
                    <a href="{{ route('fatwa.archive') }}" class="search-tag">View All</a>
                </div>
            </form>
        </div>

        <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
            <a href="{{ route('question.create') }}" class="btn btn-warning px-4 fw-bold">
                <i class="bi bi-pencil-square me-2"></i>Ask a Question
            </a>
            <a href="{{ route('fatwa.archive') }}" class="btn btn-outline-light px-4">
                <i class="bi bi-archive me-2"></i>Browse Archive
            </a>
        </div>
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
<section class="py-5 bg-white">
    <div class="container text-center">
        <h2 class="fw-bold text-green mb-2">Our Faculty & Administration</h2>
        <p class="text-muted mb-5">Guiding the institution with wisdom and authentic knowledge.</p>
        
        <div class="row g-4 justify-content-center">
            @foreach([
                ['name' => 'Muhammad Ibrahim', 'role' => 'Principal - KAUZARIYYA', 'position' => 'Administration'],
                ['name' => 'Mufti Rajeeb Qasimi', 'role' => 'HOD', 'position' => 'HOD'],
                ['name' => 'Mufti Abid Ibrahim', 'role' => 'Professor Kauzariyya', 'position' => 'Admin Online Fatwa'],
                ['name' => 'Ansaf', 'role' => 'Master - Kauzariyya', 'position' => 'Coordinator'],
            ] as $member)
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100 hover-shadow transition-all">
                    <div class="p-4 pb-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($member['name']) }}&background=1a7a4a&color=fff&size=200" 
                             class="card-img-top rounded-circle shadow-sm mx-auto d-block border border-4 border-light" 
                             style="width: 140px; height: 140px; object-fit: cover;" 
                             alt="{{ $member['name'] }}">
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">{{ $member['name'] }}</h6>
                        <p class="text-green small fw-bold mb-1">{{ $member['position'] }}</p>
                        <p class="text-muted x-small mb-0">{{ $member['role'] }}</p>
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
    .x-small { font-size: 0.75rem; }

    /* ============================================
       Hero Search Bar
       ============================================ */
    .hero-search-wrapper {
        max-width: 700px;
        width: 100%;
    }
    .hero-search-box {
        display: flex;
        align-items: center;
        background: rgba(255,255,255,0.97);
        border-radius: 50px;
        padding: 6px 6px 6px 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        gap: 0;
    }
    .search-icon { color: #1a7a4a; font-size: 1.1rem; flex-shrink: 0; margin-right: 10px; }
    .search-input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 0.93rem;
        color: #2d3748;
        background: transparent;
        min-width: 0;
    }
    .search-input::placeholder { color: #a0aec0; }
    .search-divider { width: 1px; height: 28px; background: #e2e8f0; margin: 0 10px; flex-shrink: 0; }
    .search-select {
        border: none;
        outline: none;
        font-size: 0.85rem;
        color: #4a5568;
        background: transparent;
        cursor: pointer;
        padding: 4px 8px;
        flex-shrink: 0;
        font-weight: 600;
    }
    .search-btn {
        background: #1a7a4a;
        color: #fff;
        border: none;
        border-radius: 40px;
        padding: 10px 24px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background 0.2s;
        flex-shrink: 0;
    }
    .search-btn:hover { background: #145e38; }

    /* Quick filter tags */
    .search-tags {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 14px;
        justify-content: center;
    }
    .tag-label { font-size: 0.78rem; color: rgba(255,255,255,.6); font-weight: 600; }
    .search-tag {
        font-size: 0.78rem;
        color: rgba(255,255,255,.85);
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 20px;
        padding: 3px 12px;
        text-decoration: none;
        transition: 0.2s;
        font-weight: 500;
    }
    .search-tag:hover { background: rgba(255,255,255,0.25); color: #fff; }

    @media (max-width: 576px) {
        .hero-search-box { flex-wrap: wrap; border-radius: 16px; padding: 12px; gap: 8px; }
        .search-divider { display: none; }
        .search-btn { width: 100%; border-radius: 10px; padding: 10px; }
        .search-select { width: 100%; }
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
