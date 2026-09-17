@extends('layouts.public')

@php
    $pageTitle = ($fatwa->title ?: 'Fatwa #' . $fatwa->question_id) . ' | Darul Ifta Kauzariyya';
    $rawSummary = trim(strip_tags($fatwa->body . ' — ' . $fatwa->answer));
    $metaDescription = \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $rawSummary), 160);
    $canonicalUrl = route('fatwa.show', $fatwa);
    $featuredImg = $fatwa->featured_image ? asset('storage/' . $fatwa->featured_image) : asset('assets/img/slider/center.png');

    // Structured QAPage Schema
    $qaSchema = [
        "@context" => "https://schema.org",
        "@type" => "QAPage",
        "mainEntity" => [
            "@type" => "Question",
            "name" => $fatwa->title ?: 'Islamic Ruling: ' . \Illuminate\Support\Str::limit(strip_tags($fatwa->body), 80),
            "text" => strip_tags($fatwa->body),
            "answerCount" => 1,
            "dateCreated" => $fatwa->created_at ? $fatwa->created_at->toIso8601String() : now()->toIso8601String(),
            "author" => [
                "@type" => "Person",
                "name" => "Anonymous Questioner",
            ],
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => strip_tags($fatwa->answer),
                "dateCreated" => $fatwa->published_at ? $fatwa->published_at->toIso8601String() : now()->toIso8601String(),
                "upvoteCount" => (int) $fatwa->likes,
                "url" => $canonicalUrl,
                "citation" => $fatwa->references->map(fn($r) => $r->book_name . ($r->arabic_text ? ': ' . strip_tags($r->arabic_text) : ''))->values()->toArray(),
                "author" => [
                    "@type" => "Person",
                    "name" => $fatwa->assignedMufti?->name ?? 'Mufti of Darul Ifta Kauzariyya',
                    "jobTitle" => "Islamic Jurisconsult / Mufti",
                    "worksFor" => [
                        "@type" => "Organization",
                        "name" => "Darul Ifta Kauzariyya",
                        "url" => url('/'),
                    ],
                ],
            ],
        ],
    ];

    // Structured Breadcrumbs Schema
    $breadcrumbItems = [
        [
            "@type" => "ListItem",
            "position" => 1,
            "name" => "Home",
            "item" => route('home'),
        ],
        [
            "@type" => "ListItem",
            "position" => 2,
            "name" => "Fatwa Archive",
            "item" => route('fatwa.archive'),
        ],
    ];

    if ($fatwa->category) {
        $catSlug = $fatwa->category->slug ?: $fatwa->category->id;
        $breadcrumbItems[] = [
            "@type" => "ListItem",
            "position" => 3,
            "name" => $fatwa->category->name,
            "item" => route('fatwa.archive', ['category' => $catSlug]),
        ];
        $breadcrumbItems[] = [
            "@type" => "ListItem",
            "position" => 4,
            "name" => $fatwa->title ?: 'Fatwa #' . $fatwa->question_id,
            "item" => $canonicalUrl,
        ];
    } else {
        $breadcrumbItems[] = [
            "@type" => "ListItem",
            "position" => 3,
            "name" => $fatwa->title ?: 'Fatwa #' . $fatwa->question_id,
            "item" => $canonicalUrl,
        ];
    }

    $breadcrumbSchema = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => $breadcrumbItems,
    ];
@endphp

@section('title', $pageTitle)
@section('meta_description', $metaDescription)
@section('canonical_url', $canonicalUrl)
@section('og_type', 'article')
@section('og_title', $pageTitle)
@section('og_image', $featuredImg)

@push('schema')
<script type="application/ld+json">
{!! json_encode($qaSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('content')
    @php
        $textToCheck = ($fatwa->title ?? '') . ' ' . ($fatwa->body ?? '') . ' ' . ($fatwa->answer ?? '');
        $isMalayalam = $fatwa->language == 'Malayalam' || preg_match('/[\x{0D00}-\x{0D7F}]/u', $textToCheck);
        $isArabic = $fatwa->language == 'Arabic' || (!$isMalayalam && preg_match('/[\x{0600}-\x{06FF}]/u', $textToCheck));
        $isUrdu = $fatwa->language == 'Urdu';

        $langClass = 'inter-font font-eng';
        if ($isUrdu) {
            $langClass = 'gulzar-font ur-text font-urdu';
        } elseif ($isArabic) {
            $langClass = 'alyamama-font ar-text font-arabic';
        } elseif ($isMalayalam) {
            $langClass = 'noto-serif-malayalam-font ml-text font-malayalam';
        }
    @endphp

    <style>
        :root {
            --slate-blue: #4A6372;
            --brand-green: #1a7a4a;
            --bg-soft: #f4f7f9;
            --border-color: #edf2f7;
        }

        .main-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Redesigned Card */
        .fatwa-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
        }

        .featured-image-wrapper {
            width: 100%;
            max-height: 480px;
            overflow: hidden;
            background: #f0f0f0;
        }

        .featured-image-wrapper img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .editorial-meta {
            padding: 20px 25px 0;
            font-size: 0.9rem;
            color: #718096;
        }

        .editorial-meta b {
            color: #2d3748;
        }

        .header-section {
            padding: 15px 25px 25px;
            border-bottom: 1px solid var(--border-color);
        }

        .header-section h1 {
            font-size: 1.85rem;
            font-weight: 800;
            color: #1a202c;
            line-height: 1.3;
            margin-bottom: 20px;
        }

        .meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px 30px;
            padding-top: 15px;
            border-top: 1px solid #f7fafc;
        }

        .meta-unit {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .meta-unit .label {
            font-weight: 700;
            font-size: 0.8rem;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-unit .value {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--brand-green);
        }

        .body-content {
            padding: 30px 25px;
        }

        .content-block {
            margin-bottom: 35px;
        }

        .content-block:last-child {
            margin-bottom: 0;
        }

        .block-h {
            font-weight: 800;
            font-size: 1.25rem;
            color: #000;
            margin-bottom: 16px;
            display: block;
            border-left: 4px solid var(--brand-green);
            padding-left: 12px;
        }

        .block-text {
            font-size: 1.18rem;
            line-height: 2.0;
            color: #2d3748;
        }

        .block-text p {
            margin-bottom: 1.2rem;
        }

        /* Enforce font family & increased size across entire content hierarchy */
        .block-text.noto-serif-malayalam-font,
        .block-text.noto-serif-malayalam-font *,
        .block-text.ml-text,
        .block-text.ml-text *,
        .block-text.font-malayalam,
        .block-text.font-malayalam * {
            font-family: 'Noto Serif Malayalam', serif !important;
            font-size: 1.18rem !important;
            line-height: 2.1 !important;
        }

        .block-text.ur-text,
        .block-text.ur-text *,
        .block-text.font-urdu,
        .block-text.font-urdu * {
            font-family: 'Noto Nastaliq Urdu', serif !important;
            font-size: 1.25rem !important;
            direction: rtl;
            text-align: right;
            line-height: 2.4 !important;
        }

        .block-text.ar-text,
        .block-text.ar-text *,
        .block-text.font-arabic,
        .block-text.font-arabic * {
            font-family: 'Noto Naskh Arabic', 'Amiri', serif !important;
            font-size: 1.28rem !important;
            direction: rtl;
            text-align: right;
            line-height: 2.2 !important;
        }

        .block-text.inter-font,
        .block-text.inter-font * {
            font-family: 'Google Sans Eng', 'Inter', sans-serif !important;
            font-size: 1.15rem !important;
            line-height: 1.95 !important;
        }

        .arabic-quote {
            font-family: 'Noto Naskh Arabic', 'Amiri', serif !important;
            font-size: 1.65rem;
            text-align: right;
            direction: rtl;
            margin: 22px 0;
            color: #1a202c;
            line-height: 2.2;
        }

        /* Typography & Font Stacks */
        .noto-serif-malayalam-font,
        .ml-text,
        .font-malayalam {
            font-family: 'Noto Serif Malayalam', 'Noto Sans Malayalam', 'Manjari', 'Gayathri', sans-serif !important;
            font-feature-settings: "kern" 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .noto-sans-malayalam-font {
            font-family: 'Noto Sans Malayalam', 'Noto Serif Malayalam', sans-serif !important;
            -webkit-font-smoothing: antialiased;
        }

        .alyamama-font,
        .ar-text,
        .font-arabic {
            font-family: 'Amiri', 'Noto Naskh Arabic', 'Scheherazade New', serif !important;
            direction: rtl;
            text-align: right;
            -webkit-font-smoothing: antialiased;
        }

        .gulzar-font,
        .ur-text,
        .font-urdu {
            font-family: 'Noto Nastaliq Urdu', 'Gulzar', serif !important;
            direction: rtl;
            text-align: right;
            -webkit-font-smoothing: antialiased;
        }

        .inter-font,
        .font-eng {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
            -webkit-font-smoothing: antialiased;
        }

        .header-section h1 {
            font-size: 1.85rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.45;
            letter-spacing: -0.01em;
            margin-bottom: 18px;
        }

        .header-section h1.noto-serif-malayalam-font,
        .header-section h1.ml-text,
        .header-section h1.font-malayalam {
            font-size: 1.75rem;
            line-height: 1.55;
            font-weight: 700;
        }

        /* Sidebar Styles */
        .sidebar-widget {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border-color);
        }

        .widget-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--slate-blue);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--brand-green);
            display: inline-block;
        }

        .sidebar-search .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            font-size: 0.95rem;
        }

        .recent-item {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
            text-decoration: none;
            color: inherit;
            transition: transform 0.15s ease, opacity 0.15s ease;
        }

        .recent-item:last-child {
            margin-bottom: 0;
        }

        .recent-item:hover {
            transform: translateX(3px);
            opacity: 0.95;
        }

        .recent-thumb {
            width: 82px;
            height: 64px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            background: #f0f4f8;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .recent-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .recent-thumb-fallback {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1d4054 0%, #295A75 100%);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 4px;
            border-radius: 7px;
        }

        .recent-thumb-fallback i {
            font-size: 1.15rem;
            color: #c9a84c;
            margin-bottom: 2px;
        }

        .recent-thumb-fallback .thumb-qid {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            font-family: 'Inter', sans-serif;
            opacity: 0.95;
        }

        .recent-info {
            flex: 1;
            min-width: 0;
        }

        .recent-info h6 {
            font-size: 0.94rem;
            font-weight: 600;
            line-height: 1.48;
            margin: 0;
            color: #1f2937;
            font-family: 'Noto Serif Malayalam', 'Noto Sans Malayalam', 'Plus Jakarta Sans', 'Inter', sans-serif;
            transition: color 0.15s ease;
        }

        .recent-item:hover .recent-info h6 {
            color: var(--brand-green);
        }

        .recent-info .date {
            font-size: 0.76rem;
            color: #6b7280;
            margin-top: 4px;
            display: block;
            font-weight: 500;
        }

        .cat-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .cat-list li {
            margin-bottom: 12px;
        }

        .cat-list a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-decoration: none;
            color: #4a5568;
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.2s;
        }

        .cat-list a:hover {
            color: var(--brand-green);
        }

        .cat-count {
            background: #edf2f7;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            color: #718096;
        }

        /* Signatures Section */
        .signature-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 25px;
            margin-top: 40px;
            border: 1px solid #e2e8f0;
        }

        .sig-row {
            margin-bottom: 12px;
            display: flex;
            gap: 8px;
            font-size: 0.95rem;
        }

        .sig-row b {
            color: #1a202c;
        }

        .sig-row span {
            color: var(--brand-green);
            font-weight: 700;
        }

        .ref-card {
            background: #fbfcfd;
            border: 1px solid #e2e8f0;
            border-left: 5px solid var(--brand-green);
            padding: 20px 22px;
            border-radius: 0 10px 10px 0;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .ref-card .arabic-text {
            font-family: 'Amiri', 'Noto Naskh Arabic', 'Scheherazade New', serif !important;
            font-size: 1.4rem !important;
            line-height: 2.2 !important;
            color: #111827 !important;
            direction: rtl !important;
            text-align: right !important;
            margin-bottom: 12px;
        }

        .ref-card .arabic-text.gulzar-font,
        .ref-card .arabic-text.font-urdu {
            font-family: 'Noto Nastaliq Urdu', 'Gulzar', serif !important;
            font-size: 1.35rem !important;
            line-height: 2.5 !important;
        }

        .ref-card .ref-book-meta {
            font-size: 0.95rem;
            font-weight: 600;
            color: #4b5563;
            border-top: 1px dashed #e2e8f0;
            padding-top: 10px;
            margin-top: 10px;
        }

        .ref-card .ref-book-meta .book-name {
            font-size: 1.05rem;
            color: #1f2937;
        }

        /* Bismillah & Opening Phrase */
        .arabic-bismillah-box {
            text-align: right;
            direction: rtl;
            margin: 15px 0 25px 0;
            padding: 10px 0;
        }

        .arabic-bismillah-box .bismillah-text {
            font-family: 'Amiri', 'Noto Naskh Arabic', 'Scheherazade New', serif !important;
            font-size: 1.85rem;
            font-weight: 700;
            color: #1a202c;
            line-height: 2.1;
        }

        .arabic-bismillah-box .jawab-text {
            font-family: 'Amiri', 'Noto Naskh Arabic', 'Scheherazade New', serif !important;
            font-size: 1.55rem;
            font-weight: 600;
            color: #2d3748;
            line-height: 2.1;
        }

        /* Closing Phrase */
        .arabic-closing-box {
            text-align: right;
            direction: rtl;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
            margin-top: 30px;
        }

        .arabic-closing-box .closing-text {
            font-family: 'Amiri', 'Noto Naskh Arabic', 'Scheherazade New', serif !important;
            font-size: 1.7rem;
            font-weight: 700;
            color: #1a202c;
            line-height: 2.1;
        }
        /* Fatwa Tag Pill for @{Question-ID} references */
        .fatwa-tag-pill {
            display: inline-flex;
            align-items: center;
            background: #ecfdf5;
            color: #047857 !important;
            border: 1px solid #a7f3d0;
            padding: 2px 9px;
            border-radius: 6px;
            font-size: 0.9em;
            font-weight: 600;
            text-decoration: none !important;
            margin: 0 3px;
            transition: all 0.2s ease;
            vertical-align: middle;
        }

        .fatwa-tag-pill:hover {
            background: #d1fae5;
            border-color: #059669;
            color: #065f46 !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.06);
        }

        .fatwa-tag-pill.not-found {
            background: #f1f5f9;
            color: #475569 !important;
            border-color: #cbd5e1;
        }
    </style>

    <div class="main-wrapper py-5">
        @if($fatwa->status !== 'published')
            <div class="alert alert-warning border border-warning shadow-sm d-flex align-items-center mb-4 rounded-3 p-3" role="alert">
                <i class="bi bi-shield-lock-fill fs-3 me-3 text-warning"></i>
                <div>
                    <div class="fw-bold text-dark fs-6">Unpublished Inquiry (Preview Mode)</div>
                    <div class="small text-muted">
                        This inquiry has status <span class="badge bg-warning text-dark text-uppercase fw-bold">{{ $fatwa->status }}</span>. It is not publicly visible on the archive and can only be viewed by authorized roles (Admin, Assigned Mufti, or Author).
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            {{-- Left Column: Main Content --}}
            <div class="col-lg-8">
                <div class="fatwa-card">
                    {{-- 1. Featured Image --}}
                    @if($fatwa->featured_image)
                        <div class="featured-image-wrapper">
                            <img src="{{ asset($fatwa->featured_image) }}" alt="Fatwa Banner">
                        </div>
                    @endif

                    {{-- 2. Editorial Info --}}
                    <div class="editorial-meta d-flex align-items-center flex-wrap gap-2">
                        <span class="text-muted">By</span>
                        @if($fatwa->assignedMufti)
                            <a href="{{ route('fatwa.mufti.archive', $fatwa->assignedMufti) }}"
                               class="text-decoration-none text-dark fw-bold d-inline-flex align-items-center gap-1 hover-green">
                                @if($fatwa->assignedMufti->avatar)
                                    <img src="{{ asset($fatwa->assignedMufti->avatar) }}" alt="{{ $fatwa->assignedMufti->name }}"
                                         class="rounded-circle border border-2 border-white shadow-sm" style="width: 26px; height: 26px; object-fit: cover;">
                                @else
                                    <span class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                                          style="width: 26px; height: 26px; font-size: 0.75rem;">
                                        <i class="bi bi-person-fill"></i>
                                    </span>
                                @endif
                                <span>{{ $fatwa->assignedMufti->name }}</span>
                                <i class="bi bi-patch-check-fill text-primary" style="font-size: 0.95em;" title="Verified Scholar"></i>
                            </a>
                        @else
                            <b>Darul Ifta Scholars</b>
                        @endif

                        <span class="text-muted">in</span>
                        @if($fatwa->category)
                            @php $catSlug = $fatwa->category->slug ?: $fatwa->category->id; @endphp
                            <a href="{{ route('fatwa.archive', ['category' => $catSlug]) }}" class="text-decoration-none text-dark fw-bold hover-green">
                                {{ $fatwa->category->name }}
                            </a>
                            <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.75rem;">
                                {{ number_format($categoryFatwaCount ?? ($fatwa->category->questions()->where('status', 'published')->count() ?? 0)) }} {{ Str::plural('Fatwa', $categoryFatwaCount ?? 1) }}
                            </span>
                        @else
                            <b class="text-dark">General</b>
                        @endif
                    </div>

                    {{-- 3. Header Section --}}
                    <div class="header-section">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h1 class="{{ $langClass }} mb-0">{{ $fatwa->title ?: 'Islamic Query Detail' }}</h1>
                        </div>

                        <div class="meta-row">
                            <div class="meta-unit">
                                <span class="label">Qu-Id:</span>
                                <span class="value">{{ $fatwa->question_id }}</span>
                            </div>
                            <div class="meta-unit">
                                <span class="label">Madhab:</span>
                                <span class="value">{{ $fatwa->madhab?->name ?? '—' }}</span>
                            </div>
                            <div class="meta-unit">
                                <span class="label">Date:</span>
                                <span class="value">{{ $fatwa->published_at?->format('d-m-Y') }}</span>
                            </div>
                            <div class="meta-unit ms-auto no-print">
                                <a href="javascript:window.print()" class="text-muted small"><i
                                        class="bi bi-printer me-1"></i>Print</a>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Content Body --}}
                    <div class="body-content">
                        {{-- Question --}}
                        <div class="content-block">
                            <span class="block-h">Question:</span>
                            <div class="block-text {{ $langClass }}">
                                {!! $fatwa->formatted_body !!}
                            </div>
                        </div>

                        <hr class="my-5" style="opacity: 0.1;">

                        {{-- Answer --}}
                        <div class="content-block">
                            <span class="block-h">Answer:</span>

                            <div class="arabic-bismillah-box">
                                <div class="bismillah-text">بسم الله الرحمن الرحيم</div>
                                <div class="jawab-text">الجواب وبالله التوفيق والنجاح</div>
                            </div>

                            <div class="block-text {{ $langClass }}">
                                {!! $fatwa->formatted_answer !!}
                            </div>

                            {{-- Supporting References — BEFORE closing phrase --}}
                            @if($fatwa->references->count())
                                <div class="mt-4">
                                    @foreach($fatwa->references as $ref)
                                        <div class="ref-card">
                                            <div class="arabic-text {{ $ref->language == 'Urdu' ? 'gulzar-font font-urdu' : 'alyamama-font font-arabic' }}">
                                                {{ $ref->arabic_text }}
                                            </div>
                                            <div class="ref-book-meta d-flex align-items-center gap-2">
                                                <i class="bi bi-book text-success"></i>
                                                <span class="book-name {{ $ref->language == 'Urdu' ? 'gulzar-font font-urdu' : ($ref->language == 'Arabic' ? 'alyamama-font font-arabic' : '') }}">
                                                    {{ $ref->book_name }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="arabic-closing-box">
                                <div class="closing-text">والله أعلم بالصواب</div>
                            </div>

                            <div class="mt-3 no-print d-flex align-items-center gap-2">
                                <button id="likeBtn"
                                    class="btn {{ ($hasLiked ?? false) ? 'btn-danger' : 'btn-outline-danger' }} px-3 rounded-pill"
                                    onclick="handleLike({{ $fatwa->id }})" {{ ($hasLiked ?? false) ? 'disabled' : '' }}
                                    title="{{ ($hasLiked ?? false) ? 'You already liked this!' : 'Like this Fatwa' }}">
                                    <i class="bi bi-heart-fill me-1"></i>{{ ($hasLiked ?? false) ? 'Liked' : 'Like' }} <span
                                        id="likeCount" class="badge bg-danger ms-1">{{ $fatwa->likes }}</span>
                                </button>
                                <button class="btn btn-outline-secondary px-3 rounded-pill" id="shareBtn"
                                    onclick="shareFatwa()">
                                    <i class="bi bi-share me-1"></i>Share
                                </button>
                            </div>
                        </div>


                        {{-- Signatures --}}
                        <div class="signature-box">
                            <div class="sig-row">
                                <b>Written by:</b> <span>{{ $fatwa->assignedMufti->name ?? 'Darul Ifta' }}</span>
                            </div>
                            @php
                                $verifiersList = [];
                                if (is_array($fatwa->verified_by) && count($fatwa->verified_by) > 0) {
                                    $verifiersList = \App\Models\User::whereIn('id', $fatwa->verified_by)->pluck('name')->toArray();
                                }
                            @endphp
                            @if(count($verifiersList) > 0)
                                <div class="sig-row">
                                    <b>Verified by:</b> <span>{{ implode(', ', $verifiersList) }}</span>
                                </div>
                            @endif
                            @if($fatwa->checked_approved_by && $fatwa->approver)
                                <div class="sig-row">
                                    <b>Checked & Approved by:</b> <span>{{ $fatwa->approver->name }}</span>
                                </div>
                            @endif
                            @if($fatwa->publisher)
                                <div class="sig-row">
                                    <b>Published by:</b> <span>{{ $fatwa->publisher->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Sidebar --}}
            <div class="col-lg-4 no-print">
                {{-- Widget: Search --}}
                <div class="sidebar-widget sidebar-search">
                    <form action="{{ route('fatwa.archive') }}" method="GET">
                        <div class="position-relative">
                            <input type="text" name="search" class="form-control" placeholder="Search fatwas...">
                            <button
                                class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent pe-3">
                                <i class="bi bi-search text-muted"></i>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Widget: Recent Posts --}}
                @if(isset($recentFatwas) && $recentFatwas->count())
                    <div class="sidebar-widget">
                        <span class="widget-title">Recent Fatwas</span>
                        @foreach($recentFatwas as $rf)
                            <a href="{{ route('fatwa.show', $rf) }}" class="recent-item">
                                <div class="recent-thumb">
                                    @if($rf->featured_image)
                                        <img src="{{ asset($rf->featured_image) }}" alt="{{ $rf->title ?: 'Fatwa' }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="recent-thumb-fallback" style="display:none;">
                                            <i class="bi bi-journal-text"></i>
                                            <span class="thumb-qid">{{ $rf->question_id }}</span>
                                        </div>
                                    @else
                                        <div class="recent-thumb-fallback">
                                            <i class="bi bi-journal-text"></i>
                                            <span class="thumb-qid">{{ $rf->question_id }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="recent-info">
                                    <h6>{{ Str::limit($rf->title ?: strip_tags($rf->body), 55) }}</h6>
                                    <span class="date">{{ $rf->published_at?->format('M d, Y') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Widget: Related Fatwas --}}
                @if(isset($relatedFatwas) && $relatedFatwas->count())
                    <div class="sidebar-widget">
                        <span class="widget-title">Related Content</span>
                        @php $displayedIds = $recentFatwas->pluck('id')->toArray(); @endphp
                        @foreach($relatedFatwas as $rf)
                            @if(!in_array($rf->id, $displayedIds))
                                <a href="{{ route('fatwa.show', $rf) }}" class="recent-item">
                                    <div class="recent-thumb">
                                        @if($rf->featured_image)
                                            <img src="{{ asset($rf->featured_image) }}" alt="{{ $rf->title ?: 'Fatwa' }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="recent-thumb-fallback" style="display:none;">
                                                <i class="bi bi-journal-text"></i>
                                                <span class="thumb-qid">{{ $rf->question_id }}</span>
                                            </div>
                                        @else
                                            <div class="recent-thumb-fallback">
                                                <i class="bi bi-journal-text"></i>
                                                <span class="thumb-qid">{{ $rf->question_id }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="recent-info">
                                        <h6>{{ Str::limit($rf->title ?: strip_tags($rf->body), 55) }}</h6>
                                        <span class="date text-success">Related Question</span>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Widget: Categories --}}
                @if(isset($allCategories) && $allCategories->count())
                    <div class="sidebar-widget">
                        <span class="widget-title">Categories</span>
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
                @endif
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function handleLike(fatwaId) {
                const btn = document.getElementById('likeBtn');
                const countSpan = document.getElementById('likeCount');

                if (btn.disabled) return;
                btn.disabled = true;

                fetch(`/fatwa-archive/${fatwaId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        countSpan.innerText = data.likes;
                        if (data.already_liked || data.success) {
                            btn.classList.remove('btn-outline-danger');
                            btn.classList.add('btn-danger');
                            btn.innerHTML = '<i class="bi bi-heart-fill me-1"></i>Liked <span class="badge bg-danger ms-1">' + data.likes + '</span>';
                            btn.disabled = true;
                            btn.title = 'You already liked this!';
                        }
                    })
                    .catch(() => {
                        btn.disabled = false;
                    });
            }

            function shareFatwa() {
                const shareData = {
                    title: document.title,
                    text: 'Read this Fatwa from Darul Ifta Kauzariyya',
                    url: window.location.href
                };
                if (navigator.share) {
                    navigator.share(shareData).catch(() => { });
                } else {
                    // Clipboard fallback
                    navigator.clipboard.writeText(window.location.href).then(() => {
                        const btn = document.getElementById('shareBtn');
                        const original = btn.innerHTML;
                        btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Copied!';
                        btn.classList.add('btn-success');
                        btn.classList.remove('btn-outline-secondary');
                        setTimeout(() => {
                            btn.innerHTML = original;
                            btn.classList.remove('btn-success');
                            btn.classList.add('btn-outline-secondary');
                        }, 2000);
                    });
                }
            }

        </script>
    @endpush
@endsection