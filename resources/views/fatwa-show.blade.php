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
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
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
    .editorial-meta b { color: #2d3748; }

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
    .meta-unit { display: flex; align-items: center; gap: 8px; }
    .meta-unit .label { font-weight: 700; font-size: 0.8rem; color: #4a5568; text-transform: uppercase; letter-spacing: 0.5px; }
    .meta-unit .value { font-weight: 600; font-size: 0.85rem; color: var(--brand-green); }

    .body-content { padding: 30px 25px; }
    .content-block { margin-bottom: 35px; }
    .content-block:last-child { margin-bottom: 0; }
    .block-h { font-weight: 800; font-size: 1.15rem; color: #000; margin-bottom: 15px; display: block; border-left: 4px solid var(--brand-green); padding-left: 12px; }
    .block-text { font-size: 1.05rem; line-height: 1.85; color: #2d3748; }
    .block-text p { margin-bottom: 1rem; }

    .arabic-quote {
        font-family: 'Amiri', 'Alyamama', serif;
        font-size: 1.5rem;
        text-align: center;
        direction: rtl;
        margin: 25px 0;
        color: #1a202c;
        line-height: 2;
    }

    /* Sidebar Styles */
    .sidebar-widget {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
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
        gap: 15px;
        margin-bottom: 20px;
        text-decoration: none;
        color: inherit;
        transition: 0.2s;
    }
    .recent-item:hover { opacity: 0.8; }
    .recent-thumb {
        width: 120px; /* Adjusted to look good in sidebar, we'll keep the ratio */
        height: 72px;
        border-radius: 6px;
        overflow: hidden;
        flex-shrink: 0;
        background: #f7fafc;
    }
    .recent-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .recent-info h6 { font-size: 0.9rem; font-weight: 700; line-height: 1.4; margin: 0; color: #2d3748; }
    .recent-info .date { font-size: 0.75rem; color: #a0aec0; margin-top: 5px; display: block; }

    .cat-list { list-style: none; padding: 0; margin: 0; }
    .cat-list li { margin-bottom: 12px; }
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
    .cat-list a:hover { color: var(--brand-green); }
    .cat-count { background: #edf2f7; padding: 2px 10px; border-radius: 20px; font-size: 0.75rem; color: #718096; }

    /* Signatures Section */
    .signature-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 25px;
        margin-top: 40px;
        border: 1px solid #e2e8f0;
    }
    .sig-row { margin-bottom: 12px; display: flex; gap: 8px; font-size: 0.95rem; }
    .sig-row b { color: #1a202c; }
    .sig-row span { color: var(--brand-green); font-weight: 700; }

    .ref-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-left: 4px solid var(--brand-green);
        padding: 15px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 15px;
    }
</style>

<div class="main-wrapper py-5">
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
                <div class="editorial-meta">
                    By <b><a href="{{ route('fatwa.mufti.archive', $fatwa->muftiAnswer->mufti) }}" class="text-decoration-none text-dark hover-green">{{ $fatwa->muftiAnswer->mufti->name }}</a></b> in <b>{{ $fatwa->question->category->name }}</b>
                </div>

                {{-- 3. Header Section --}}
                <div class="header-section">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h1 class="{{ $langClass }} mb-0">{{ $fatwa->question->title ?: 'Islamic Query Detail' }}</h1>
                        <div class="text-warning h5 mb-0">
                            @for($i=0; $i<($fatwa->question->category->stars ?? 5); $i++)
                                <i class="bi bi-star-fill"></i>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="meta-row">
                        <div class="meta-unit">
                            <span class="label">Qu-Id:</span>
                            <span class="value">{{ $fatwa->question->question_id }}</span>
                        </div>
                        <div class="meta-unit">
                            <span class="label">Madhab:</span>
                            <span class="value">{{ $fatwa->question->madhab }}</span>
                        </div>
                        <div class="meta-unit">
                            <span class="label">Date:</span>
                            <span class="value">{{ $fatwa->published_at?->format('d-m-Y') }}</span>
                        </div>
                        <div class="meta-unit ms-auto no-print">
                            <a href="javascript:window.print()" class="text-muted small"><i class="bi bi-printer me-1"></i>Print</a>
                        </div>
                    </div>
                </div>

                {{-- 4. Content Body --}}
                <div class="body-content">
                    {{-- Question --}}
                    <div class="content-block">
                        <span class="block-h">Question:</span>
                        <div class="block-text {{ $langClass }}">
                            {!! $fatwa->question->body !!}
                        </div>
                    </div>

                    <hr class="my-5" style="opacity: 0.1;">

                    {{-- Answer --}}\n                    <div class="content-block">
                        <span class="block-h">Answer:</span>
                        
                        <div class="arabic-quote" style="text-align: right; direction: rtl;">
                            بسم الله الرحمن الرحيم <br>
                            الجواب وبالله التوفيق والنجاح
                        </div>

                        <div class="block-text {{ $langClass }}">
                            {!! $fatwa->muftiAnswer->full_answer !!}
                        </div>

                        {{-- Supporting References — BEFORE closing phrase --}}
                        @if($fatwa->muftiAnswer->supportingReferences->count())
                        <div class="mt-4">
                            <span class="block-h">References:</span>
                            @foreach($fatwa->muftiAnswer->supportingReferences as $ref)
                                <div class="ref-card">
                                    <div class="arabic-text mb-2 {{ $ref->language == 'Urdu' ? 'gulzar-font' : 'alyamama-font' }}" style="font-size: 1.25rem; line-height: 1.8; color: #000;">
                                        {{ $ref->arabic_text }}
                                    </div>
                                    <div class="small text-muted fw-bold"><i class="bi bi-book me-1"></i>{{ $ref->book_name }}</div>
                                </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="arabic-quote" style="text-align: right; direction: rtl; border-top: 1px solid #f1f5f9; padding-top: 15px; margin-top: 25px;">
                            والله أعلم بالصواب
                        </div>

                        <div class="mt-3 no-print d-flex align-items-center gap-2">
                            <button id="likeBtn"
                                class="btn {{ $hasLiked ? 'btn-danger' : 'btn-outline-danger' }} px-3 rounded-pill"
                                onclick="handleLike({{ $fatwa->id }})"
                                {{ $hasLiked ? 'disabled' : '' }}
                                title="{{ $hasLiked ? 'You already liked this!' : 'Like this Fatwa' }}">
                                <i class="bi bi-heart-fill me-1"></i>{{ $hasLiked ? 'Liked' : 'Like' }} <span id="likeCount" class="badge bg-danger ms-1">{{ $fatwa->likes }}</span>
                            </button>
                            <button class="btn btn-outline-secondary px-3 rounded-pill" id="shareBtn" onclick="shareFatwa()">
                                <i class="bi bi-share me-1"></i>Share
                            </button>
                        </div>
                    </div>


                    {{-- Signatures --}}
                    <div class="signature-box">
                        <div class="sig-row">
                            <b>Written by:</b> <span>{{ $fatwa->muftiAnswer->mufti->name }}</span>
                        </div>
                        @if($fatwa->verifiedBy)
                        <div class="sig-row">
                            <b>Verified by:</b> <span>{{ $fatwa->verifiedBy->name }}</span>
                        </div>
                        @endif
                        @if($fatwa->checkedApprovedBy)
                        <div class="sig-row">
                            <b>Checked & Approved by:</b> <span>{{ $fatwa->checkedApprovedBy->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Tags Footer --}}
                <div class="p-4 border-top bg-light d-flex justify-content-between align-items-center no-print">
                    <div>
                        <span class="fw-bold small me-3">Tags:</span>
                        @foreach(['#fiqh', '#islamic_query', '#fatwa'] as $tag)
                            <a href="#" class="text-muted small me-2 text-decoration-none">{{ $tag }}</a>
                        @endforeach
                    </div>
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-share"></i></a>
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
                        <button class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent pe-3">
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
                        <img src="{{ asset($rf->featured_image ?: 'assets/img/placeholder.jpg') }}" alt="Fatwa">
                    </div>
                    <div class="recent-info">
                        <h6>{{ Str::limit($rf->question->title ?: strip_tags($rf->question->body), 50) }}</h6>
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
                            <img src="{{ asset($rf->featured_image ?: 'assets/img/placeholder.jpg') }}" alt="Fatwa">
                        </div>
                        <div class="recent-info">
                            <h6>{{ Str::limit($rf->question->title ?: strip_tags($rf->question->body), 50) }}</h6>
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
                <ul class="cat-list">
                    @foreach($allCategories as $cat)
                    <li>
                        <a href="{{ route('fatwa.archive', ['category' => $cat->id]) }}">
                            <span>{{ $cat->name }}</span>
                            <span class="cat-count">{{ $cat->questions_count }}</span>
                        </a>
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
            navigator.share(shareData).catch(() => {});
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
