@extends('layouts.public')

@section('title', $fatwa->question->question_id . ' — Fatwa Detail')

@section('content')
@php
    $langClass = '';
    if($fatwa->question->language == 'Urdu') $langClass = 'gulzar-font ur-text';
    elseif($fatwa->question->language == 'Arabic') $langClass = 'alyamama-font ar-text';
    elseif($fatwa->question->language == 'Hindi') $langClass = 'inter-font';
    elseif($fatwa->question->language == 'Malayalam') $langClass = 'noto-serif-malayalam-font';
    else $langClass = 'inter-font';
@endphp

<style>
    :root {
        --slate-blue: #4A6372;
        --brand-green: #1a7a4a;
        --bg-soft: #f4f7f9;
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
        padding: 30px 25px;
    }
    .app-header h1 {
        font-size: 1.4rem;
        font-weight: 700;
        line-height: 1.5;
        margin: 0;
    }
    .meta-grid {
        background: #fff;
        padding: 20px 25px;
        border-bottom: 1px solid #edf2f7;
    }
    .meta-item {
        margin-bottom: 10px;
    }
    .meta-label {
        font-weight: 700;
        font-size: 0.85rem;
        color: #333;
        width: 80px;
        display: inline-block;
    }
    .meta-value {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--brand-green);
    }
    .content-body {
        padding: 25px;
    }
    .section-label {
        font-weight: 700;
        font-size: 1.1rem;
        color: #000;
        margin-bottom: 15px;
        display: block;
    }
    .body-text {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #333;
    }
    .arabic-block {
        font-family: 'Amiri', 'Alyamama', serif;
        font-size: 1.4rem;
        text-align: center;
        direction: rtl;
        margin: 20px 0;
        color: #000;
    }
    .signature-section {
        padding: 0 25px 25px;
    }
    .signature-item {
        margin-bottom: 15px;
    }
    .sig-label {
        font-weight: 700;
        font-size: 0.95rem;
        display: block;
        color: #000;
    }
    .sig-value {
        color: var(--brand-green);
        font-weight: 600;
        font-size: 0.95rem;
    }
    .references-section {
        background-color: #f9fafb;
        margin: 20px 25px;
        padding: 20px;
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
        font-size: 0.9rem;
        margin-right: 8px;
        text-decoration: none;
    }
    .share-icon {
        color: var(--slate-blue);
        font-size: 1.2rem;
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
    .search-box {
        background: #fff;
        border-radius: 8px;
        padding: 10px 15px;
        display: flex;
        align-items: center;
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
    .related-card:hover { border-left: 3px solid var(--brand-green); transform: translateX(5px); }
</style>

<div class="container py-4">
    <div class="fatwa-container">
        
        {{-- Breadcrumb for desktop --}}
        <nav aria-label="breadcrumb" class="mb-3 d-none d-md-block">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('fatwa.archive') }}" class="text-muted">Archive</a></li>
                <li class="breadcrumb-item active">{{ $fatwa->question->question_id }}</li>
            </ol>
        </nav>

        <div class="app-card mb-4">
            {{-- Header --}}
            <div class="app-header">
                <h1 class="{{ $langClass }}">{{ $fatwa->question->title ?: 'Islamic Query Detail' }}</h1>
            </div>

            {{-- Metadata --}}
            <div class="meta-grid">
                <div class="row g-0">
                    <div class="col-6">
                        <div class="meta-item"><span class="meta-label">Qu-Id:</span> <span class="meta-value">{{ $fatwa->question->question_id }}</span></div>
                        <div class="meta-item"><span class="meta-label">Category:</span> <span class="meta-value">{{ $fatwa->question->category->name }}</span></div>
                    </div>
                    <div class="col-6">
                        <div class="meta-item"><span class="meta-label">Madhab:</span> <span class="meta-value">{{ $fatwa->question->madhab }}</span></div>
                        <div class="meta-item"><span class="meta-label">Published:</span> <span class="meta-value">{{ $fatwa->published_at?->format('d-m-Y') }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Question Content --}}
            <div class="content-body">
                <span class="section-label">Question:</span>
                <div class="body-text {{ $langClass }}">{!! nl2br(e($fatwa->question->body)) !!}</div>
            </div>

            <hr class="mx-4 my-0" style="opacity: 0.1;">

            {{-- Answer Content --}}
            <div class="content-body">
                <span class="section-label">Answer:</span>
                
                <div class="arabic-block">
                    بسم الله الرحمن الرحيم <br>
                    الجواب وبالله التوفيق والنجاح
                </div>

                <div class="body-text {{ $langClass }} mt-3">
                    {!! $fatwa->muftiAnswer->full_answer !!}
                </div>
            </div>

            {{-- Quotes / References --}}
            @if($fatwa->muftiAnswer->supportingReferences->count())
            <div class="references-section">
                @foreach($fatwa->muftiAnswer->supportingReferences as $ref)
                    <div class="mb-4">
                        <div class="arabic-text mb-2 text-dark {{ $ref->language == 'Urdu' ? 'gulzar-font' : 'alyamama-font' }}" style="font-size: 1.2rem; line-height: 1.7;">
                            {{ $ref->arabic_text }}
                        </div>
                        <div class="small text-muted"><i class="bi bi-book me-1"></i>{{ $ref->book_name }}</div>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Closing & Signatures --}}
            <div class="signature-section">
                <div class="arabic-block" style="text-align: right; margin-bottom: 20px;">
                    والله أعلم بالصواب
                </div>

                <div class="signature-item">
                    <span class="sig-label">Written by:</span>
                    <span class="sig-value">{{ $fatwa->muftiAnswer->mufti->name }}</span>
                </div>

                @if($fatwa->verifiedBy)
                <div class="signature-item">
                    <span class="sig-label">Verified by:</span>
                    <span class="sig-value">{{ $fatwa->verifiedBy->name }}</span>
                </div>
                @endif

                @if($fatwa->checkedApprovedBy)
                <div class="signature-item">
                    <span class="sig-label">Checked & Approved by:</span>
                    <span class="sig-value">{{ $fatwa->checkedApprovedBy->name }}</span>
                </div>
                @endif
            </div>

            {{-- Tags & Share --}}
            <div class="tags-footer">
                <div>
                    <span class="fw-bold small me-2">Tags:</span>
                    <a href="#" class="tag-item">#islam</a>
                    <a href="#" class="tag-item">#figh</a>
                    <a href="#" class="tag-item">#hadiya</a>
                    <a href="#" class="tag-item">#salath</a>
                </div>
                <a href="#" class="share-icon"><i class="bi bi-share"></i></a>
            </div>
        </div>

        {{-- Related Questions --}}
        @if(isset($relatedFatwas) && $relatedFatwas->count())
        <div class="related-section">
            <h5 class="related-title">Related Questions</h5>
            
            <div class="search-box no-print">
                <i class="bi bi-search text-muted me-2"></i>
                <input type="text" class="form-control border-0 p-0 shadow-none" placeholder="Search...">
            </div>

            @foreach($relatedFatwas as $rf)
            <a href="{{ route('fatwa.show', $rf) }}" class="related-card">
                <div class="pe-3">
                    <div class="body-text small text-dark mb-0">{{ Str::limit($rf->question->title ?: $rf->question->body, 60) }}</div>
                </div>
                <div class="text-end" style="min-width: 100px;">
                    <span class="fw-bold small" style="color:#666;">(QU ID {{ $rf->id }})</span>
                </div>
            </a>
            @endforeach
        </div>
        @endif

        {{-- Back Button --}}
        <div class="mt-4 text-center no-print">
            <a href="{{ route('fatwa.archive') }}" class="btn btn-link text-muted text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>Back to Archive
            </a>
        </div>
    </div>
</div>
@endsection
