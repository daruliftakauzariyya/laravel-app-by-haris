@extends('layouts.public')
@section('title', 'Mufti Portfolio — ' . $user->name)
@section('content')
<div class="container py-5">
    <div class="mb-5">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="row g-0">
                <div class="col-md-3 bg-primary bg-opacity-10 d-flex flex-column align-items-center justify-content-center p-4 py-5 text-center">
                    @if($user->avatar)
                        <img src="{{ asset($user->avatar) }}" class="rounded-circle border border-4 border-white shadow-sm mb-3" style="width:140px; height:140px; object-fit:cover;">
                    @else
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center border border-4 border-white shadow-sm mb-3" style="width:140px; height:140px;">
                            <i class="bi bi-person-fill h1 mb-0 opacity-50"></i>
                        </div>
                    @endif
                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted small mb-0">{{ $user->country ?? 'Scholar' }}</p>
                    <div class="mt-3">
                        <span class="badge bg-white text-primary border border-primary border-opacity-25 px-3">
                            {{ $user->madhab && is_array($user->madhab) ? implode(', ', $user->madhab) : ($user->madhab ?: 'Various Madhabs') }}
                        </span>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card-body p-4 p-lg-5">
                        <h4 class="fw-bold mb-4">Academic Impact & Contributions</h4>
                        <div class="row g-4 text-center">
                            <div class="col-6 col-lg-3">
                                <div class="bg-light rounded-4 p-4 border h-100 transition-up">
                                    <div class="h2 fw-bold text-primary mb-1">{{ number_format($totalAnswers) }}</div>
                                    <div class="text-muted x-small fw-bold text-uppercase tracking-wider">Questions Answered</div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="bg-light rounded-4 p-4 border h-100 transition-up">
                                    <div class="h2 fw-bold text-success mb-1">{{ number_format($totalTopics) }}</div>
                                    <div class="text-muted x-small fw-bold text-uppercase tracking-wider">Topics Covered</div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="bg-light rounded-4 p-4 border h-100 transition-up">
                                    <div class="h2 fw-bold text-danger mb-1">{{ number_format($totalLikes) }}</div>
                                    <div class="text-muted x-small fw-bold text-uppercase tracking-wider">Appreciation/Likes</div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="bg-light rounded-4 p-4 border h-100 transition-up">
                                    <div class="h2 fw-bold text-info mb-1">{{ number_format($totalViews) }}</div>
                                    <div class="text-muted x-small fw-bold text-uppercase tracking-wider">Public Reads</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <h5 class="fw-bold mb-4 d-flex align-items-center">
                <i class="bi bi-journal-check text-primary me-2 h4 mb-0"></i>
                Published Fatwas ({{ $fatwas->total() }})
            </h5>

            @if($fatwas->isEmpty())
                <div class="card border-0 shadow-sm p-5 text-center">
                    <i class="bi bi-inbox h1 text-muted opacity-25"></i>
                    <p class="mb-0">No published fatwas found for this scholar.</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($fatwas as $fatwa)
                    <div class="col-md-6">
                        <a href="{{ route('fatwa.show', $fatwa) }}" class="text-decoration-none text-dark">
                            <div class="fatwa-card h-100">
                                <div class="fatwa-thumb">
                                    <img src="{{ asset($fatwa->featured_image ?: 'assets/img/fatwa-placeholder.jpg') }}" alt="Fatwa" onerror="this.src='https://placehold.co/400x180/1a7a4a/fff?text=Fatwa'">
                                    <span class="madhab-badge">{{ $fatwa->madhab?->name == 'Shafii' ? "Shafi'i" : ($fatwa->madhab?->name ?? 'General/Other') }}</span>
                                </div>
                                <div class="fatwa-body">
                                    <div class="fatwa-meta small mb-2 d-flex justify-content-between">
                                        <span class="text-primary fw-bold">{{ $fatwa->category->name ?? 'General' }}</span>
                                        <span class="text-warning small">
                                            @for($i=0; $i<($fatwa->category->stars ?? 5); $i++)
                                                <i class="bi bi-star-fill"></i>
                                            @endfor
                                        </span>
                                        <span class="text-muted">{{ $fatwa->published_at?->format('d M Y') }}</span>
                                    </div>
                                    <h6 class="fw-bold mb-2 line-clamp-2">{{ Str::limit($fatwa->title ?: strip_tags($fatwa->body), 60) }}</h6>
                                    <p class="text-muted x-small mb-3 line-clamp-2">{{ Str::limit(strip_tags($fatwa->answer), 80) }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-2">
                                        <span class="x-small text-muted"><i class="bi bi-eye me-1"></i>{{ number_format($fatwa->views) }} views</span>
                                        <span class="x-small text-muted"><i class="bi bi-heart-fill text-danger me-1"></i>{{ number_format($fatwa->likes) }}</span>
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

        <div class="col-lg-4">
            <div class="sidebar-widget">
                <span class="widget-title">Knowledge Streams</span>
                <ul class="cat-list" style="padding-left: 0; list-style: none;">
                    @foreach($allCategories as $cat)
                    @php
                        $isCurrentOrChildSelected = (request('category') == $cat->id || $cat->children->pluck('id')->contains(request('category')));
                    @endphp
                    <li class="mb-3">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <a href="{{ route('fatwa.archive', ['category' => $cat->id]) }}" 
                               class="flex-grow-1 d-flex justify-content-between align-items-center text-decoration-none {{ request('category') == $cat->id ? 'active text-success fw-bold' : 'text-dark' }}" style="font-weight: 600; font-size: 0.95rem;">
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
                                <li class="py-1">
                                    <a href="{{ route('fatwa.archive', ['category' => $child->id]) }}" 
                                       class="d-flex justify-content-between align-items-center text-decoration-none {{ request('category') == $child->id ? 'active text-success fw-bold' : 'text-muted' }}" style="font-size: 0.85rem; font-weight: 500;">
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
            
            <div class="card border-0 shadow-sm bg-success bg-opacity-10 p-4 rounded-4 mt-4">
                <h6 class="fw-bold text-success"><i class="bi bi-shield-check me-2"></i>Verified Scholar</h6>
                <p class="small text-muted mb-0">This scholar is a verified contributor to Darul Ifta's archive of Islamic jurisprudence.</p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .fatwa-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #edf2f7;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    .fatwa-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    .fatwa-thumb { position: relative; height: 160px; overflow: hidden; }
    .fatwa-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .fatwa-body { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
    .madhab-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(var(--bs-primary-rgb), 0.9);
        color: #fff;
        font-size: 0.65rem;
        padding: 2px 10px;
        border-radius: 20px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .transition-up { transition: transform 0.2s ease; }
    .transition-up:hover { transform: translateY(-3px); }
    .x-small { font-size: 0.75rem; }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .hover-green:hover { color: #1a7a4a !important; }

    /* Sidebar Widgets */
    .sidebar-widget {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border: 1px solid #edf2f7;
    }
    .widget-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #4A6372;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #1a7a4a;
        display: inline-block;
        width: 100%;
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
    }
    .cat-list a:hover { color: #1a7a4a; }
    .cat-count { background: #edf2f7; padding: 2px 10px; border-radius: 20px; font-size: 0.75rem; color: #718096; }
</style>
@endpush
@endsection
