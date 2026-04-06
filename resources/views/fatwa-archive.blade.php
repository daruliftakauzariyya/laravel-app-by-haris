@extends('layouts.public')
@section('title', 'Fatwa Archive')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold text-green"><i class="bi bi-archive me-2"></i>Fatwa Archive</h2>
            <p class="text-muted mb-0">Browse verified Islamic Fatwas</p>
        </div>
        <a href="{{ route('question.create') }}" class="btn btn-primary">
            <i class="bi bi-pencil-square me-2"></i>Ask a Question
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="card border-0 shadow-sm p-3 mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search by keyword or ID..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="madhab" class="form-select">
                    <option value="">All Madhabs</option>
                    @foreach(['Hanafi','Shafii','Maliki','Hanbali','Other'] as $m)
                        <option value="{{ $m }}" {{ request('madhab') == $m ? 'selected' : '' }}>
                            {{ $m == 'Shafii' ? "Shafi'i" : $m }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('fatwa.archive') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    @if($fatwas->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size:3rem;"></i>
            <p class="mt-3">No fatwas found. <a href="{{ route('question.create') }}">Be the first to ask!</a></p>
        </div>
    @else
    <div class="row g-4">
        @foreach($fatwas as $fatwa)
        @php
            $langClass = '';
            if($fatwa->question->language == 'Urdu') $langClass = 'gulzar-font';
            elseif($fatwa->question->language == 'Arabic') $langClass = 'alyamama-font';
            elseif($fatwa->question->language == 'Malayalam') $langClass = 'noto-serif-malayalam-font';
            else $langClass = 'inter-font';
        @endphp
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <span class="badge bg-secondary me-2">{{ $fatwa->question->question_id }}</span>
                            <span class="badge" style="background:#1a7a4a;">{{ $fatwa->question->madhab == 'Shafii' ? "Shafi'i" : $fatwa->question->madhab }}</span>
                        </div>
                        <small class="text-muted"><i class="bi bi-calendar me-1"></i>{{ $fatwa->published_at?->format('d M Y') }}</small>
                    </div>
                    <h5 class="fw-bold mb-2 {{ $langClass }}">
                        {{ $fatwa->question->title ?: Str::limit(strip_tags($fatwa->question->body), 100) }}
                    </h5>
                    <p class="text-muted mb-3 {{ $langClass }}">{{ Str::limit($fatwa->muftiAnswer->summary, 160) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-person-badge me-1"></i>{{ $fatwa->muftiAnswer->mufti->name ?? 'Darul Ifta' }}
                            &nbsp;|&nbsp;<i class="bi bi-eye me-1"></i>{{ $fatwa->views }} views
                        </small>
                        <a href="{{ route('fatwa.show', $fatwa) }}" class="btn btn-outline-success btn-sm">
                            Read Full Fatwa <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $fatwas->links() }}</div>
    @endif
</div>
@endsection
