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
            width: 100%;
            margin: 0 auto;
            padding: 0;
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

        /* Signatures & Scholarly Certification Section */
        .signature-box {
            background: #ffffff;
            border-radius: 14px;
            padding: 22px 24px;
            margin-top: 35px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            position: relative;
        }

        .signature-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(to bottom, #1d4054, #c9a84c);
            border-radius: 14px 0 0 14px;
        }

        .scholarly-cert-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .scholarly-sig-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 14px;
        }

        .sig-card {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 10px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
        }

        .sig-card:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .sig-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            flex-shrink: 0;
        }

        .sig-icon-blue {
            background: rgba(37, 99, 235, 0.1);
            color: #2563eb;
            border: 1px solid rgba(37, 99, 235, 0.2);
        }

        .sig-icon-green {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .sig-icon-gold {
            background: rgba(201, 168, 76, 0.15);
            color: #a8883a;
            border: 1px solid rgba(201, 168, 76, 0.3);
        }

        .sig-icon-slate {
            background: rgba(100, 116, 139, 0.1);
            color: #475569;
            border: 1px solid rgba(100, 116, 139, 0.2);
        }

        .sig-content {
            flex: 1;
            min-width: 0;
        }

        .sig-role-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            margin-bottom: 2px;
        }

        .sig-scholar-link {
            font-size: 0.92rem;
            font-weight: 700;
            color: #1d4054;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: color 0.2s ease;
            word-break: break-word;
        }

        .sig-scholar-link:hover {
            color: #2563eb;
            text-decoration: underline;
        }

        /* Action Footer & Like Button Animation */
        .fatwa-actions-bar {
            margin-top: 25px;
            padding-top: 18px;
            border-top: 1px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: wrap;
            gap: 12px;
        }

        .like-btn-animated {
            position: relative;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: visible;
            display: inline-flex;
            align-items: center;
        }

        .like-btn-animated:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(220, 53, 69, 0.25);
        }

        .like-btn-animated .like-icon {
            display: inline-block;
            transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .like-btn-animated:hover:not(:disabled) .like-icon {
            transform: scale(1.22);
        }

        @keyframes heartPop {
            0% { transform: scale(1); }
            20% { transform: scale(1.45) rotate(-12deg); }
            40% { transform: scale(0.85) rotate(8deg); }
            65% { transform: scale(1.2) rotate(-4deg); }
            85% { transform: scale(0.95) rotate(2deg); }
            100% { transform: scale(1) rotate(0deg); }
        }

        @keyframes pulseGlow {
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.6); }
            70% { box-shadow: 0 0 0 12px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }

        .like-btn-animated.is-animating {
            animation: pulseGlow 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .like-btn-animated.is-animating .like-icon {
            animation: heartPop 0.65s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
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

    <div class="container py-4">
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

                            {{-- Scholarly Certification & Signatures Box --}}
                            <div class="signature-box">
                                <div class="scholarly-cert-title">
                                    <i class="bi bi-patch-check-fill text-warning me-1"></i>
                                    <span>Scholarly Verification & Certification</span>
                                </div>

                                <div class="scholarly-sig-grid">
                                    {{-- 1. Written by (Single Mufti) --}}
                                    @if($fatwa->assignedMufti)
                                        <div class="sig-card">
                                            <div class="sig-icon-wrap sig-icon-blue"><i class="bi bi-pen-fill"></i></div>
                                            <div class="sig-content">
                                                <div class="sig-role-label">Written by</div>
                                                <a href="{{ route('fatwa.mufti.archive', $fatwa->assignedMufti) }}" class="sig-scholar-link">
                                                    <span>{{ $fatwa->assignedMufti->name }}</span>
                                                    <i class="bi bi-arrow-up-right-square ms-1 opacity-75 small"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <div class="sig-card">
                                            <div class="sig-icon-wrap sig-icon-blue"><i class="bi bi-pen-fill"></i></div>
                                            <div class="sig-content">
                                                <div class="sig-role-label">Written by</div>
                                                <span class="sig-scholar-link text-dark">Darul Ifta Faculty</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- 2. Verified by (Minimum 1, can be multiple Muftis) --}}
                                    @php
                                        $verifiers = collect();
                                        if (!empty($fatwa->verified_by)) {
                                            $ids = is_array($fatwa->verified_by) ? $fatwa->verified_by : [$fatwa->verified_by];
                                            $verifiers = \App\Models\User::whereIn('id', $ids)->get();
                                        }
                                    @endphp
                                    @if($verifiers->isNotEmpty())
                                        <div class="sig-card">
                                            <div class="sig-icon-wrap sig-icon-green"><i class="bi bi-shield-check"></i></div>
                                            <div class="sig-content">
                                                <div class="sig-role-label">Verified by</div>
                                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                                    @foreach($verifiers as $v)
                                                        <a href="{{ route('fatwa.mufti.archive', $v) }}" class="sig-scholar-link">
                                                            <span>{{ $v->name }}</span>@if(!$loop->last), @endif
                                                            <i class="bi bi-arrow-up-right-square ms-1 opacity-75 small"></i>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- 3. Checked & Approved by (Optional, Single Mufti/Scholar) --}}
                                    @if($fatwa->checked_approved_by && $fatwa->approver)
                                        <div class="sig-card">
                                            <div class="sig-icon-wrap sig-icon-gold"><i class="bi bi-award-fill"></i></div>
                                            <div class="sig-content">
                                                <div class="sig-role-label">Checked & Approved by</div>
                                                <a href="{{ route('fatwa.mufti.archive', $fatwa->approver) }}" class="sig-scholar-link">
                                                    <span>{{ $fatwa->approver->name }}</span>
                                                    <i class="bi bi-arrow-up-right-square ms-1 opacity-75 small"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions Footer: Like & Share --}}
                            <div class="fatwa-actions-bar no-print">
                                <button id="likeBtn"
                                    class="btn {{ ($hasLiked ?? false) ? 'btn-danger' : 'btn-outline-danger' }} px-3 rounded-pill like-btn-animated"
                                    onclick="handleLike({{ $fatwa->id }})" {{ ($hasLiked ?? false) ? 'disabled' : '' }}
                                    title="{{ ($hasLiked ?? false) ? 'You already liked this!' : 'Like this Fatwa' }}">
                                    <i class="bi bi-heart-fill like-icon me-1"></i>
                                    <span class="like-text">{{ ($hasLiked ?? false) ? 'Liked' : 'Like' }}</span>
                                    <span id="likeCount" class="badge bg-danger ms-1">{{ $fatwa->likes }}</span>
                                </button>
                                <button class="btn btn-outline-secondary px-3 rounded-pill" id="shareBtn"
                                    onclick="shareFatwa()">
                                    <i class="bi bi-share me-1"></i>Share
                                </button>
                            </div>
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

    {{-- Share Fatwa Modal with Short URL --}}
    <div class="modal fade" id="shareFatwaModal" tabindex="-1" aria-labelledby="shareFatwaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(30, 77, 43, 0.1); color: #1e4d2b;">
                            <i class="bi bi-share-fill"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0" id="shareFatwaModalLabel">Share this Fatwa</h6>
                            <span class="text-muted small" style="font-size: 0.8rem;">Short link forwarding enabled</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Short Link Copy Box --}}
                    <label class="form-label small fw-semibold text-secondary mb-1">Short URL</label>
                    <div class="input-group mb-3">
                        <input type="text" id="fatwaShortUrlInput" class="form-control bg-light border-end-0 font-monospace" style="font-size: 0.88rem;" value="{{ route('fatwa.short', $fatwa->id) }}" readonly onclick="this.select()">
                        <button class="btn btn-outline-success border-start-0 px-3" type="button" id="modalCopyBtn" onclick="copyShortUrlFromModal()">
                            <i class="bi bi-clipboard me-1" id="modalCopyIcon"></i><span id="modalCopyText">Copy</span>
                        </button>
                    </div>

                    {{-- Social Share Options --}}
                    <label class="form-label small fw-semibold text-secondary mb-2">Share directly to</label>
                    <div class="d-grid gap-2">
                        <a href="https://api.whatsapp.com/send?text={{ rawurlencode(($fatwa->title ?: 'Islamic Fatwa') . "\n\n" . route('fatwa.short', $fatwa->id)) }}" target="_blank" rel="noopener noreferrer" class="btn btn-success d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 text-white">
                            <i class="bi bi-whatsapp fs-5"></i>
                            <span class="fw-medium">WhatsApp</span>
                        </a>
                        <a href="https://t.me/share/url?url={{ rawurlencode(route('fatwa.short', $fatwa->id)) }}&text={{ rawurlencode($fatwa->title ?: 'Islamic Fatwa') }}" target="_blank" rel="noopener noreferrer" class="btn d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 text-white" style="background-color: #229ED9; border-color: #229ED9;">
                            <i class="bi bi-telegram fs-5"></i>
                            <span class="fw-medium">Telegram</span>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ rawurlencode($fatwa->title ?: 'Islamic Fatwa') }}&url={{ rawurlencode(route('fatwa.short', $fatwa->id)) }}" target="_blank" rel="noopener noreferrer" class="btn btn-dark d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 text-white">
                            <i class="bi bi-twitter-x fs-5"></i>
                            <span class="fw-medium">X (Twitter)</span>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(route('fatwa.short', $fatwa->id)) }}" target="_blank" rel="noopener noreferrer" class="btn d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 text-white" style="background-color: #1877F2; border-color: #1877F2;">
                            <i class="bi bi-facebook fs-5"></i>
                            <span class="fw-medium">Facebook</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function handleLike(id) {
                const btn = document.getElementById('likeBtn');
                const countSpan = document.getElementById('likeCount');
                if (!btn || btn.disabled) return;

                btn.disabled = true;
                btn.classList.add('is-animating');

                fetch(`/fatwa-archive/${id}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'liked' || data.success) {
                            btn.classList.remove('btn-outline-danger');
                            btn.classList.add('btn-danger');
                            btn.innerHTML = '<i class="bi bi-heart-fill like-icon me-1"></i><span class="like-text">Liked</span> <span class="badge bg-white text-danger ms-1" id="likeCount">' + data.likes + '</span>';
                            btn.disabled = true;
                            btn.title = 'You already liked this!';
                        }
                        if (countSpan) {
                            countSpan.innerText = data.likes;
                        }
                        setTimeout(() => {
                            btn.classList.remove('is-animating');
                        }, 700);
                    })
                    .catch(() => {
                        btn.disabled = false;
                        btn.classList.remove('is-animating');
                    });
            }

            const shortFatwaUrl = @json(route('fatwa.short', $fatwa->id));
            const fatwaShareTitle = @json($fatwa->title ?: 'Islamic Fatwa - Darul Ifta Kauzariyya');

            function shareFatwa() {
                const shareData = {
                    title: fatwaShareTitle,
                    text: fatwaShareTitle + "\n\nRead full Fatwa at:\n",
                    url: shortFatwaUrl
                };

                if (navigator.share) {
                    navigator.share(shareData).catch((err) => {
                        if (err.name !== 'AbortError') {
                            openShareModal();
                        }
                    });
                } else {
                    openShareModal();
                }
            }

            function openShareModal() {
                const modalEl = document.getElementById('shareFatwaModal');
                if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                } else {
                    copyShortUrlFallback();
                }
            }

            function copyShortUrlFromModal() {
                const input = document.getElementById('fatwaShortUrlInput');
                if (!input) return;
                navigator.clipboard.writeText(input.value).then(() => {
                    const text = document.getElementById('modalCopyText');
                    const icon = document.getElementById('modalCopyIcon');
                    if (text && icon) {
                        text.innerText = 'Copied!';
                        icon.className = 'bi bi-check2-circle text-success';
                        setTimeout(() => {
                            text.innerText = 'Copy';
                            icon.className = 'bi bi-clipboard';
                        }, 2000);
                    }
                }).catch(() => {
                    input.select();
                    document.execCommand('copy');
                });
            }

            function copyShortUrlFallback() {
                navigator.clipboard.writeText(shortFatwaUrl).then(() => {
                    const btn = document.getElementById('shareBtn');
                    if (!btn) return;
                    const original = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check2-circle me-1 text-success"></i>Link Copied!';
                    btn.classList.add('btn-light', 'border-success');
                    btn.classList.remove('btn-outline-secondary');
                    setTimeout(() => {
                        btn.innerHTML = original;
                        btn.classList.remove('btn-light', 'border-success');
                        btn.classList.add('btn-outline-secondary');
                    }, 2000);
                });
            }
        </script>
    @endpush
@endsection