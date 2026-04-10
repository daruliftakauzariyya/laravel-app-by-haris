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

<div class="row g-4">
    @forelse($muftis as $mufti)
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <div class="mufti-profile-card">
            {{-- Profile Header: Image & Verified Status --}}
            <div class="mufti-card-image-wrapper">
                <img src="{{ $mufti->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($mufti->name).'&background=f8fafc&color=1a7a4a&size=300&bold=true' }}" 
                     alt="{{ $mufti->name }}"
                     class="mufti-card-image shadow-sm">
            </div>

            <div class="mufti-card-body">
                <div class="d-flex align-items-center gap-1 mb-1">
                    <h6 class="mufti-card-name">{{ $mufti->name }}</h6>
                    <i class="bi bi-patch-check-fill text-success verified-badge"></i>
                </div>
                <p class="mufti-card-email text-muted small mb-3">{{ $mufti->email }}</p>

                {{-- Performance Stats --}}
                <div class="mufti-card-stats mb-4">
                    <div class="stat-item" title="Total Assigned">
                        <i class="bi bi-journal-text me-1 text-muted"></i>
                        <span>{{ number_format($mufti->total_assigned ?? 0) }}</span>
                    </div>
                    <div class="stat-item" title="Published Fatwas">
                        <i class="bi bi-patch-check me-1 text-muted"></i>
                        <span>{{ number_format($mufti->published_count ?? 0) }}</span>
                    </div>
                    @if($mufti->avg_days)
                    <div class="stat-item" title="Avg Days to Answer">
                        <i class="bi bi-lightning-charge-fill me-1 text-warning"></i>
                        <span>{{ $mufti->avg_days }}d</span>
                    </div>
                    @endif
                </div>

                {{-- Action Gateway --}}
                <div class="d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.muftis.activity', $mufti) }}" class="btn btn-stats shadow-sm px-3 py-2">
                        View Stats <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    
                    <div class="dropdown">
                        <button class="btn btn-action-trigger shadow-sm" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 sm-small p-2">
                            <li>
                                <form method="POST" action="{{ route('admin.muftis.toggle-status', $mufti) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="dropdown-item rounded-2 {{ $mufti->is_active ? 'text-danger' : 'text-success' }}">
                                        <i class="bi bi-power me-2"></i>{{ $mufti->is_active ? 'Deactivate' : 'Activate' }} Profile
                                    </button>
                                </form>
                            </li>
                            <li><hr class="dropdown-divider opacity-50"></li>
                            <li><a class="dropdown-item rounded-2" href="{{ route('admin.muftis.edit', $mufti) }}"><i class="bi bi-pencil me-2"></i>Edit Profile</a></li>
                            @if(!$mufti->muftiAnswers()->exists() && !$mufti->assignedQuestions()->exists())
                            <li>
                                <form method="POST" action="{{ route('admin.muftis.destroy', $mufti) }}" onsubmit="return confirm('Delete Mufti profile?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item rounded-2 text-danger"><i class="bi bi-trash me-2"></i>Delete Scholar</button>
                                </form>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Status Strip --}}
            <div class="mufti-status-strip {{ $mufti->is_active ? 'bg-success' : 'bg-danger' }}"></div>
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

@push('styles')
<style>
    .mufti-profile-card {
        background: #fff;
        border-radius: 2rem;
        padding: 1.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.025);
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .mufti-profile-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 50px rgba(0,0,0,0.08);
    }
    .mufti-card-image-wrapper {
        margin-bottom: 1.25rem;
        display: flex;
        justify-content: center;
        width: 100%;
    }
    .mufti-card-image {
        width: 95px;
        height: 120px;
        object-fit: cover;
        border-radius: 1.25rem;
        border: 4px solid #fff;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .mufti-card-body {
        text-align: center;
        width: 100%;
    }
    .mufti-card-name {
        font-weight: 700;
        font-size: 1.15rem;
        margin: 0;
        color: #1a202c;
    }
    .verified-badge {
        font-size: 1rem;
    }
    .mufti-card-email {
        font-size: 0.8rem;
        font-weight: 500;
        opacity: 0.7;
    }
    .mufti-card-stats {
        display: flex;
        justify-content: center;
        gap: 1.25rem;
        padding: 0.75rem 0;
        border-top: 1px solid rgba(0,0,0,0.03);
        border-bottom: 1px solid rgba(0,0,0,0.03);
    }
    .stat-item {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        font-weight: 700;
        color: #4a5568;
    }
    .btn-stats {
        background: #f8fafc;
        border-radius: 1.25rem;
        font-size: 0.85rem;
        font-weight: 700;
        color: #1a7a4a;
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.2s;
    }
    .btn-stats:hover {
        background: #1a7a4a;
        color: #fff;
        border-color: #1a7a4a;
    }
    .btn-action-trigger {
        background: #f8fafc;
        width: 40px;
        height: 40px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(0,0,0,0.05);
        color: #4a5568;
    }
    .btn-action-trigger:hover {
        background: #eef2f7;
    }
    .mufti-status-strip {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 4px;
        opacity: 0.6;
    }
    .sm-small { font-size: 0.85rem; }
</style>
@endpush

@endsection
