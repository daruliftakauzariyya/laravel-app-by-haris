@extends('layouts.public')
@section('title', $fatwa->question->question_id . ' — Fatwa Detail')
@section('content')
@php
    $langClass = '';
    if($fatwa->question->language == 'Urdu') $langClass = 'gulzar-font ur-text';
    elseif($fatwa->question->language == 'Arabic') $langClass = 'alyamama-font ar-text';
    elseif($fatwa->question->language == 'Hindi') $langClass = 'inter-font';
    elseif($fatwa->question->language == 'Malayalam') $langClass = 'noto-serif-malayalam-font ml-text';
    else $langClass = 'inter-font';
@endphp

<div class="container py-5" style="max-width:860px;">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4 no-print">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-green">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('fatwa.archive') }}" class="text-green">Fatwa Archive</a></li>
            <li class="breadcrumb-item active">{{ $fatwa->question->question_id }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h2 class="fw-bold mb-0 text-green"><i class="bi bi-file-earmark-check me-2"></i>Fatwa Detail</h2>
        <button onclick="window.print()" class="btn btn-gold px-4">
            <i class="bi bi-printer me-2"></i>Print Fatwa
        </button>
    </div>

    {{-- Print Only Header --}}
    <div class="d-none d-print-block text-center mb-5 border-bottom pb-4">
        <h1 class="fw-bold text-green">Darul Ifta Kauzariyya</h1>
        <p class="mb-1">Authentic Islamic Guidance</p>
        <p class="small text-muted mb-0">Website: daruliftaportal.com | Email: darulifta.kauzariyya@gmail.com</p>
    </div>

    {{-- Question Card --}}
    <div class="card border-0 shadow mb-4">
        <div class="card-header py-3" style="background:linear-gradient(135deg,#1a7a4a,#145e38); color:#fff;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 fw-bold"><i class="bi bi-question-circle me-2"></i>Question</h5>
                <div class="d-flex gap-2">
                    <span class="badge bg-warning text-dark">{{ $fatwa->question->question_id }}</span>
                    <span class="badge bg-light text-dark">{{ $fatwa->question->madhab == 'Shafii' ? "Shafi'i" : $fatwa->question->madhab }}</span>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @if($fatwa->question->title)
                <h4 class="fw-bold mb-3 {{ $langClass }}">{{ $fatwa->question->title }}</h4>
            @endif
            <div class="{{ $langClass }}" style="line-height:1.8;">{!! $fatwa->question->body !!}</div>
        </div>
    </div>

    {{-- Answer Card --}}
    <div class="card border-0 shadow mb-4">
        <div class="card-header py-3" style="background:linear-gradient(135deg,#c9a84c,#a8873a); color:#fff;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 fw-bold"><i class="bi bi-patch-check me-2"></i>Fatwa Answer</h5>
                <span class="badge bg-white text-dark">
                    <i class="bi bi-person-badge me-1"></i>{{ $fatwa->muftiAnswer->mufti->name ?? 'Darul Ifta' }}
                </span>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="alert alert-success-subtle border-0 p-3 mb-4" style="background:#f0faf4; border-left:4px solid #1a7a4a !important; border-left-width:4px;">
                <strong>Summary:</strong> {{ $fatwa->muftiAnswer->summary }}
            </div>
            <div class="{{ $langClass }}" style="line-height:1.9;">{!! $fatwa->muftiAnswer->full_answer !!}</div>
        </div>
    </div>

    {{-- Supporting References --}}
    @if($fatwa->muftiAnswer->supportingReferences->count())
    <div class="card border-0 shadow mb-4 page-break-before">
        <div class="card-header py-3 bg-light">
            <h5 class="mb-0 fw-bold text-green"><i class="bi bi-book me-2"></i>Supporting Islamic References</h5>
        </div>
        <div class="card-body p-4">
            @foreach($fatwa->muftiAnswer->supportingReferences as $ref)
            <div class="mb-4 p-3 rounded" style="background:#fafafa; border: 1px solid #e8e8e8;">
                <div class="arabic-text mb-2 text-green {{ $ref->language == 'Urdu' ? 'gulzar-font' : 'alyamama-font' }}">{{ $ref->arabic_text }}</div>
                <div class="d-flex align-items-center gap-2 no-print">
                    <i class="bi bi-journal-text text-muted"></i>
                    <strong class="small">{{ $ref->book_name }}</strong>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle small">{{ $ref->language }}</span>
                </div>
                <div class="d-none d-print-block mt-1 small text-muted">Ref: {{ $ref->book_name }} ({{ $ref->language }})</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Meta --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="row text-center g-3 text-muted small">
                <div class="col-md-3"><i class="bi bi-calendar me-1"></i>Published: <strong>{{ $fatwa->published_at?->format('d M Y') }}</strong></div>
                <div class="col-md-3"><i class="bi bi-person me-1"></i>By: <strong>{{ $fatwa->muftiAnswer->mufti->name ?? 'Darul Ifta' }}</strong></div>
                <div class="col-md-3"><i class="bi bi-eye me-1 no-print"></i><span class="no-print">Views:</span> <strong>{{ $fatwa->views }}</strong></div>
                <div class="col-md-3"><i class="bi bi-tag me-1"></i>Madhab: <strong>{{ $fatwa->question->madhab == 'Shafii' ? "Shafi'i" : $fatwa->question->madhab }}</strong></div>
            </div>
        </div>
    </div>

    <div class="mt-4 no-print">
        <a href="{{ route('fatwa.archive') }}" class="btn btn-outline-secondary me-2"><i class="bi bi-arrow-left me-1"></i>Back to Archive</a>
        <a href="{{ route('question.create') }}" class="btn btn-primary">Ask Your Question</a>
    </div>
</div>
@endsection
