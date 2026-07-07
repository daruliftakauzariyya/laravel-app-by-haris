@extends('layouts.admin')
@section('title', 'Mufti Management')
@section('topbar-title', 'Mufti User Profiles')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Scholar Faculty</h4>
        <p class="text-muted small mb-0">Manage and monitor designated scholarship profiles</p>
    </div>
    <a href="{{ route('admin.muftis.create') }}" class="btn btn-primary shadow-sm px-4">
        <i class="bi bi-person-plus-fill me-2"></i>Add New Mufti
    </a>
</div>

<div class="row g-3">
    @forelse($muftis as $mufti)
    <div class="col-6 col-md-4 col-lg-3 col-xl-5th">
        <div class="scholar-full-card">
            {{-- Background/Main Image --}}
            <div class="scholar-image-container">
                <img src="{{ $mufti->avatar ? asset($mufti->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($mufti->name).'&background=f1f5f9&color=1a7a4a&size=512' }}" 
                     alt="{{ $mufti->name }}"
                     class="scholar-main-img">
                
                {{-- Quick Actions Overlay --}}
                <div class="scholar-overlay-actions">
                    <div class="dropdown">
                        <button class="btn-glass-action" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2">
                            <li><a class="dropdown-item rounded-2" href="{{ route('admin.muftis.edit', $mufti) }}"><i class="bi bi-pencil-fill me-2"></i>Edit Profile</a></li>
                            <li>
                                <form method="POST" action="{{ route('admin.muftis.toggle-status', $mufti) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="dropdown-item rounded-2 {{ $mufti->is_active ? 'text-danger' : 'text-success' }}">
                                        <i class="bi bi-power me-2"></i>{{ $mufti->is_active ? 'Deactivate' : 'Activate' }} 
                                    </button>
                                </form>
                            </li>
                            @if(!$mufti->assignedQuestions()->exists())
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('admin.muftis.destroy', $mufti) }}" onsubmit="return confirm('Delete Scholar?');">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item rounded-2 text-danger"><i class="bi bi-trash-fill me-2"></i>Delete Scholar</button>
                                </form>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Info Content --}}
            <div class="scholar-info-overlay">
                <div class="info-content">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h5 class="scholar-name mb-0">{{ $mufti->name }}</h5>
                        <i class="bi bi-patch-check-fill verified-badge-icon"></i>
                    </div>
                    <p class="scholar-description text-muted small mb-3">
                        Scholar at Darul Ifta • {{ $mufti->country ?? 'Mufti' }}
                    </p>

                    <div class="scholar-footer">
                        <div class="mini-metrics">
                            <div class="metric-pill" title="Assigned">
                                <i class="bi bi-people"></i> {{ number_format($mufti->total_assigned ?? 0) }}
                            </div>
                            <div class="metric-pill" title="Published">
                                <i class="bi bi-journal-check"></i> {{ number_format($mufti->published_count ?? 0) }}
                            </div>
                        </div>
                        <a href="{{ route('admin.muftis.activity', $mufti) }}" class="btn-scholar-view shadow-sm">
                            Stats <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Subtle Status Indicator --}}
            <div class="status-indicator-dot {{ $mufti->is_active ? 'active' : 'inactive' }}"></div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 mt-4">
        <div class="mb-3"><i class="bi bi-people text-muted" style="font-size:3rem; opacity:.2;"></i></div>
        <h5 class="text-muted">No Scholar Profiles Registered</h5>
        <p class="text-muted small">Scholar accounts will appear here once they are added to the system.</p>
    </div>
    @endforelse
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $muftis->links() }}
</div>

@push('styles')
<style>
    .col-xl-5th {
        flex: 0 0 20%;
        max-width: 20%;
    }
    @media (max-width: 1200px) {
        .col-xl-5th {
            flex: 0 0 25%;
            max-width: 25%;
        }
    }
    @media (max-width: 992px) {
        .col-xl-5th {
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }
    }

    .scholar-full-card {
        aspect-ratio: 95 / 150;
        background: #fff;
        border-radius: 2.25rem;
        position: relative;
        overflow: hidden;
        border: 3px solid #fff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
        display: flex;
        flex-direction: column;
    }
    .scholar-full-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 60px rgba(0,0,0,0.12);
    }

    .scholar-image-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }
    .scholar-main-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .scholar-full-card:hover .scholar-main-img {
        transform: scale(1.04);
    }

    .scholar-overlay-actions {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        z-index: 5;
    }
    .btn-glass-action {
        width: 38px;
        height: 38px;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1a202c;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: all 0.2s;
    }
    .btn-glass-action:hover {
        background: #fff;
        transform: scale(1.05);
    }

    .scholar-info-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 2.5rem 1.25rem 1.5rem;
        background: linear-gradient(to top, 
            rgba(0,0,0,1) 0%, 
            rgba(0,0,0,0.85) 45%, 
            rgba(0,0,0,0.4) 75%, 
            rgba(0,0,0,0) 100%);
        z-index: 3;
    }
    .scholar-name {
        font-weight: 900;
        font-size: 1.15rem;
        color: #ffffff;
        letter-spacing: -0.5px;
        line-height: 1.15;
    }
    .verified-badge-icon {
        color: #10b981;
        font-size: 1.1rem;
        filter: drop-shadow(0 0 5px rgba(16, 185, 129, 0.4));
    }
    .scholar-description {
        font-weight: 600;
        color: rgba(255,255,255,0.7) !important;
        margin-top: 3px;
        font-size: 0.72rem;
    }

    .scholar-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 1rem;
    }
    .mini-metrics {
        display: flex;
        gap: 0.4rem;
    }
    .metric-pill {
        font-size: 0.62rem;
        font-weight: 800;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(5px);
        color: #ffffff;
        padding: 0.35rem 0.5rem;
        border-radius: 0.6rem;
        display: flex;
        align-items: center;
        gap: 3px;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .btn-scholar-view {
        background: #ffffff;
        border-radius: 100px;
        font-size: 0.72rem;
        font-weight: 800;
        color: #1a202c;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border: 1px solid rgba(0,0,0,0.08);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .btn-scholar-view:hover {
        background: #1a202c;
        color: #fff;
        transform: scale(1.05);
        border-color: #1a202c;
    }

    .status-indicator-dot {
        position: absolute;
        top: 1.5rem;
        left: 1.5rem;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        z-index: 5;
        border: 2px solid #fff;
    }
    .status-indicator-dot.active { background-color: #10b981; box-shadow: 0 0 10px #10b981; }
    .status-indicator-dot.inactive { background-color: #ef4444; }
</style>
@endpush

@endsection
