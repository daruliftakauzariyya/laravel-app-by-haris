@extends('layouts.public')

@section('title', 'Home')

@section('content')

{{-- Hero Section --}}
<section style="background: linear-gradient(135deg, #1a7a4a 0%, #145e38 60%, #0d3d24 100%); color:#fff; padding: 80px 0;">
    <div class="container text-center">
        <p class="mb-2" style="color:#c9a84c; letter-spacing:3px; font-size:.85rem; font-weight:600;">بسم الله الرحمن الرحيم</p>
        <h1 class="display-4 fw-bold mb-3">Darul Ifta Kauzariyya</h1>
        <p class="lead mb-4" style="color:rgba(255,255,255,.85); max-width:600px; margin: 0 auto;">
            Ask your Islamic questions and receive authentic Fatwa answers from qualified Muftis based on Quran and Sunnah.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('question.create') }}" class="btn btn-warning btn-lg px-4 fw-bold">
                <i class="bi bi-pencil-square me-2"></i>Ask a Question
            </a>
            <a href="{{ route('fatwa.archive') }}" class="btn btn-outline-light btn-lg px-4">
                <i class="bi bi-archive me-2"></i>Browse Fatwas
            </a>
        </div>
    </div>
</section>

{{-- Stats Section --}}
<section class="py-5">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-4 h-100">
                    <div style="font-size:2.5rem; color:#1a7a4a;"><i class="bi bi-question-circle"></i></div>
                    <h2 class="fw-bold text-green mt-2">{{ number_format($stats['total']) }}</h2>
                    <p class="text-muted mb-0 small">Total Questions</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-4 h-100">
                    <div style="font-size:2.5rem; color:#c9a84c;"><i class="bi bi-patch-check"></i></div>
                    <h2 class="fw-bold mt-2" style="color:#c9a84c;">{{ number_format($stats['answered']) }}</h2>
                    <p class="text-muted mb-0 small">Answered</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center p-4 h-100">
                    <div style="font-size:2.5rem; color:#0d6efd;"><i class="bi bi-book"></i></div>
                    <h2 class="fw-bold mt-2" style="color:#0d6efd;">{{ number_format($stats['published']) }}</h2>
                    <p class="text-muted mb-0 small">Published Fatwas</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Latest Fatwas --}}
@if($latestFatwas->count())
<section class="py-4 bg-white">
    <div class="container">
        <h2 class="fw-bold mb-4 text-green"><i class="bi bi-journal-bookmark me-2"></i>Latest Fatwas</h2>
        <div class="row g-4">
            @foreach($latestFatwas as $fatwa)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 hover-shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $fatwa->question->madhab }}</span>
                            <small class="text-muted">{{ $fatwa->published_at?->format('d M Y') }}</small>
                        </div>
                        <h6 class="fw-bold mb-2">{{ Str::limit($fatwa->question->title ?: $fatwa->question->body, 80) }}</h6>
                        <p class="small text-muted mb-3">{{ Str::limit($fatwa->muftiAnswer->summary, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $fatwa->muftiAnswer->mufti->name ?? 'Darul Ifta' }}</small>
                            <a href="{{ route('fatwa.show', $fatwa) }}" class="btn btn-outline-success btn-sm">Read <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('fatwa.archive') }}" class="btn btn-outline-success px-5">View All Fatwas</a>
        </div>
    </div>
</section>
@endif

{{-- How It Works --}}
<section class="py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5 text-green">How It Works</h2>
        <div class="row g-4 text-center">
            @foreach([
                ['bi-person-plus', '1', 'Register', 'Create an account and complete your profile.'],
                ['bi-pencil-square', '2', 'Submit Question', 'Ask your Islamic question with details and attach files if needed.'],
                ['bi-person-badge', '3', 'Mufti Reviews', 'A qualified Mufti answers with Quranic and Hadith references.'],
                ['bi-check-circle', '4', 'Fatwa Published', 'Admin verifies and publishes the authenticated Fatwa.'],
            ] as [$icon, $step, $title, $desc])
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width:60px;height:60px;background:linear-gradient(135deg,#1a7a4a,#c9a84c); color:#fff; font-size:1.5rem;">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <span class="badge bg-secondary mb-2 mx-auto">Step {{ $step }}</span>
                    <h6 class="fw-bold">{{ $title }}</h6>
                    <p class="small text-muted mb-0">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
