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
            <div class="col-md-3 col-sm-6">
                <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-search me-1"></i>Search Keyword</label>
                <input type="text" name="search" class="form-control" placeholder="Search fatwas..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-folder2 me-1"></i>Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($allCategories as $parent)
                        <option value="{{ $parent->id }}" {{ request('category') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                        @foreach($parent->children as $child)
                            <option value="{{ $child->id }}" {{ request('category') == $child->id ? 'selected' : '' }}>
                                &nbsp;&nbsp;&mdash;&nbsp;{{ $child->name }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-bank me-1"></i>Madhab</label>
                <select name="madhab_id" class="form-select">
                    <option value="">All Madhabs</option>
                    @foreach($madhabs as $m)
                        <option value="{{ $m->id }}" {{ request('madhab_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-sort-down me-1"></i>Sort By</label>
                <select name="sort" class="form-select">
                    <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Latest Published</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest Published</option>
                    <option value="most_read" {{ request('sort') == 'most_read' ? 'selected' : '' }}>Most Read (Popular)</option>
                    <option value="most_liked" {{ request('sort') == 'most_liked' ? 'selected' : '' }}>Most Liked</option>
                    <option value="most_starred" {{ request('sort') == 'most_starred' ? 'selected' : '' }}>Highest Rated (Stars)</option>
                </select>
            </div>
            <div class="col-md-2 col-sm-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                <a href="{{ route('fatwa.archive') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </div>
    </form>

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-lg-8">
            @if($fatwas->isEmpty())
                <div class="text-center py-5 text-muted card border-0 shadow-sm">
                    <i class="bi bi-inbox" style="font-size:3rem;"></i>
                    <p class="mt-3">No fatwas found. <a href="{{ route('question.create') }}">Be the first to ask!</a></p>
                </div>
            @else
            <div class="row g-4">
                @foreach($fatwas as $fatwa)
                <div class="col-md-6">
                    <a href="{{ route('fatwa.show', $fatwa) }}" class="text-decoration-none text-dark">
                        <div class="fatwa-card h-100">
                            {{-- Thumbnail --}}
                            <div class="fatwa-thumb">
                                <img src="{{ asset($fatwa->featured_image ?: 'assets/img/fatwa-placeholder.jpg') }}"
                                     alt="Fatwa"
                                     onerror="this.src='https://placehold.co/400x180/1a7a4a/fff?text=Fatwa'">
                                <span class="madhab-badge">{{ $fatwa->madhab?->name }}</span>
                            </div>

                            {{-- Card Body --}}
                            <div class="fatwa-body">
                                <div class="fatwa-meta">
                                    <span><i class="bi bi-folder2 me-1"></i>{{ $fatwa->category->name ?? 'General' }}</span>
                                    <span class="text-warning">
                                        @for($i=0; $i<($fatwa->category->stars ?? 5); $i++)
                                            <i class="bi bi-star-fill x-small"></i>
                                        @endfor
                                    </span>
                                    <span>{{ $fatwa->published_at?->format('d M Y') }}</span>
                                </div>

                                <h6 class="fatwa-title">
                                    {{ Str::limit($fatwa->title ?: strip_tags($fatwa->body), 75) }}
                                </h6>

                                <p class="fatwa-excerpt">
                                    {{ Str::limit(strip_tags($fatwa->answer), 95) }}
                                </p>

                                <div class="fatwa-footer">
                                    <i class="bi bi-person-circle text-muted flex-shrink-0"></i>
                                    <a href="{{ route('fatwa.mufti.archive', $fatwa->assignedMufti) }}"
                                       class="fatwa-author text-decoration-none text-dark hover-green">
                                        {{ $fatwa->assignedMufti->name ?? 'Darul Ifta' }}
                                    </a>
                                    <span class="like-count flex-shrink-0 text-danger small fw-semibold">
                                        <i class="bi bi-heart-fill me-1"></i>{{ number_format($fatwa->likes) }}
                                    </span>
                                    <span class="read-more-btn flex-shrink-0">Read More <i class="bi bi-arrow-right"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            <div class="mt-5">{{ $fatwas->links() }}</div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Widget: Search --}}
            <div class="sidebar-widget sidebar-search">
                <form action="{{ route('fatwa.archive') }}" method="GET">
                    <div class="position-relative">
                        <input type="text" name="search" class="form-control" placeholder="Search fatwas..." value="{{ request('search') }}">
                        <button class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent pe-3">
                            <i class="bi bi-search text-muted"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Widget: Categories --}}
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

            {{-- Widget: Madhab Quick Select --}}
            <div class="sidebar-widget">
                <span class="widget-title">Madhabs</span>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @foreach($madhabs as $m)
                        <a href="{{ route('fatwa.archive', ['madhab_id' => $m->id]) }}" 
                           class="btn btn-sm btn-outline-success px-3 rounded-pill {{ request('madhab_id') == $m->id ? 'active' : '' }}">
                            {{ $m->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    :root {
        --slate-blue: #4A6372;
        --brand-green: #1a7a4a;
        --border-color: #edf2f7;
    }

    /* ============================================
       Fatwa Card
       ============================================ */
    .fatwa-card {
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #edf2f7;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        display: flex;
        flex-direction: column;
    }
    .fatwa-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(26, 122, 74, 0.12);
        border-color: #c6e9d8;
    }

    .fatwa-thumb {
        position: relative;
        width: 100%;
        height: 180px;
        overflow: hidden;
        background: #f0f4f8;
    }
    .fatwa-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .fatwa-card:hover .fatwa-thumb img { transform: scale(1.04); }

    .madhab-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #1a7a4a;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        letter-spacing: 0.5px;
    }

    .fatwa-body {
        padding: 18px 20px 16px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .fatwa-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        color: #a0aec0;
        margin-bottom: 10px;
        font-weight: 500;
    }
    .fatwa-meta span:first-child { color: #1a7a4a; font-weight: 600; }

    .fatwa-title {
        font-size: 0.98rem;
        font-weight: 800;
        color: #1a202c;
        line-height: 1.45;
        margin-bottom: 10px;
        flex-grow: 1;
    }

    .fatwa-excerpt {
        font-size: 0.83rem;
        color: #718096;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .fatwa-footer {
        display: flex;
        align-items: center;
        border-top: 1px solid #f1f5f9;
        padding-top: 12px;
        margin-top: auto;
        gap: 6px;
        flex-wrap: nowrap;  /* Never wrap */
        overflow: hidden;
    }
    .fatwa-author {
        font-size: 0.8rem;
        font-weight: 600;
        color: #4a5568;
        flex: 1 1 0;         /* Grow to fill, but shrink when needed */
        min-width: 0;        /* Key: allow flex child to shrink below content size */
        white-space: nowrap; /* Force single line */
        overflow: hidden;
        text-overflow: ellipsis; /* Show ... when name is clipped */
        display: block;
    }
    .fatwa-author:hover { color: #1a7a4a; }
    .read-more-btn {
        font-size: 0.8rem;
        font-weight: 700;
        color: #1a7a4a;
        border: 1.5px solid #1a7a4a;
        padding: 4px 14px;
        border-radius: 20px;
        transition: 0.2s;
    }
    .fatwa-card:hover .read-more-btn { background: #1a7a4a; color: #fff; }

    /* ============================================
       Sidebar Widgets
       ============================================ */
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
        width: 100%;
    }

    .sidebar-search .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
        font-size: 0.95rem;
    }

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
</style>
@endpush
@endsection
