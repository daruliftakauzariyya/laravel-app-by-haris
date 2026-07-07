@extends('layouts.public')

@section('title', 'Home — Darul Ifta Kauzariyya')

@section('content')

{{-- ══════════════════════════════════════════════
     Hero Section: Premium Static Arches Layout
     ══════════════════════════════════════════════ --}}
<section class="premium-static-hero" style="background-image: linear-gradient(to bottom, rgba(13,38,62,0.85) 0%, rgba(13,38,62,0.5) 50%, rgba(13,38,62,0.9) 100%), url('{{ asset('assets/img/slider/center.png') }}');">
    <!-- Faint Islamic Background Pattern overlay -->
    <div class="hero-bg-overlay-pattern"></div>

    <div class="container hero-content-container">
        <div class="text-center mb-4 hero-text-block">
            <h1 class="hero-main-title display-4">
                Darul Ifta <span class="text-brand-gold">Kauzariyya</span>
            </h1>
            <p class="hero-subtitle">Trusted Islamic guidance. Authentic answers.</p>
        </div>

        <!-- Search & Quick Actions Card -->
        <div class="hero-premium-search-card mx-auto shadow-lg">
            <form action="{{ route('fatwa.archive') }}" method="GET" id="hero-search-form">
                <div class="hero-search-row">
                    <div class="search-input-col">
                        <i class="bi bi-search text-muted me-2"></i>
                        <input type="text" name="search" class="form-control border-0 shadow-none bg-transparent" 
                               placeholder="Search fatwas, topics or references..." value="{{ request('search') }}">
                    </div>
                    <div class="search-divider"></div>
                    <div class="search-dropdown-col">
                        <select name="madhab" class="form-select border-0 shadow-none bg-transparent font-eng">
                            <option value="">All Madhabs</option>
                            <option value="Shafii" {{ request('madhab') == 'Shafii' ? 'selected' : '' }}>Shafi'i</option>
                            <option value="Hanafi" {{ request('madhab') == 'Hanafi' ? 'selected' : '' }}>Hanafi</option>
                            <option value="Maliki" {{ request('madhab') == 'Maliki' ? 'selected' : '' }}>Maliki</option>
                            <option value="Hanbali" {{ request('madhab') == 'Hanbali' ? 'selected' : '' }}>Hanbali</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-search-submit">Search</button>
                </div>
            </form>

            <!-- Popular Search Pills -->
            <div class="hero-popular-searches mt-3">
                <span class="popular-label">Popular Searches:</span>
                <div class="popular-pills">
                    <a href="{{ route('fatwa.archive', ['search' => 'salah']) }}" class="search-pill-btn">Salah</a>
                    <a href="{{ route('fatwa.archive', ['search' => 'zakat']) }}" class="search-pill-btn">Zakat</a>
                    <a href="{{ route('fatwa.archive', ['search' => 'marriage']) }}" class="search-pill-btn">Marriage</a>
                    <a href="{{ route('fatwa.archive', ['search' => 'fasting']) }}" class="search-pill-btn">Fasting</a>
                    <a href="{{ route('fatwa.archive', ['search' => 'inheritance']) }}" class="search-pill-btn">Inheritance</a>
                    <a href="{{ route('fatwa.archive', ['search' => 'qurbani']) }}" class="search-pill-btn">Qurbani</a>
                </div>
            </div>

            <!-- Quick Action Bottom Bar -->
            <div class="hero-quick-actions-bar mt-4">
                <div class="row g-0">
                    <div class="col-md-6 border-end-md">
                        <a href="{{ route('fatwa.archive') }}" class="quick-action-link d-flex align-items-center">
                            <div class="action-icon-box bg-green-tint">
                                <i class="bi bi-book-half"></i>
                            </div>
                            <div class="action-text-box">
                                <h6 class="action-title mb-0">Explore Knowledge</h6>
                                <span class="action-subtitle">Browse fatwas by category and topic</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('question.create') }}" class="quick-action-link d-flex align-items-center">
                            <div class="action-icon-box bg-gold-tint">
                                <i class="bi bi-question-circle"></i>
                            </div>
                            <div class="action-text-box">
                                <h6 class="action-title mb-0">Ask a Question</h6>
                                <span class="action-subtitle">Get a scholarly answer to your question</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Asymmetrical Curved Wave Bottom -->
    <div class="hero-wave-divider">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,60 C380,120 960,0 1440,60 L1440,120 L0,120 Z" fill="#ffffff"></path>
        </svg>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     Section: Recent Public Fatwas
     ══════════════════════════════════════════════ --}}
@if($latestFatwas->count())
<section class="py-5 premium-recent-section" style="background: #ffffff;">
    <div class="container position-relative">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="section-gold-title mb-1">
                    <span class="title-icon-box"><i class="bi bi-journal-bookmark-fill"></i></span>
                    Recent Public Fatwas
                </h2>
                <p class="section-gold-subtitle">Browse the latest Islamic rulings from our scholars</p>
            </div>
            <a href="{{ route('fatwa.archive') }}" class="btn btn-explore-more d-none d-md-inline-flex">
                View All Fatwas <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($latestFatwas as $fatwa)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <a href="{{ route('fatwa.show', $fatwa) }}" class="text-decoration-none">
                    <div class="recent-fatwa-card-v2 h-100 d-flex flex-column justify-content-between">
                        <!-- Top Text & Image Row -->
                        <div class="card-top-half p-4">
                            <div class="card-text-side pe-2">
                                <span class="badge-custom-category">
                                    {{ $fatwa->category?->name ?? 'General / Other' }}
                                </span>
                                <h5 class="recent-fatwa-title-v2 mt-3 ml-text">
                                    {{ Str::limit(strip_tags($fatwa->title ?: $fatwa->body), 85) }}
                                </h5>
                            </div>
                            <div class="card-image-side" style="background-image: url('{{ asset($fatwa->featured_image ?: 'assets/img/fatwa-placeholder.jpg') }}');">
                                <span class="card-madhab-badge">{{ $fatwa->madhab?->name ?? 'General' }}</span>
                            </div>
                        </div>

                        <!-- Full Width Bottom Footer Bar -->
                        <div class="card-bottom-bar d-flex align-items-center justify-content-between px-4 py-3">
                            <span class="meta-item"><i class="bi bi-calendar3 me-1"></i>{{ $fatwa->published_at?->format('M d, Y') ?? 'May 20, 2024' }}</span>
                            <span class="meta-item"><i class="bi bi-eye me-1"></i>{{ number_format($fatwa->views) }} Views</span>
                            <i class="bi bi-arrow-right text-gold"></i>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5 d-md-none">
            <a href="{{ route('fatwa.archive') }}" class="btn btn-explore-more w-100">View All Fatwas</a>
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════
     Section: Values Banner (Gold outline icons)
     ══════════════════════════════════════════════ --}}
<section class="py-5 values-banner-section" style="background: #0a2540; border-top: 1px solid #081d33; border-bottom: 1px solid #081d33;">
    <div class="container">
        <div class="row g-4 text-center text-md-start">
            <div class="col-md-3">
                <div class="value-item-box d-flex flex-column flex-md-row align-items-center align-items-md-start gap-3">
                    <div class="value-icon"><i class="bi bi-shield-check"></i></div>
                    <div class="value-text">
                        <h6 class="value-title">Authentic & Trusted</h6>
                        <p class="value-desc">All fatwas are verified by qualified and experienced scholars</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="value-item-box d-flex flex-column flex-md-row align-items-center align-items-md-start gap-3">
                    <div class="value-icon"><i class="bi bi-book"></i></div>
                    <div class="value-text">
                        <h6 class="value-title">Based on Authentic Sources</h6>
                        <p class="value-desc">Rulings are derived from the Quran, Sunnah and classical texts</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="value-item-box d-flex flex-column flex-md-row align-items-center align-items-md-start gap-3">
                    <div class="value-icon"><i class="bi bi-people"></i></div>
                    <div class="value-text">
                        <h6 class="value-title">Multiple Madhabs</h6>
                        <p class="value-desc">View opinions from different madhabs for clarity</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="value-item-box d-flex flex-column flex-md-row align-items-center align-items-md-start gap-3">
                    <div class="value-icon"><i class="bi bi-award"></i></div>
                    <div class="value-text">
                        <h6 class="value-title">100% Free Service</h6>
                        <p class="value-desc">Our service is completely free for everyone</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     Section: Most Useful & Curated Fatwas
     ══════════════════════════════════════════════ --}}
@if($featuredData['fatwas']->isNotEmpty())
<section class="py-5 premium-featured-section" style="background: linear-gradient(180deg, #ffffff 0%, #f7f9fa 100%);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="section-gold-title mb-1">
                    <span class="title-icon-box gold"><i class="bi bi-stars"></i></span>
                    {{ $featuredData['label'] }}
                </h2>
                <p class="section-gold-subtitle">{{ $featuredData['sub'] }}</p>
            </div>
            <a href="{{ route('fatwa.archive') }}" class="btn btn-explore-more secondary d-none d-md-inline-flex">
                View Curated Archive <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($featuredData['fatwas'] as $fatwa)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('fatwa.show', $fatwa) }}" class="text-decoration-none">
                    <div class="fatwa-card-featured-v2 h-100">
                        <div class="featured-thumb-v2">
                            <img src="{{ asset($fatwa->featured_image ?: 'assets/img/fatwa-placeholder.jpg') }}" alt="Fatwa" loading="lazy"
                                 onerror="this.src='https://placehold.co/400x180/295a75/fff?text=Fatwa'">
                            <span class="featured-madhab-badge">{{ $fatwa->madhab?->name ?? 'General' }}</span>
                            @if($fatwa->views > 50)
                            <span class="featured-hot-badge"><i class="bi bi-fire me-1"></i>Popular</span>
                            @endif
                        </div>
                        <div class="featured-body-v2">
                            <div class="featured-meta-v2">
                                <span class="category"><i class="bi bi-folder2 me-1"></i>{{ $fatwa->category?->name ?? 'General' }}</span>
                                <span class="views"><i class="bi bi-eye me-1"></i>{{ number_format($fatwa->views) }}</span>
                            </div>
                            <h5 class="featured-title-v2 ml-text">{{ Str::limit(strip_tags($fatwa->title ?: $fatwa->body), 75) }}</h5>
                            <p class="featured-excerpt-v2 text-muted">{{ Str::limit(strip_tags($fatwa->answer ?? ''), 95) }}</p>
                            
                            <div class="featured-footer-v2">
                                <div class="author-info">
                                    <i class="bi bi-person-circle"></i>
                                    <span>{{ $fatwa->assignedMufti?->name ?? 'Darul Ifta' }}</span>
                                </div>
                                <span class="read-more">Read Fatwa <i class="bi bi-chevron-right"></i></span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════
     Section: Statistics counter cards
     ══════════════════════════════════════════════ --}}
<section class="py-5 premium-stats-section" style="background: #ffffff;">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-6 col-md-3">
                <div class="stats-card-v2 border-green">
                    <div class="stats-icon text-green"><i class="bi bi-question-circle"></i></div>
                    <h3 class="stats-number text-green">{{ number_format($stats['total']) }}</h3>
                    <p class="stats-label">Total Questions</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card-v2 border-blue">
                    <div class="stats-icon text-blue"><i class="bi bi-patch-check"></i></div>
                    <h3 class="stats-number text-blue">{{ number_format($stats['published']) }}</h3>
                    <p class="stats-label">Published Fatwas</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card-v2 border-gold">
                    <div class="stats-icon text-gold"><i class="bi bi-person-badge"></i></div>
                    <h3 class="stats-number text-gold">{{ number_format($stats['muftis']) }}</h3>
                    <p class="stats-label">Total Muftis</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-card-v2 border-teal">
                    <div class="stats-icon text-teal"><i class="bi bi-eye"></i></div>
                    <h3 class="stats-number text-teal">{{ number_format($stats['visitors']) }}</h3>
                    <p class="stats-label">Portal Visitors</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     Section: Meet the Scholars
     ══════════════════════════════════════════════ --}}
@php
$scholars = [
    [
        'name' => 'Moulana Mohammed Ibrahim',
        'role' => 'Muthavalli',
        'dept' => 'KAUZARIYYA',
        'img' => asset('team-photos/ibrahim.jpeg'),
        'icon' => 'bi-book-open',
        'has_image' => true
    ],
    [
        'name' => 'Mufti Rajeeb Al Qasimi',
        'role' => 'HOD - Hanafi',
        'dept' => 'Darul Ifta Kauzariyya',
        'img' => asset('team-photos/rajeeb.png'),
        'icon' => 'bi-pen',
        'has_image' => true
    ],
    [
        'name' => 'Al Usthad Abdul Samad Al Kauzari',
        'role' => 'HOD — Shafi',
        'dept' => 'Darul Ifta Kauzariyya',
        'img' => asset('team-photos/Abdul-Samad.png'),
        'icon' => 'bi-bank',
        'has_image' => true,
        'initials' => 'AS'
    ],
    [
        'name' => 'Mufti Abid Ibrahim Al Kauzari',
        'role' => 'Admin Darul Ifta',
        'dept' => 'Kauzariyya',
        'img' => asset('team-photos/Abid.png'),
        'icon' => 'bi-book',
        'has_image' => true
    ],
    [
        'name' => 'Al Usthad Ilyas Al Kauzari',
        'role' => 'Sheikhul Hadhees',
        'dept' => 'Kauzariyya',
        'img' => asset('team-photos/Usthad-Ilyas.png'),
        'icon' => 'bi-mortarboard',
        'has_image' => true
    ]
];
@endphp

<section class="premium-scholars-section py-5">
    <div class="scholars-watermark watermark-left"></div>
    <div class="scholars-watermark watermark-right"></div>
    <div class="scholars-dots dots-left"></div>
    <div class="scholars-dots dots-right"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="text-center mb-5">
            <div class="scholars-eyebrow-container">
                <span class="scholars-flower">❖</span>
                <span class="scholars-eyebrow">MEET THE SCHOLARS</span>
                <span class="scholars-flower">❖</span>
            </div>
            <h2 class="scholars-heading mt-3">Our Faculty <span class="text-brand-gold">&</span> Administration</h2>
            <p class="scholars-subheading mt-2">Guiding the institution with wisdom and authentic knowledge.</p>
        </div>

        <div class="scholars-grid-row">
            @foreach($scholars as $scholar)
            <div class="scholar-col-5">
                <div class="scholar-premium-card-v2">
                    <div class="scholar-ribbon-tag">
                        <i class="bi bi-person-fill"></i>
                    </div>

                    <div class="scholar-photo-area">
                        @if($scholar['has_image'])
                            <img src="{{ $scholar['img'] }}" alt="{{ $scholar['name'] }}" class="scholar-img-v2"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="scholar-initials-fallback" style="display: none;">
                                <span>{{ collect(explode(' ', $scholar['name']))->map(fn($n) => $n[0] ?? '')->join('') }}</span>
                            </div>
                        @else
                            <div class="scholar-initials-circle">
                                <span>{{ $scholar['initials'] }}</span>
                            </div>
                        @endif

                        <div class="scholar-wave-separator">
                            <svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                                <path fill="#0a2540" fill-opacity="1" d="M0,224 C480,320 960,320 1440,224 L1440,320 L0,320 Z"></path>
                            </svg>
                        </div>

                        <div class="scholar-role-badge">
                            <i class="bi {{ $scholar['icon'] }}"></i>
                        </div>
                    </div>

                    <div class="scholar-body-v2 text-center">
                        <h4 class="scholar-name-v2">{{ $scholar['name'] }}</h4>
                        <div class="scholar-line-separator"></div>
                        <p class="scholar-role-text-gold">{{ $scholar['role'] }}</p>
                        <p class="scholar-dept-text">{{ $scholar['dept'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="scholars-slider-dots mt-5">
            <span class="dot"></span>
            <span class="dot active"></span>
            <span class="dot"></span>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('team') }}" class="btn-explore-scholars shadow-sm">
                <i class="bi bi-bank me-2"></i> Explore Our Team <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════
     Section: Process of Getting a Fatwa
     ══════════════════════════════════════════════ --}}
<section class="premium-process-section py-5">
    <div class="process-watermark watermark-left-mosque"></div>
    <div class="process-watermark watermark-right-leaves"></div>
    <div class="process-bg-pattern"></div>

    <div class="container py-4 position-relative" style="z-index: 2;">
        <div class="text-center mb-5">
            <div class="process-eyebrow-container">
                <span class="process-flower">❖</span>
                <span class="process-eyebrow">TRUST & AUTHENTICITY</span>
                <span class="process-flower">❖</span>
            </div>
            <h2 class="process-heading mt-3">Process of Getting a Fatwa</h2>
            
            <div class="hero-divider-small mb-3">
                <span></span><span class="diamond">&#9670;</span><span></span>
            </div>

            <p class="process-subheading">Every inquiry undergoes a rigorous scholarly verification process before being shared with the public.</p>
        </div>
        
        <div class="process-grid-row">
            @php
            $steps = [
                [
                    'num' => '01',
                    'title' => 'Register Account',
                    'desc' => 'Create your verified profile to track your scholarly inquiries.',
                    'big_icon' => 'bi-person-check',
                    'small_icon' => 'bi-person',
                    'color_class' => 'step-green'
                ],
                [
                    'num' => '02',
                    'title' => 'Submit Inquiry',
                    'desc' => 'Detail your situation. Use Arabic support for scholarly references if needed.',
                    'big_icon' => 'bi-pencil-square',
                    'small_icon' => 'bi-pencil-fill',
                    'color_class' => 'step-orange'
                ],
                [
                    'num' => '03',
                    'title' => 'Scholar Review',
                    'desc' => 'Qualified Muftis draft authentic rulings based on classical texts.',
                    'big_icon' => 'bi-mortarboard',
                    'small_icon' => 'bi-book-half',
                    'color_class' => 'step-blue'
                ],
                [
                    'num' => '04',
                    'title' => 'Administrative Certification',
                    'desc' => 'Final verification by the Head of Ifta before publication.',
                    'big_icon' => 'bi-shield-check',
                    'small_icon' => 'bi-patch-check',
                    'color_class' => 'step-purple'
                ]
            ];
            @endphp

            @foreach($steps as $index => $step)
                <div class="process-card-col {{ $step['color_class'] }}">
                    <div class="process-premium-card">
                        <div class="process-num-pill">{{ $step['num'] }}</div>

                        <div class="process-icon-circle mb-3">
                            <i class="bi {{ $step['big_icon'] }}"></i>
                        </div>

                        <h4 class="process-card-title">{{ $step['title'] }}</h4>
                        <div class="process-card-line"></div>
                        <p class="process-card-desc">{{ $step['desc'] }}</p>

                        <div class="process-bottom-badge">
                            <i class="bi {{ $step['small_icon'] }}"></i>
                        </div>
                    </div>

                    @if($index < 3)
                        <div class="process-connector-line">
                            <span class="connector-dot"></span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('styles')
<style>
    /* Global Page Variables & General Overrides */
    .transition-all { transition: all 0.3s ease; }
    .text-brand-gold { color: #c9a054 !important; }
    .text-green { color: #1a7a4a !important; }
    .text-blue { color: #2563eb !important; }
    .text-gold { color: #c9a054 !important; }
    .text-teal { color: #0d9488 !important; }

    /* ============================================
       1. Redesigned Premium Static Hero & Search
       ============================================ */
    .premium-static-hero {
        position: relative;
        width: 100%;
        min-height: 640px;
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        padding: 100px 0 140px;
        display: flex;
        align-items: center;
        overflow: hidden;
        z-index: 1;
    }
    .hero-bg-overlay-pattern {
        position: absolute;
        inset: 0;
        opacity: 0.015;
        pointer-events: none;
        z-index: 1;
        background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><path d="M30,0 L60,30 L30,60 L0,30 Z" fill="none" stroke="black" stroke-width="0.5"/></svg>');
        background-size: 30px 30px;
    }
    .hero-content-container {
        position: relative;
        z-index: 5;
    }
    .hero-text-block {
        text-shadow: 0 2px 12px rgba(0,0,0,0.55);
    }
    .hero-bismillah {
        color: #c9a054;
        font-family: 'Amiri', 'Noto Naskh Arabic', serif;
        font-size: 1.45rem;
        margin-bottom: 0.5rem;
        letter-spacing: 1px;
        font-weight: bold;
    }
    .hero-main-title {
        font-family: Georgia, 'Times New Roman', serif;
        font-weight: bold;
        color: #ffffff;
        margin-bottom: 0.5rem;
    }
    .hero-subtitle {
        color: #e2e8f0;
        font-size: 1.15rem;
        font-weight: 500;
    }

    /* Search Box styling */
    .hero-premium-search-card {
        max-width: 860px;
        background: #ffffff;
        border-radius: 28px;
        padding: 30px 30px 0 30px;
        border: 1px solid rgba(0,0,0,0.03);
        overflow: hidden;
    }
    .hero-search-row {
        display: flex;
        align-items: center;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 18px;
        padding: 6px 6px 6px 20px;
    }
    .search-input-col {
        flex: 1;
        display: flex;
        align-items: center;
    }
    .search-input-col .form-control {
        font-size: 0.95rem;
        color: #1e293b;
    }
    .search-input-col .form-control::placeholder {
        color: #94a3b8;
    }
    .search-divider {
        width: 1.5px;
        height: 28px;
        background: #e2e8f0;
        margin: 0 15px;
    }
    .search-dropdown-col {
        width: 150px;
    }
    .search-dropdown-col .form-select {
        font-size: 0.92rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        padding-right: 25px;
    }
    .btn-search-submit {
        background: #063a2a;
        color: #ffffff;
        font-size: 0.95rem;
        font-weight: 700;
        border-radius: 12px;
        padding: 10px 32px;
        transition: all 0.2s ease;
        border: none;
    }
    .btn-search-submit:hover {
        background: #04251b;
        color: #ffffff;
        transform: translateY(-1px);
    }
    .hero-popular-searches {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding: 4px 10px;
    }
    .popular-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748b;
    }
    .popular-pills {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .search-pill-btn {
        background: #f1f5f9;
        color: #475569;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        padding: 4px 15px;
        border-radius: 100px;
        transition: all 0.2s ease;
    }
    .search-pill-btn:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    /* Actions bottom bar */
    .hero-quick-actions-bar {
        background: #fafaf7;
        margin: 25px -30px 0 -30px;
        border-top: 1px solid #e2e8f0;
    }
    .quick-action-link {
        display: flex;
        align-items: center;
        padding: 24px 30px;
        text-decoration: none;
        transition: background 0.2s ease;
    }
    .quick-action-link:hover {
        background: rgba(6, 58, 42, 0.025);
    }
    .action-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .bg-green-tint { background: #e6f7f0; color: #10b981; }
    .bg-gold-tint { background: #fdf6e2; color: #c9a054; }
    .action-text-box {
        margin-left: 15px;
        text-align: left;
    }
    .action-title {
        color: #0d233a;
        font-weight: 800;
        font-size: 0.95rem;
        line-height: 1.2;
    }
    .action-subtitle {
        color: #64748b;
        font-size: 0.76rem;
        margin-top: 2px;
        display: inline-block;
    }
    .hero-wave-divider {
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 90px;
        z-index: 2;
    }
    .hero-wave-divider svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    /* ============================================
       2. Section Titles
       ============================================ */
    .section-gold-title {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 2.1rem;
        font-weight: bold;
        color: #0d233a;
        display: flex;
        align-items: center;
    }
    .title-icon-box {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: #e6f3ec;
        color: #1a7a4a;
        border-radius: 12px;
        font-size: 1.25rem;
        margin-right: 14px;
    }
    .title-icon-box.gold {
        background: #fdf6e2;
        color: #c9a054;
    }
    .section-gold-subtitle {
        color: #64748b;
        font-size: 0.9rem;
        margin-left: 54px;
        margin-bottom: 0;
    }
    .btn-explore-more {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        color: #1a7a4a;
        border: 1.5px solid #1a7a4a;
        padding: 9px 24px;
        border-radius: 50px;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.25s ease;
    }
    .btn-explore-more:hover {
        background: #1a7a4a;
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(26,122,74,0.15);
    }
    .btn-explore-more.secondary {
        color: #c9a054;
        border-color: #c9a054;
    }
    .btn-explore-more.secondary:hover {
        background: #c9a054;
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(199,160,84,0.15);
    }

    /* ============================================
       3. Recent Public Fatwas Card Grid (Mockup V2)
       ============================================ */
    .recent-fatwa-card-v2 {
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #f1f0ea;
        box-shadow: 0 8px 24px rgba(0,0,0,0.02);
        transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.3s ease, border-color 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        cursor: pointer;
    }
    .recent-fatwa-card-v2:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 36px rgba(10,35,64,0.07);
        border-color: rgba(199, 160, 84, 0.25);
    }
    .card-top-half {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-grow: 1;
    }
    .card-text-side {
        flex: 1;
    }
    .badge-custom-category {
        display: inline-block;
        background: #e6f7f0;
        color: #10b981;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 50px;
        letter-spacing: 0.3px;
    }
    .recent-fatwa-title-v2 {
        font-size: 0.98rem;
        font-weight: 800;
        color: #0d233a;
        line-height: 1.5;
        margin: 0;
    }
    .card-image-side {
        width: 95px;
        height: 100px;
        border-radius: 12px;
        background-size: cover;
        background-position: center;
        position: relative;
        flex-shrink: 0;
        margin-left: 10px;
    }
    .card-madhab-badge {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: rgba(13, 35, 58, 0.85);
        color: #ffffff;
        font-size: 0.62rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .card-bottom-bar {
        border-top: 1px solid #f1f0ea;
        color: #64748b;
        font-size: 0.75rem;
    }
    .card-bottom-bar .meta-item {
        display: inline-flex;
        align-items: center;
        font-weight: 500;
    }
    .card-bottom-bar .text-gold {
        font-size: 0.95rem;
        transition: transform 0.25s ease;
    }
    .recent-fatwa-card-v2:hover .card-bottom-bar .text-gold {
        transform: translateX(4px);
    }

    /* ============================================
       4. Values Banner Section
       ============================================ */
    .value-item-box {
        padding: 10px 0;
    }
    .value-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #081d33; /* Darker navy background for icon */
        border: 1.5px solid rgba(199,160,84,0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        color: #c9a054;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        flex-shrink: 0;
    }
    .value-title {
        font-weight: 800;
        font-size: 0.95rem;
        color: #ffffff; /* White title for readability */
        margin-bottom: 4px;
    }
    .value-desc {
        color: #94a3b8; /* Soft light gray text */
        font-size: 0.78rem;
        margin: 0;
        line-height: 1.45;
    }

    /* ============================================
       5. Curated / Featured Section (Mockup V2)
       ============================================ */
    .fatwa-card-featured-v2 {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid #edf2f7;
        box-shadow: 0 6px 20px rgba(0,0,0,0.02);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .fatwa-card-featured-v2:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.06);
    }
    .featured-thumb-v2 {
        position: relative;
        width: 100%;
        height: 190px;
        overflow: hidden;
        background: #f1f5f9;
    }
    .featured-thumb-v2 img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .fatwa-card-featured-v2:hover .featured-thumb-v2 img {
        transform: scale(1.03);
    }
    .featured-madhab-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #c9a054;
        color: #fff;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .featured-hot-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
    }
    .featured-body-v2 {
        padding: 24px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .featured-meta-v2 {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        margin-bottom: 12px;
        font-weight: 600;
    }
    .featured-meta-v2 .category { color: #1a7a4a; }
    .featured-meta-v2 .views { color: #94a3b8; }
    
    .featured-title-v2 {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0d233a;
        line-height: 1.45;
        margin-bottom: 10px;
    }
    .featured-excerpt-v2 {
        font-size: 0.84rem;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    .featured-footer-v2 {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f1f5f9;
        padding-top: 14px;
        margin-top: auto;
    }
    .featured-footer-v2 .author-info {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
    }
    .featured-footer-v2 .author-info i {
        color: #94a3b8;
        font-size: 0.95rem;
    }
    .featured-footer-v2 .read-more {
        font-size: 0.78rem;
        font-weight: 800;
        color: #c9a054;
        display: flex;
        align-items: center;
        gap: 3px;
        transition: transform 0.2s ease;
    }
    .fatwa-card-featured-v2:hover .featured-footer-v2 .read-more {
        transform: translateX(3px);
    }

    /* ============================================
       6. Stats Cards (V2)
       ============================================ */
    .stats-card-v2 {
        background: #ffffff;
        border-radius: 22px;
        padding: 26px 20px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.02);
        border: 1px solid #f1f0ea;
        text-align: center;
        height: 100%;
        transition: transform 0.3s ease;
    }
    .stats-card-v2:hover {
        transform: translateY(-5px);
    }
    .stats-card-v2.border-green { border-bottom: 3.5px solid #1a7a4a; }
    .stats-card-v2.border-blue { border-bottom: 3.5px solid #2563eb; }
    .stats-card-v2.border-gold { border-bottom: 3.5px solid #c9a054; }
    .stats-card-v2.border-teal { border-bottom: 3.5px solid #0d9488; }
    
    .stats-icon {
        font-size: 2.1rem;
        margin-bottom: 12px;
    }
    .stats-number {
        font-size: 1.85rem;
        font-weight: 900;
        margin: 0 0 4px 0;
    }
    .stats-label {
        font-size: 0.78rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    /* ============================================
       7. Meet the Scholars Section
       ============================================ */
    .premium-scholars-section {
        position: relative;
        background: #fafaf7;
        padding: 85px 0;
        overflow: hidden;
    }
    .scholars-watermark {
        position: absolute;
        width: 220px;
        height: 220px;
        background-repeat: no-repeat;
        background-size: contain;
        opacity: 0.035;
        pointer-events: none;
        z-index: 1;
    }
    .watermark-left {
        left: 20px;
        top: 15%;
        background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="black" stroke-width="0.75"><circle cx="50" cy="50" r="30"/><circle cx="50" cy="50" r="45"/><path d="M50,5 C55,20 45,20 50,5 Z"/><path d="M50,95 C55,80 45,80 50,95 Z"/><path d="M5,50 C20,55 20,45 5,50 Z"/><path d="M95,50 C80,55 80,45 95,50 Z"/><path d="M18,18 C28,28 20,35 18,18 Z"/><path d="M82,82 C72,72 80,65 82,82 Z"/><path d="M18,82 C28,72 20,65 18,82 Z"/><path d="M82,18 C72,28 80,35 82,18 Z"/></g></svg>');
    }
    .watermark-right {
        right: 20px;
        bottom: 15%;
        background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="black" stroke-width="0.75"><circle cx="50" cy="50" r="30"/><circle cx="50" cy="50" r="45"/><path d="M50,5 C55,20 45,20 50,5 Z"/><path d="M50,95 C55,80 45,80 50,95 Z"/><path d="M5,50 C20,55 20,45 5,50 Z"/><path d="M95,50 C80,55 80,45 95,50 Z"/><path d="M18,18 C28,28 20,35 18,18 Z"/><path d="M82,82 C72,72 80,65 82,82 Z"/><path d="M18,82 C28,72 20,65 18,82 Z"/><path d="M82,18 C72,28 80,35 82,18 Z"/></g></svg>');
    }
    .scholars-dots {
        position: absolute;
        width: 32px;
        height: 50px;
        background-image: radial-gradient(#d1d5db 2px, transparent 2px);
        background-size: 8px 8px;
        opacity: 0.65;
        z-index: 1;
        pointer-events: none;
    }
    .dots-left {
        left: 2%;
        top: 52%;
        transform: translateY(-50%);
    }
    .dots-right {
        right: 2%;
        top: 52%;
        transform: translateY(-50%);
    }
    .scholars-eyebrow-container {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        border: 1px solid rgba(199, 160, 84, 0.35);
        background: rgba(199, 160, 84, 0.05);
        padding: 5px 20px;
        border-radius: 50px;
    }
    .scholars-eyebrow {
        color: #0a2540;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 2px;
    }
    .scholars-flower {
        color: #c9a054;
        font-size: 0.75rem;
    }
    .scholars-heading {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 2.5rem;
        font-weight: bold;
        color: #0a2540;
    }
    .scholars-heading .text-brand-gold {
        color: #c9a054;
        font-family: 'Times New Roman', Georgia, serif;
        font-style: italic;
    }
    .scholars-subheading {
        color: #718096;
        font-size: 0.95rem;
        max-width: 600px;
        margin: 0 auto;
    }
    .hero-divider-small {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin: 0.5rem auto;
    }
    .hero-divider-small span:not(.diamond) {
        display: block;
        width: 50px;
        height: 1px;
        background: linear-gradient(to right, transparent, #c9a054, transparent);
    }
    .hero-divider-small .diamond {
        color: #c9a054;
        font-size: 0.55rem;
    }
    .scholars-grid-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 16px;
        margin-top: 40px;
    }
    .scholar-col-5 {
        flex: 0 0 calc(20% - 13px);
        max-width: calc(20% - 13px);
        min-width: 195px;
    }
    .scholar-premium-card-v2 {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 8px 30px rgba(0,0,0,0.03);
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        border: 1px solid rgba(0,0,0,0.03);
    }
    .scholar-premium-card-v2:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(10,37,64,0.12);
    }
    .scholar-ribbon-tag {
        position: absolute;
        top: 0;
        left: 18px;
        width: 24px;
        height: 32px;
        background: #0a2540;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c9a054;
        font-size: 0.75rem;
        border-radius: 0 0 3px 3px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    }
    .scholar-ribbon-tag::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 0;
        height: 0;
        border-left: 12px solid #0a2540;
        border-right: 12px solid #0a2540;
        border-bottom: 5px solid transparent;
    }
    .scholar-photo-area {
        position: relative;
        width: 100%;
        height: 220px;
        background: #fafaf7;
        background-image: radial-gradient(rgba(199, 160, 84, 0.08) 1px, transparent 1px);
        background-size: 16px 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .scholar-img-v2 {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .scholar-premium-card-v2:hover .scholar-img-v2 {
        transform: scale(1.04);
    }
    .scholar-initials-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #f5f4ef;
        border: 1.5px solid rgba(199, 160, 84, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
    }
    .scholar-initials-circle span {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 1.7rem;
        font-weight: bold;
        color: #0a2540;
    }
    .scholar-initials-fallback {
        position: absolute;
        inset: 0;
        background: #f5f4ef;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .scholar-initials-fallback span {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 1.8rem;
        font-weight: bold;
        color: #0a2540;
    }
    .scholar-wave-separator {
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 25px;
        z-index: 2;
    }
    .scholar-wave-separator svg {
        width: 100%;
        height: 100%;
        display: block;
    }
    .scholar-role-badge {
        position: absolute;
        bottom: -13px;
        left: 50%;
        transform: translateX(-50%);
        width: 32px;
        height: 32px;
        background: #ffffff;
        border: 1px solid #c9a054;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 5;
        color: #c9a054;
        font-size: 0.85rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    }
    .scholar-body-v2 {
        background: #0a2540;
        padding: 34px 14px 24px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .scholar-name-v2 {
        color: #ffffff;
        font-size: 1rem;
        font-weight: bold;
        margin: 0;
        letter-spacing: -0.2px;
        line-height: 1.25;
    }
    .scholar-line-separator {
        width: 32px;
        height: 1px;
        background: #c9a054;
        margin: 10px auto;
        opacity: 0.75;
    }
    .scholar-role-text-gold {
        color: #c9a054;
        font-size: 0.75rem;
        font-weight: 700;
        margin: 0 0 2px 0;
    }
    .scholar-dept-text {
        color: rgba(255,255,255,0.65);
        font-size: 0.68rem;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .scholars-slider-dots {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }
    .scholars-slider-dots .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #e2e8f0;
        display: inline-block;
    }
    .scholars-slider-dots .dot.active {
        background: #c9a054;
        width: 20px;
        border-radius: 4px;
    }
    .btn-explore-scholars {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #0a2540;
        color: #ffffff;
        padding: 11px 26px;
        border-radius: 100px;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid #c9a054;
        transition: all 0.3s ease;
    }
    .btn-explore-scholars:hover {
        background: #c9a054;
        color: #0a2540;
        box-shadow: 0 8px 20px rgba(199, 160, 84, 0.2);
        transform: translateY(-2px);
    }
    @media(max-width: 1100px) {
        .scholar-col-5 {
            flex: 0 0 calc(33.33% - 12px);
            max-width: calc(33.33% - 12px);
        }
    }
    @media(max-width: 480px) {
        .scholar-col-5 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    /* ============================================
       8. Process Section — Redesigned Mockup Layout
       ============================================ */
    .premium-process-section {
        position: relative;
        background: #07162c; /* Deep premium dark navy */
        padding: 90px 0;
        overflow: hidden;
    }
    .watermark-left-mosque {
        position: absolute;
        left: 20px;
        top: 20%;
        width: 280px;
        height: 280px;
        background-repeat: no-repeat;
        background-size: contain;
        opacity: 0.035;
        pointer-events: none;
        z-index: 1;
        background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="white" stroke-width="1.2"><path d="M20,180 L180,180 M30,180 L30,130 C30,110 50,90 70,90 L70,180 M170,180 L170,130 C170,110 150,90 130,90 L130,180 M70,90 L70,80 C70,50 130,50 130,80 L130,90 M100,50 L100,20 M90,30 L110,30 M100,50 C80,50 80,70 100,70 C120,70 120,50 100,50 Z"/></g></svg>');
    }
    .watermark-right-leaves {
        position: absolute;
        right: 0;
        top: 0;
        width: 260px;
        height: 260px;
        background-repeat: no-repeat;
        background-size: contain;
        opacity: 0.085;
        pointer-events: none;
        z-index: 3;
        background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg"><path d="M120,0 C100,15 80,22 60,25 C50,26 30,22 25,20 C23,19 20,15 20,15 C20,15 25,25 35,30 C45,35 65,33 85,25 C105,17 120,0 120,0 Z" fill="%232e7d32"/><path d="M90,10 C75,18 60,22 45,22 C35,22 25,18 20,16 C22,20 30,24 40,26 C50,28 70,26 85,18 C100,10 90,10 90,10 Z" fill="%231b5e20"/><path d="M110,5 C95,13 80,18 65,20 C55,21 40,18 35,16 C38,20 45,24 55,26 C65,28 85,26 100,16 C110,6 110,5 110,5 Z" fill="%2381c784"/></svg>');
    }
    .process-bg-pattern {
        position: absolute;
        inset: 0;
        opacity: 0.015;
        pointer-events: none;
        z-index: 1;
        background-image: url('data:image/svg+xml;utf8,<svg viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><path d="M30,0 L60,30 L30,60 L0,30 Z" fill="none" stroke="white" stroke-width="0.5"/></svg>');
        background-size: 40px 40px;
    }
    .process-eyebrow-container {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        border: 1px solid rgba(199, 160, 84, 0.45);
        background: rgba(199, 160, 84, 0.08);
        padding: 5px 20px;
        border-radius: 50px;
    }
    .process-eyebrow {
        color: #c9a054;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 2px;
    }
    .process-flower {
        color: #c9a054;
        font-size: 0.75rem;
    }
    .process-heading {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 2.6rem;
        font-weight: bold;
        color: #ffffff; /* White title on dark bg */
    }
    .process-subheading {
        color: #94a3b8; /* Lighter text on dark bg */
        font-size: 0.95rem;
        max-width: 600px;
        margin: 0 auto;
    }
    .process-grid-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 24px;
        margin-top: 50px;
        position: relative;
    }
    .process-card-col {
        flex: 1 1 240px;
        max-width: 280px;
        position: relative;
    }
    .process-premium-card {
        background: #ffffff;
        border-radius: 28px;
        padding: 50px 24px 36px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.035);
        border: 1px solid rgba(0, 0, 0, 0.03);
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
        position: relative;
        text-align: center;
    }
    .process-premium-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(13, 35, 58, 0.08);
    }
    .step-green .process-premium-card { border-bottom: 3.5px solid #10b981; }
    .step-orange .process-premium-card { border-bottom: 3.5px solid #f59e0b; }
    .step-blue .process-premium-card { border-bottom: 3.5px solid #3b82f6; }
    .step-purple .process-premium-card { border-bottom: 3.5px solid #8b5cf6; }
    .process-num-pill {
        position: absolute;
        top: -14px;
        left: 50%;
        transform: translateX(-50%);
        padding: 4px 20px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 800;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    .step-green .process-num-pill { background: #e6f7f0; color: #10b981; }
    .step-orange .process-num-pill { background: #fef3c7; color: #d97706; }
    .step-blue .process-num-pill { background: #eff6ff; color: #2563eb; }
    .step-purple .process-num-pill { background: #f5f3ff; color: #7c3aed; }
    .process-icon-circle {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        box-shadow: inset 0 2px 6px rgba(0,0,0,0.02);
    }
    .step-green .process-icon-circle { background: #e6f7f0; color: #10b981; }
    .step-orange .process-icon-circle { background: #fef3c7; color: #d97706; }
    .step-blue .process-icon-circle { background: #eff6ff; color: #2563eb; }
    .step-purple .process-icon-circle { background: #f5f3ff; color: #7c3aed; }
    .process-card-title {
        color: #0d233a;
        font-size: 1.15rem;
        font-weight: bold;
        margin: 8px 0 0 0;
    }
    .process-card-line {
        width: 25px;
        height: 2px;
        margin: 10px auto 14px;
    }
    .step-green .process-card-line { background: #10b981; }
    .step-orange .process-card-line { background: #f59e0b; }
    .step-blue .process-card-line { background: #3b82f6; }
    .step-purple .process-card-line { background: #8b5cf6; }
    .process-card-desc {
        color: #6b7280;
        font-size: 0.82rem;
        line-height: 1.55;
        margin: 0;
    }
    .process-bottom-badge {
        position: absolute;
        bottom: -16px;
        left: 50%;
        transform: translateX(-50%);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 0.8rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 5;
    }
    .step-green .process-bottom-badge { background: #10b981; }
    .step-orange .process-bottom-badge { background: #f59e0b; }
    .step-blue .process-bottom-badge { background: #3b82f6; }
    .step-purple .process-bottom-badge { background: #8b5cf6; }
    .process-connector-line {
        position: absolute;
        top: 50%;
        right: -20px;
        width: 32px;
        height: 2px;
        border-top: 2px dotted rgba(255,255,255,0.25);
        transform: translateY(-50%);
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .connector-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        border: 2px solid;
        background: #ffffff;
    }
    .step-green .connector-dot { border-color: #10b981; }
    .step-orange .connector-dot { border-color: #f59e0b; }
    .step-blue .connector-dot { border-color: #3b82f6; }
    .step-purple .connector-dot { border-color: #8b5cf6; }
    @media(max-width: 1200px) {
        .process-connector-line { display: none; }
        .process-card-col {
            flex: 0 0 calc(50% - 12px);
            max-width: calc(50% - 12px);
        }
    }
    @media(max-width: 576px) {
        .process-card-col {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    /* ============================================
       9. Fatwa Card — Clean Box Model Design
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
    .fatwa-body {
        padding: 18px 20px 16px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .fatwa-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        color: #a0aec0;
        margin-bottom: 10px;
        font-weight: 500;
    }
    .fatwa-meta span:first-child { color: #1a7a4a; font-weight: 600; }
    .fatwa-title {
        font-size: 0.98rem;
        font-weight: 800;
        color: #1a202c;
        line-height: 1.45;
        margin-bottom: 10px;
        flex-grow: 1;
    }
    .fatwa-excerpt {
        font-size: 0.83rem;
        color: #718096;
        line-height: 1.6;
        margin-bottom: 16px;
    }
    .fatwa-footer {
        display: flex;
        align-items: center;
        border-top: 1px solid #f1f5f9;
        padding-top: 12px;
        margin-top: auto;
        gap: 6px;
        flex-wrap: nowrap;
        overflow: hidden;
    }
    .fatwa-author {
        font-size: 0.8rem;
        font-weight: 600;
        color: #4a5568;
        flex: 1 1 0;
        min-width: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }
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