@extends('layouts.public')

@section('title', $question->title ?: 'Inquiry #'.$question->question_id.' Details — Darul Ifta Kauzariyya')

@section('content')
@php
    $textToCheck = ($question->title ?? '') . ' ' . ($question->body ?? '') . ' ' . ($question->answer ?? '') . ' ' . ($question->draft_answer ?? '');
    $isMalayalam = $question->language == 'Malayalam' || preg_match('/[\x{0D00}-\x{0D7F}]/u', $textToCheck);
    $isArabic = $question->language == 'Arabic' || (!$isMalayalam && preg_match('/[\x{0600}-\x{06FF}]/u', $textToCheck));
    $isUrdu = $question->language == 'Urdu';

    $langClass = 'font-eng';
    if ($isUrdu) {
        $langClass = 'font-urdu';
    } elseif ($isArabic) {
        $langClass = 'font-arabic';
    } elseif ($isMalayalam) {
        $langClass = 'font-malayalam';
    }

    $status = strtolower($question->status ?: 'pending');
    $statusBadgeClass = 'bg-secondary text-white';
    $statusIcon = 'bi-info-circle';

    if ($status === 'published') {
        $statusBadgeClass = 'bg-success text-white';
        $statusIcon = 'bi-check-circle-fill';
    } elseif ($status === 'answered') {
        $statusBadgeClass = 'bg-primary text-white';
        $statusIcon = 'bi-chat-left-dots-fill';
    } elseif ($status === 'assigned') {
        $statusBadgeClass = 'bg-info text-dark';
        $statusIcon = 'bi-person-badge';
    } elseif ($status === 'pending') {
        $statusBadgeClass = 'bg-warning text-dark';
        $statusIcon = 'bi-hourglass-split';
    } elseif ($status === 'rejected') {
        $statusBadgeClass = 'bg-danger text-white';
        $statusIcon = 'bi-x-circle-fill';
    }

    $rawBody = $question->body ?? '';
    $rawAnswer = $question->answer ?: $question->draft_answer;
@endphp

<style>
    .query-detail-canvas {
        background-color: #f8fafc;
        min-height: 85vh;
        padding-top: 1.5rem;
        padding-bottom: 5rem;
    }

    .query-card-main {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(5, 41, 97, 0.04);
    }

    .query-header-box {
        background: linear-gradient(135deg, #052961 0%, #0d3b82 60%, #1e4b9c 100%);
        color: #ffffff;
        padding: 1.75rem 1.5rem;
        position: relative;
    }

    @media (min-width: 768px) {
        .query-header-box {
            padding: 2.25rem 2.5rem;
        }
    }

    .query-meta-bar {
        background: #fdfaf3;
        border-bottom: 1px solid #f1ebd8;
        padding: 1rem 1.5rem;
    }

    @media (min-width: 768px) {
        .query-meta-bar {
            padding: 1.25rem 2.5rem;
        }
    }

    .query-content-pane {
        padding: 1.75rem 1.5rem;
    }

    @media (min-width: 768px) {
        .query-content-pane {
            padding: 2.5rem;
        }
    }

    /* ── Typography & HTML Content Capacity ── */
    .query-text-body {
        font-family: 'Noto Serif Malayalam', 'Literata', 'Inter', serif;
        font-size: 1.12rem;
        line-height: 2.1;
        color: #1e293b;
        word-break: break-word;
    }

    .query-text-body p {
        margin-bottom: 1.15rem;
        line-height: 2.1;
    }

    .query-text-body p:last-child {
        margin-bottom: 0;
    }

    .query-text-body h1,
    .query-text-body h2,
    .query-text-body h3,
    .query-text-body h4,
    .query-text-body h5,
    .query-text-body h6 {
        font-family: 'Noto Serif Malayalam', 'Plus Jakarta Sans', sans-serif;
        font-weight: 800;
        color: #0f172a;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        line-height: 1.5;
    }

    .query-text-body ul,
    .query-text-body ol {
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
    }

    .query-text-body li {
        margin-bottom: 0.5rem;
        line-height: 1.9;
    }

    .query-text-body blockquote {
        background: #f8fafc;
        border-left: 4px solid var(--brand-gold, #c9a84c);
        padding: 1rem 1.25rem;
        border-radius: 0 12px 12px 0;
        margin: 1.25rem 0;
        font-style: italic;
    }

    .query-text-body strong,
    .query-text-body b {
        font-weight: 700;
        color: #0f172a;
    }

    /* Multi-lingual font family registration */
    .font-malayalam,
    .noto-serif-malayalam-font {
        font-family: 'Noto Serif Malayalam', 'Noto Sans Malayalam', 'Manjari', serif !important;
        line-height: 2.15 !important;
    }

    .font-arabic,
    .alyamama-font {
        font-family: 'Noto Naskh Arabic', 'Amiri', 'Scheherazade New', serif !important;
        line-height: 2.3 !important;
        font-size: 1.35rem !important;
        direction: rtl;
        text-align: right;
    }

    .font-urdu,
    .gulzar-font {
        font-family: 'Noto Nastaliq Urdu', 'Gulzar', serif !important;
        line-height: 2.4 !important;
        font-size: 1.25rem !important;
        direction: rtl;
        text-align: right;
    }

    .font-eng,
    .inter-font {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif !important;
        line-height: 1.85 !important;
    }

    .arabic-bismillah {
        font-family: 'Noto Naskh Arabic', 'Amiri', serif;
        font-size: 1.85rem;
        font-weight: 700;
        color: #052961;
        line-height: 2.2;
    }

    .arabic-intro {
        font-family: 'Noto Naskh Arabic', 'Amiri', serif;
        font-size: 1.45rem;
        font-weight: 600;
        color: #475569;
        line-height: 2.0;
    }

    .reference-quote-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #052961;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
    }
</style>

<div class="query-detail-canvas">
    <div class="container">
        
        {{-- Navigation & Breadcrumbs Bar --}}
        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
            <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-white bg-white border shadow-sm rounded-pill px-3 py-1.5 fw-bold text-dark d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i>
                <span>Back to Dashboard</span>
            </a>
            <span class="text-muted small fw-semibold">
                Inquiry Tracking ID: <strong class="text-dark">#{{ $question->question_id }}</strong>
            </span>
        </div>

        {{-- Main Query Card (Full Container Width matching Header/Footer) --}}
        <div class="query-card-main mb-4">
            
            {{-- 1. Header Banner --}}
            <div class="query-header-box">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge px-3 py-1.5 rounded-pill {{ $statusBadgeClass }} fw-bold" style="font-size: 0.75rem;">
                                <i class="bi {{ $statusIcon }} me-1"></i> {{ ucfirst($status) }}
                            </span>
                            <span class="badge bg-white bg-opacity-20 text-white px-3 py-1.5 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                                <i class="bi bi-folder2 me-1"></i> {{ $question->category->name ?? 'General Fiqh' }}
                            </span>
                        </div>
                        <h2 class="fw-800 text-white mb-0 {{ $langClass }}" style="font-size: 1.45rem; line-height: 1.45;">
                            {{ $question->title ?: 'Inquiry #'.$question->question_id }}
                        </h2>
                    </div>

                    <div class="text-md-end text-white-50 small">
                        <div>Submitted: <strong class="text-white">{{ $question->created_at ? $question->created_at->format('d M Y, h:i A') : 'N/A' }}</strong></div>
                        <div>School: <strong class="text-white">{{ $question->madhab?->name ?? 'General Jurisprudence' }}</strong></div>
                    </div>
                </div>
            </div>

            {{-- 2. Metadata Grid --}}
            <div class="query-meta-bar">
                <div class="row g-3 text-dark small">
                    <div class="col-6 col-md-3">
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Question ID</div>
                        <div class="fw-800 text-primary fs-6">#{{ $question->question_id }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Jurisprudence School</div>
                        <div class="fw-bold fs-6">{{ $question->madhab?->name ?? 'General Fiqh' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Assigned Mufti</div>
                        <div class="fw-bold fs-6">{{ $question->assignedMufti->name ?? 'Darul Ifta Faculty' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Fatwa Status</div>
                        <div class="fw-bold fs-6 {{ $status === 'published' ? 'text-success' : 'text-primary' }}">
                            {{ $question->published_at ? 'Published on ' . $question->published_at->format('d M Y') : ucfirst($status) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Inquirer's Question Section (Raw HTML rendering support) --}}
            <div class="query-content-pane border-bottom">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                        <i class="bi bi-person-fill me-1"></i> Inquirer Question
                    </span>
                </div>
                <div class="query-text-body {{ $langClass }}">
                    @if($question->formatted_body)
                        {!! $question->formatted_body !!}
                    @elseif(str_contains($rawBody, '<p') || str_contains($rawBody, '<div') || str_contains($rawBody, '<br'))
                        {!! $rawBody !!}
                    @else
                        {!! nl2br(e($rawBody)) !!}
                    @endif
                </div>
            </div>

            {{-- 4. Scholarly Answer / Fatwa Section (If answered/published) --}}
            @if(($question->answer || $question->draft_answer) && in_array($question->status, ['answered', 'approved', 'published']))
                <div class="query-content-pane bg-light bg-opacity-50">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill fw-bold" style="font-size: 0.75rem;">
                                <i class="bi bi-patch-check-fill me-1"></i> Official Scholarly Ruling (Fatwa)
                            </span>
                            @if($question->status === 'published')
                                @if($question->is_public)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1.5 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                                        <i class="bi bi-globe2 me-1"></i> Public Archive
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-3 py-1.5 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                                        <i class="bi bi-shield-lock-fill me-1"></i> Private (Shareable via Link)
                                    </span>
                                @endif
                            @endif
                        </div>

                        @if($question->status === 'published')
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5"
                                    data-bs-toggle="modal" data-bs-target="#userShareModal">
                                <i class="bi bi-share-fill"></i>
                                <span>Share Fatwa</span>
                            </button>
                            <a href="{{ route('fatwa.show', $question) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center gap-1.5">
                                <i class="bi bi-box-arrow-up-right"></i>
                                <span>View Fatwa Page</span>
                            </a>
                        </div>
                        @endif
                    </div>

                    @if($question->status === 'published' && !$question->is_public)
                    <div class="alert alert-info py-2 px-3 mb-4 rounded-3 border-0 small d-flex align-items-center gap-2" style="background-color: #f0f9ff; color: #0369a1;">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div>
                            <strong>Private Fatwa:</strong> ഈ ഫത്വ പബ്ലിക് ആർക്കൈവിൽ ലിസ്റ്റ് ചെയ്തിട്ടില്ല. നിങ്ങൾ ഷെയർ ബട്ടൺ വഴി നൽകുന്ന ലിങ്ക് വഴി മറ്റുള്ളവർക്ക് (ലോഗിൻ ചെയ്ത ശേഷം) ഇത് വായിക്കാൻ കഴിയും.
                        </div>
                    </div>
                    @endif

                    {{-- Arabic Opening --}}
                    <div class="text-end mb-4" dir="rtl">
                        <div class="arabic-bismillah">بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ</div>
                        <div class="arabic-intro">الْجَوَابُ وَبِاللهِ التَّوْفِيقُ وَالنَّجَاحُ</div>
                    </div>

                    {{-- Fatwa Text Body (Rich HTML Support & Proper Font Family) --}}
                    <div class="query-text-body {{ $langClass }} mb-5">
                        @if($question->formatted_answer)
                            {!! $question->formatted_answer !!}
                        @elseif(str_contains($rawAnswer, '<p') || str_contains($rawAnswer, '<div') || str_contains($rawAnswer, '<br'))
                            {!! $rawAnswer !!}
                        @else
                            {!! nl2br(e($rawAnswer)) !!}
                        @endif
                    </div>

                    {{-- Supporting Scholarly References --}}
                    @php 
                        $refs = $question->references->isNotEmpty() ? $question->references : $question->draftReferences; 
                    @endphp
                    @if($refs && $refs->count())
                        <div class="mb-4">
                            <h6 class="fw-800 text-dark mb-3"><i class="bi bi-book-half text-primary me-2"></i>Scholarly References & Texts</h6>
                            @foreach($refs as $ref)
                                <div class="reference-quote-card">
                                    <div class="text-end text-dark mb-2" dir="rtl" style="font-family: 'Noto Naskh Arabic', serif; font-size: 1.25rem; line-height: 2.0;">
                                        {{ $ref->arabic_text }}
                                    </div>
                                    <div class="small text-muted fw-bold">
                                        <i class="bi bi-bookmark-check-fill text-primary me-1"></i> {{ $ref->book_name }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Signature & Author Attributions --}}
                    <div class="pt-4 border-top">
                        <div class="text-end mb-3 fw-bold" dir="rtl" style="font-family: 'Noto Naskh Arabic', serif; font-size: 1.4rem; color: #052961;">
                            وَاللهُ أَعْلَمُ بِالصَّوَابِ
                        </div>
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 small text-muted">
                            <div>
                                Written by: <strong class="text-dark">{{ $question->assignedMufti->name ?? 'Darul Ifta Faculty' }}</strong>
                                @if($question->publisher)
                                    • Approved by: <strong class="text-dark">{{ $question->publisher->name }}</strong>
                                @endif
                            </div>
                            <div>
                                <span class="badge bg-white text-dark border px-2.5 py-1 rounded-pill">
                                    Darul Ifta Kauzariyya Archive
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($status === 'pending' || $status === 'assigned')
                <div class="query-content-pane bg-white text-center py-5">
                    <div class="d-inline-flex p-3 rounded-circle bg-warning bg-opacity-10 text-warning mb-3">
                        <i class="bi bi-hourglass-split fs-2"></i>
                    </div>
                    <h5 class="fw-800 text-dark mb-1">Ruling Under Scholarly Review</h5>
                    <p class="text-muted small mb-0" style="max-width: 480px; margin: 0 auto;">
                        Your inquiry has been received and is currently being studied by our qualified Muftis according to authentic Quran and Sunnah sources. You will be notified once the ruling is issued.
                    </p>
                </div>
            @endif

        </div>

    </div>
</div>

{{-- Share Modal for Questioner --}}
@if($question->status === 'published')
<div class="modal fade" id="userShareModal" tabindex="-1" aria-labelledby="userShareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(30, 77, 43, 0.1); color: #1e4d2b;">
                        <i class="bi bi-share-fill"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0" id="userShareModalLabel">Share this Fatwa</h6>
                        <span class="text-muted small" style="font-size: 0.8rem;">Direct forwarding link</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if(!$question->is_public)
                    <div class="alert alert-warning py-2 px-3 mb-3 border-0 rounded-3 small">
                        <i class="bi bi-shield-lock-fill me-1"></i><strong>Private Fatwa:</strong> ഈ ലിങ്ക് ലഭിച്ചവർക്ക് ഫത്വ വായിക്കാൻ ലോഗിൻ ചെയ്യേണ്ടതുണ്ട് (Recipients must log in to read this fatwa).
                    </div>
                @endif

                {{-- Short Link Copy Box --}}
                <label class="form-label small fw-semibold text-secondary mb-1">Fatwa Link</label>
                <div class="input-group mb-3">
                    <input type="text" id="userFatwaUrlInput" class="form-control bg-light border-end-0 font-monospace" style="font-size: 0.88rem;" value="{{ route('fatwa.short', $question->id) }}" readonly onclick="this.select()">
                    <button class="btn btn-outline-success border-start-0 px-3" type="button" onclick="copyUserFatwaUrl()">
                        <i class="bi bi-clipboard me-1" id="userCopyIcon"></i><span id="userCopyText">Copy</span>
                    </button>
                </div>

                {{-- Social Share Options --}}
                <label class="form-label small fw-semibold text-secondary mb-2">Share directly to</label>
                <div class="d-grid gap-2">
                    <a href="https://api.whatsapp.com/send?text={{ rawurlencode(($question->title ?: 'Islamic Fatwa') . "\n\n" . route('fatwa.short', $question->id)) }}" target="_blank" rel="noopener noreferrer" class="btn btn-success d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 text-white">
                        <i class="bi bi-whatsapp fs-5"></i>
                        <span class="fw-medium">WhatsApp</span>
                    </a>
                    <a href="https://t.me/share/url?url={{ rawurlencode(route('fatwa.short', $question->id)) }}&text={{ rawurlencode($question->title ?: 'Islamic Fatwa') }}" target="_blank" rel="noopener noreferrer" class="btn d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 text-white" style="background-color: #229ED9; border-color: #229ED9;">
                        <i class="bi bi-telegram fs-5"></i>
                        <span class="fw-medium">Telegram</span>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ rawurlencode($question->title ?: 'Islamic Fatwa') }}&url={{ rawurlencode(route('fatwa.short', $question->id)) }}" target="_blank" rel="noopener noreferrer" class="btn btn-dark d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 text-white">
                        <i class="bi bi-twitter-x fs-5"></i>
                        <span class="fw-medium">X (Twitter)</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyUserFatwaUrl() {
        const input = document.getElementById('userFatwaUrlInput');
        if (!input) return;
        navigator.clipboard.writeText(input.value).then(() => {
            const text = document.getElementById('userCopyText');
            const icon = document.getElementById('userCopyIcon');
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
</script>
@endpush
@endif

@endsection
