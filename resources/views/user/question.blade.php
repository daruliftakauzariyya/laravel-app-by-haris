@extends('layouts.public')

@section('title', 'Query Details')

@section('content')
@php
    $langClass = '';
    if($question->language == 'Urdu') $langClass = 'gulzar-font ur-text';
    elseif($question->language == 'Arabic') $langClass = 'alyamama-font ar-text';
    elseif($question->language == 'Hindi') $langClass = 'inter-font';
    elseif($question->language == 'Malayalam') $langClass = 'noto-serif-malayalam-font';
    else $langClass = 'inter-font';
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
                <div class="row g-0">
                    <div class="col-6">
                        <div class="meta-item"><span class="meta-label">Qu-Id:</span> <span class="meta-value">{{ $question->question_id }}</span></div>
                        <div class="meta-item"><span class="meta-label">Category:</span> <span class="meta-value">{{ $question->category->name }}</span></div>
                    </div>
                    <div class="col-6">
                        <div class="meta-item"><span class="meta-label">Madhab:</span> <span class="meta-value">{{ $question->madhab }}</span></div>
                        <div class="meta-item"><span class="meta-label">Published:</span> <span class="meta-value">{{ $question->fatwa?->published_at?->format('d-m-Y') ?: 'Pending' }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Question Content --}}
            <div class="content-body">
                <span class="section-label">Question:</span>
                <div class="body-text {{ $langClass }}">{!! nl2br(e($question->body)) !!}</div>
            </div>

            @if($question->muftiAnswer && in_array($question->status, ['answered', 'approved', 'published']))
            <hr class="mx-4 my-2" style="opacity: 0.1;">

            {{-- Answer Content --}}
            <div class="content-body">
                <span class="section-label">Answer:</span>
                
                <div class="arabic-block">
                    بسم الله الرحمن الرحيم <br>
                    الجواب وبالله التوفيق والنجاح
                </div>

                <div class="body-text {{ $langClass }} mt-3">
                    {!! $question->muftiAnswer->full_answer !!}
                </div>
            </div>

            {{-- Quotes / References --}}
            @if($question->muftiAnswer->supportingReferences->count())
            <div class="references-section">
                <h6 class="fw-bold small mb-3 text-muted">Supporting References</h6>
                @foreach($question->muftiAnswer->supportingReferences as $ref)
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
                    <span class="sig-value">{{ $question->muftiAnswer->mufti->name }}</span>
                </div>

                @if($question->fatwa && $question->fatwa->verifiedBy)
                <div class="signature-item">
                    <span class="sig-label">Verified by:</span>
                    <span class="sig-value">{{ $question->fatwa->verifiedBy->name }}</span>
                </div>
                @endif

                @if($question->fatwa && $question->fatwa->checkedApprovedBy)
                <div class="signature-item">
                    <span class="sig-label">Checked & Approved by:</span>
                    <span class="sig-value">{{ $question->fatwa->checkedApprovedBy->name }}</span>
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
            <a href="{{ route('fatwa.show', $rq->fatwa) }}" class="related-card border-0 shadow-sm">
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
