@extends('layouts.admin')
@section('title', 'Mufti Management')
@section('topbar-title', 'Mufti User Profiles')
@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-0">Scholar Faculty & Dashboard Signals</h4>
        <p class="text-muted small mb-0">Monitor live activity, login counts, and scholarly workload</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.muftis.create') }}" class="btn btn-primary shadow-sm px-4">
            <i class="bi bi-person-plus-fill me-2"></i>Add New Mufti
        </a>
    </div>
</div>

{{-- Top Faculty Pulse Metrics --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4 col-12">
        <div class="p-3 bg-white rounded-4 border shadow-xs d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted text-uppercase x-small fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Registered Scholars</div>
                <h3 class="fw-800 text-dark mb-0 mt-1">{{ number_format($totalMuftis ?? $muftis->total()) }}</h3>
            </div>
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle fs-4">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-4 col-12">
        <div class="p-3 bg-white rounded-4 border shadow-xs d-flex align-items-center justify-content-between" style="border-left: 4px solid #10b981 !important;">
            <div>
                <div class="text-success text-uppercase x-small fw-bold d-flex align-items-center gap-1" style="font-size: 10px; letter-spacing: 0.5px;">
                    <span class="live-pulse-dot" style="width: 7px; height: 7px;"></span> Live Online Now
                </div>
                <h3 class="fw-800 text-success mb-0 mt-1">{{ number_format($onlineCount ?? 0) }} <span class="text-muted fw-normal fs-6">Scholars</span></h3>
            </div>
            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle fs-4">
                <i class="bi bi-broadcast"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-4 col-12">
        <div class="p-3 bg-white rounded-4 border shadow-xs d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted text-uppercase x-small fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Faculty Total Logins</div>
                <h3 class="fw-800 text-dark mb-0 mt-1">{{ number_format($totalLogins ?? 0) }}</h3>
            </div>
            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle fs-4">
                <i class="bi bi-box-arrow-in-right"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    @forelse($muftis as $mufti)
    @php
        $isMuftiOnline = $mufti->isOnline(5);
        $lastSeenFormatted = $mufti->last_seen_at ? $mufti->last_seen_at->diffForHumans() : 'Never active';
        $lastLoginFormatted = $mufti->last_login_at ? $mufti->last_login_at->diffForHumans() : 'Never';
    @endphp
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
                        <button class="btn-glass-action" data-bs-toggle="dropdown" aria-label="Scholar Options">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2">
                            <li><a class="dropdown-item rounded-2" href="{{ route('admin.muftis.history', $mufti) }}"><i class="bi bi-clock-history me-2 text-primary"></i>View Work History</a></li>
                            <li><a class="dropdown-item rounded-2" href="{{ route('admin.muftis.activity', $mufti) }}"><i class="bi bi-speedometer2 me-2"></i>Performance Stats</a></li>
                            <li><a class="dropdown-item rounded-2" href="{{ route('admin.muftis.edit', $mufti) }}"><i class="bi bi-pencil-fill me-2"></i>Edit Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('admin.muftis.toggle-status', $mufti) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="dropdown-item rounded-2 {{ $mufti->is_active ? 'text-danger' : 'text-success' }}">
                                        <i class="bi bi-power me-2"></i>{{ $mufti->is_active ? 'Deactivate Account' : 'Activate Account' }} 
                                    </button>
                                </form>
                            </li>
                            @if(!$mufti->assignedQuestions()->exists())
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('admin.muftis.destroy', $mufti) }}" onsubmit="return confirm('Delete Scholar profile?');">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item rounded-2 text-danger"><i class="bi bi-trash-fill me-2"></i>Delete Scholar</button>
                                </form>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Live Online Status Badge (Top-Left) --}}
            <div class="scholar-live-tag {{ $isMuftiOnline ? 'tag-online' : 'tag-offline' }}" title="{{ $isMuftiOnline ? 'Currently Live on Mufti Dashboard' : 'Last seen: ' . $lastSeenFormatted }}">
                @if($isMuftiOnline)
                    <span class="live-pulse-dot"></span>
                    <span class="tag-label">ONLINE</span>
                @else
                    <span class="offline-dot"></span>
                    <span class="tag-label">{{ $mufti->last_seen_at ? $mufti->last_seen_at->diffForHumans(null, true) : 'OFFLINE' }}</span>
                @endif
            </div>

            {{-- Info Content --}}
            <div class="scholar-info-overlay">
                <div class="info-content">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h5 class="scholar-name mb-0 text-truncate" title="{{ $mufti->name }}">{{ $mufti->name }}</h5>
                        <i class="bi bi-patch-check-fill verified-badge-icon flex-shrink-0"></i>
                    </div>
                    <p class="scholar-description text-muted small mb-2">
                        Scholar at Darul Ifta • {{ $mufti->country ?? 'Mufti' }}
                    </p>

                    <div class="scholar-footer">
                        <div class="mini-metrics">
                            <div class="metric-pill" title="Dashboard Total Logins">
                                <i class="bi bi-box-arrow-in-right text-warning"></i> {{ number_format($mufti->login_count ?? 0) }}
                            </div>
                            <div class="metric-pill" title="Assigned Questions">
                                <i class="bi bi-people"></i> {{ number_format($mufti->total_assigned ?? 0) }}
                            </div>
                            <div class="metric-pill" title="Published Answers">
                                <i class="bi bi-journal-check text-success"></i> {{ number_format($mufti->published_count ?? 0) }}
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.muftis.history', $mufti) }}" class="btn btn-xs btn-primary rounded-pill px-2 fw-bold text-white d-inline-flex align-items-center gap-1 shadow-xs" style="font-size: 11px;" title="Work History & Live Logs">
                                <i class="bi bi-clock-history"></i> History
                            </a>
                            <a href="{{ route('admin.muftis.activity', $mufti) }}" class="btn-scholar-view shadow-sm" title="View Full Performance & Login Logs">
                                Stats <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Account Status Dot (Green for Active account, Red for Disabled) --}}
            <div class="status-indicator-dot {{ $mufti->is_active ? 'active' : 'inactive' }}" title="Account Status: {{ $mufti->is_active ? 'Active' : 'Disabled' }}"></div>
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
        transform: translateY(-10px);
        box-shadow: 0 25px 60px rgba(0,0,0,0.14);
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
        top: 1.25rem;
        right: 1.25rem;
        z-index: 5;
    }
    .btn-glass-action {
        width: 36px;
        height: 36px;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1a202c;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }
    .btn-glass-action:hover {
        background: #fff;
        transform: scale(1.06);
    }

    /* ── Live Online Tag ── */
    .scholar-live-tag {
        position: absolute;
        top: 1.25rem;
        left: 1.25rem;
        z-index: 5;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 5px;
        backdrop-filter: blur(8px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .scholar-live-tag.tag-online {
        background: rgba(16, 185, 129, 0.9);
        color: #ffffff;
        border: 1px solid rgba(255,255,255,0.3);
    }
    .scholar-live-tag.tag-offline {
        background: rgba(15, 23, 42, 0.7);
        color: rgba(255,255,255,0.8);
        border: 1px solid rgba(255,255,255,0.1);
    }

    .live-pulse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: #ffffff;
        border-radius: 50%;
        box-shadow: 0 0 0 rgba(255, 255, 255, 0.6);
        animation: pulse-live 1.8s infinite;
    }
    @keyframes pulse-live {
        0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.8); }
        70% { box-shadow: 0 0 0 6px rgba(255, 255, 255, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
    }

    .offline-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        background: rgba(255,255,255,0.4);
        border-radius: 50%;
    }

    .scholar-info-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 2.5rem 1.15rem 1.25rem;
        background: linear-gradient(to top, 
            rgba(0,0,0,0.98) 0%, 
            rgba(0,0,0,0.85) 45%, 
            rgba(0,0,0,0.4) 75%, 
            rgba(0,0,0,0) 100%);
        z-index: 3;
    }
    .scholar-name {
        font-weight: 900;
        font-size: 1.1rem;
        color: #ffffff;
        letter-spacing: -0.5px;
        line-height: 1.15;
    }
    .verified-badge-icon {
        color: #10b981;
        font-size: 1.05rem;
        filter: drop-shadow(0 0 5px rgba(16, 185, 129, 0.4));
    }
    .scholar-description {
        font-weight: 600;
        color: rgba(255,255,255,0.75) !important;
        margin-top: 2px;
        font-size: 0.72rem;
    }

    .scholar-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 0.75rem;
        gap: 6px;
    }
    .mini-metrics {
        display: flex;
        gap: 0.3rem;
        flex-wrap: wrap;
    }
    .metric-pill {
        font-size: 0.62rem;
        font-weight: 800;
        background: rgba(255,255,255,0.18);
        backdrop-filter: blur(5px);
        color: #ffffff;
        padding: 0.3rem 0.45rem;
        border-radius: 0.55rem;
        display: flex;
        align-items: center;
        gap: 3px;
        border: 1px solid rgba(255,255,255,0.15);
    }
    .btn-scholar-view {
        background: #ffffff;
        border-radius: 100px;
        font-size: 0.7rem;
        font-weight: 800;
        color: #1a202c;
        text-decoration: none;
        padding: 0.45rem 0.85rem;
        border: 1px solid rgba(0,0,0,0.08);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        white-space: nowrap;
    }
    .btn-scholar-view:hover {
        background: #1a202c;
        color: #fff;
        transform: scale(1.05);
        border-color: #1a202c;
    }

    .status-indicator-dot {
        position: absolute;
        bottom: 1.25rem;
        right: 1.25rem;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        z-index: 5;
        border: 2px solid #fff;
        display: none; /* Replaced by top-left live tag */
    }
</style>
@endpush

@endsection
