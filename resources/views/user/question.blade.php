@extends('layouts.public')

@section('title', 'Query Details')

@section('content')
@php
    $textToCheck = ($question->title ?? '') . ' ' . ($question->body ?? '') . ' ' . ($question->answer ?? '') . ' ' . ($question->draft_answer ?? '');
    $isMalayalam = $question->language == 'Malayalam' || preg_match('/[\x{0D00}-\x{0D7F}]/u', $textToCheck);
    $isArabic = $question->language == 'Arabic' || (!$isMalayalam && preg_match('/[\x{0600}-\x{06FF}]/u', $textToCheck));
    $isUrdu = $question->language == 'Urdu';

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
        --pending: #ffc107;
        --published: #198754;
        --rejected: #dc3545;
    }
    .fatwa-container {
        max-width: 800px;
        margin: 0 auto;
    }
    .app-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: none;
    }
    .app-header {
        background-color: var(--slate-blue);
        color: #fff;
        padding: 25px;
        position: relative;
    }
    .status-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 0.75rem;
        padding: 5px 12px;
        border-radius: 50px;
        font-weight: 700;
        text-transform: uppercase;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .app-header h1 {
        font-size: 1.3rem;
        font-weight: 700;
        line-height: 1.5;
        margin: 0;
        padding-right: 120px;
    }
    .meta-grid {
        background: #fff;
        padding: 20px 25px;
        border-bottom: 1px solid #edf2f7;
    }
    .meta-item {
        margin-bottom: 8px;
    }
    .meta-label {
        font-weight: 700;
        font-size: 0.82rem;
        color: #555;
        width: 75px;
        display: inline-block;
    }
    .meta-value {
        font-weight: 600;
        font-size: 0.82rem;
        color: var(--brand-green);
    }
    .content-body {
        padding: 25px;
    }
    .section-label {
        font-weight: 700;
        font-size: 1.25rem;
        color: #000;
        margin-bottom: 16px;
        display: block;
    }
    .body-text {
        font-size: 1.18rem;
        line-height: 2.0;
        color: #333;
    }
    .body-text.noto-serif-malayalam-font,
    .body-text.noto-serif-malayalam-font *,
    .body-text.ml-text,
    .body-text.ml-text *,
    .body-text.font-malayalam,
    .body-text.font-malayalam * {
        font-family: 'Noto Serif Malayalam', serif !important;
        font-size: 1.18rem !important;
        line-height: 2.1 !important;
    }
    .body-text.ur-text,
    .body-text.ur-text *,
    .body-text.font-urdu,
    .body-text.font-urdu * {
        font-family: 'Noto Nastaliq Urdu', serif !important;
        font-size: 1.25rem !important;
        direction: rtl;
        text-align: right;
        line-height: 2.4 !important;
    }
    .body-text.ar-text,
    .body-text.ar-text *,
    .body-text.font-arabic,
    .body-text.font-arabic * {
        font-family: 'Noto Naskh Arabic', 'Amiri', serif !important;
        font-size: 1.28rem !important;
        direction: rtl;
        text-align: right;
        line-height: 2.2 !important;
    }
    .body-text.inter-font,
    .body-text.inter-font * {
        font-family: 'Google Sans Eng', 'Inter', sans-serif !important;
        font-size: 1.15rem !important;
        line-height: 1.95 !important;
    }
    .arabic-block {
        font-family: 'Noto Naskh Arabic', 'Amiri', serif !important;
        font-size: 1.65rem;
        text-align: right;
        direction: rtl;
        margin: 22px 0;
        color: #000;
        line-height: 2.2;
    }
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
    .signature-section {
        padding: 0 25px 25px;
    }
    .signature-item {
        margin-bottom: 12px;
    }
    .sig-label {
        font-weight: 700;
        font-size: 0.9rem;
        display: block;
        color: #000;
    }
    .sig-value {
        color: var(--brand-green);
        font-weight: 600;
        font-size: 0.9rem;
    }
    .references-section {
        background-color: #f9fafb;
        margin: 15px 25px;
        padding: 18px;
        border-radius: 8px;
        border-right: 4px solid var(--brand-green);
    }
    .tags-footer {
        padding: 15px 25px;
        border-top: 1px solid #edf2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .tag-item {
        color: #666;
        font-size: 0.85rem;
        margin-right: 8px;
        text-decoration: none;
    }
    .share-icon {
        color: var(--slate-blue);
        font-size: 1.1rem;
    }
    .related-section {
        background-color: #eef2f5;
        border-radius: 20px 20px 0 0;
        margin-top: 30px;
        padding: 25px;
    }
    .related-title {
        font-weight: 700;
        color: var(--slate-blue);
        margin-bottom: 20px;
    }
    .related-card {
        background: #fff;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s;
    }
</style>

<div class="container py-4">
    <div class="fatwa-container">
        
        {{-- Back Button --}}
        <div class="mb-3">
            <a href="{{ route('user.dashboard') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="app-card mb-4">
            {{-- Header --}}
            <div class="app-header">
                {{-- Status Badge --}}
                @php
                    $statusColor = 'bg-secondary';
                    if($question->status == 'pending') $statusColor = 'bg-warning text-dark';
                    elseif($question->status == 'published') $statusColor = 'bg-success';
                    elseif($question->status == 'answered') $statusColor = 'bg-info';
                    elseif($question->status == 'rejected') $statusColor = 'bg-danger';
                @endphp
                <span class="status-badge {{ $statusColor }}">
                    {{ ucfirst($question->status) }}
                </span>

                <h1 class="{{ $langClass }}">{{ $question->title ?: 'Your Query Detail' }}</h1>
            </div>

            {{-- Metadata --}}
            <div class="meta-grid">
                {{-- Row 1: Qu-Id & Madhab --}}
                <div class="row g-0 mb-2">
                    <div class="col-6">
                        <div class="meta-item"><span class="meta-label text-dark">Qu-Id:</span> <span class="meta-value" style="color: #1a7a4a;">{{ $question->question_id }}</span></div>
                    </div>
                    <div class="col-6">
                        <div class="meta-item"><span class="meta-label text-dark">Madhab:</span> <span class="meta-value" style="color: #1a7a4a;">{{ $question->madhab?->name == 'Shafii' ? "Shafi'i" : ($question->madhab?->name ?? 'General/Other') }}</span></div>
                    </div>
                </div>
                {{-- Row 2: Category & Published --}}
                <div class="row g-0">
                    <div class="col-6">
                        <div class="meta-item"><span class="meta-label text-dark">Category:</span> <span class="meta-value" style="color: #1a7a4a;">{{ $question->category->name ?? 'Pending' }}</span></div>
                    </div>
                    <div class="col-6">
                        <div class="meta-item"><span class="meta-label text-dark">Published:</span> <span class="meta-value" style="color: #1a7a4a;">{{ $question->published_at?->format('d-m-Y') ?: 'Pending' }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Question Content --}}
            <div class="content-body">
                <span class="section-label">Question:</span>
                <div class="body-text {{ $langClass }}">{!! $question->formatted_body !!}</div>
            </div>

            @if(($question->answer || $question->draft_answer) && in_array($question->status, ['answered', 'approved', 'published']))
            <hr class="mx-4 my-2" style="opacity: 0.1;">

            {{-- Answer Content --}}
            <div class="content-body">
                <span class="section-label">Answer:</span>
                
                <div class="arabic-block text-end" dir="rtl" style="margin: 15px 0 25px 0;">
                    <div style="font-size: 1.85rem; font-weight: 700; line-height: 2.1;">بسم الله الرحمن الرحيم</div>
                    <div style="font-size: 1.55rem; font-weight: 600; line-height: 2.1; color: #374151;">الجواب وبالله التوفيق والنجاح</div>
                </div>

                <div class="body-text {{ $langClass }} mt-3">
                    {!! $question->formatted_answer !!}
                </div>
            </div>

            {{-- Quotes / References --}}
            @php 
                $refs = $question->references->isNotEmpty() ? $question->references : $question->draftReferences; 
            @endphp
            @if($refs->count())
            <div class="references-section px-4 pb-2">
                <h6 class="fw-bold small mb-3 text-muted">Supporting References</h6>
                @foreach($refs as $ref)
                    <div class="p-3 mb-3 bg-light rounded border-start border-4 border-success">
                        <div class="arabic-text mb-2 text-dark {{ $ref->language == 'Urdu' ? 'gulzar-font font-urdu' : 'alyamama-font font-arabic' }}" style="font-size: 1.35rem; line-height: 2.2;">
                            {{ $ref->arabic_text }}
                        </div>
                        <div class="small text-muted fw-semibold"><i class="bi bi-book me-1 text-success"></i>{{ $ref->book_name }}</div>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Closing & Signatures --}}
            <div class="signature-section">
                <div class="arabic-block" style="text-align: right; margin-bottom: 25px; border-top: 1px solid #f0f0f0; padding-top: 20px; font-size: 1.7rem; font-weight: 700;">
                    والله أعلم بالصواب
                </div>

                <div class="signature-item">
                    <span class="sig-label text-dark">Written by:</span>
                    <span class="sig-value" style="color: #1a7a4a;">{{ $question->assignedMufti->name ?? 'Darul Ifta' }}</span>
                </div>

                @if($question->publisher)
                <div class="signature-item">
                    <span class="sig-label text-dark">Published & Approved by:</span>
                    <span class="sig-value" style="color: #1a7a4a;">{{ $question->publisher->name }}</span>
                </div>
                @endif
            </div>

            {{-- Tags & Share --}}
            <div class="tags-footer">
                <div>
                    <span class="fw-bold small me-2">Tags:</span>
                    <span class="tag-item">#islam</span>
                    <span class="tag-item">#figh</span>
                    <span class="tag-item">#hadiya</span>
                    <span class="tag-item">#salath</span>
                </div>
                <a href="#" class="share-icon"><i class="bi bi-share"></i></a>
            </div>
            @endif
        </div>

        {{-- Related Questions --}}
        @if(isset($relatedQuestions) && $relatedQuestions->count())
        <div class="related-section">
            <h5 class="related-title">Related Archive Queries</h5>
            @foreach($relatedQuestions as $rq)
            <a href="{{ route('fatwa.show', $rq->id) }}" class="related-card border-0 shadow-sm">
                <div class="pe-3">
                    <div class="body-text small text-dark mb-0">{{ Str::limit($rq->title ?: $rq->body, 60) }}</div>
                </div>
                <div class="text-end" style="min-width: 100px;">
                    <span class="fw-bold small" style="color:#666;">(QU ID {{ $rq->id }})</span>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
