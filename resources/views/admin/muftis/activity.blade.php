@extends('layouts.admin')

@section('title', 'Academic Impact — ' . $user->name)
@section('topbar-title', 'Scholar Performance Insights')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
            <div class="card-body p-0">
                <div class="d-flex align-items-center p-4 bg-white">
                    <div class="me-4 position-relative">
                        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1a7a4a&color=fff&size=100' }}" 
                             class="rounded-circle shadow-sm border border-4 border-light" width="100" height="100">
                        <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-white border-2 rounded-circle shadow-sm" title="Active"></span>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 text-dark">{{ $user->name }}</h3>
                        <p class="text-muted mb-2"><i class="bi bi-geo-alt me-1"></i>{{ $user->country ?? 'Global Scholar' }}</p>
                        <div class="d-flex gap-2">
                            <span class="badge bg-soft-success text-success px-3 rounded-pill border border-success border-opacity-10">Active Scholar</span>
                            <span class="badge bg-soft-primary text-primary px-3 rounded-pill border border-primary border-opacity-10">{{ count($user->madhab ?? []) }} Madhabs</span>
                            @if($user->can_check_approve)
                                <span class="badge bg-soft-info text-info px-3 rounded-pill border border-info border-opacity-10"><i class="bi bi-shield-check me-1"></i>Senior Checker</span>
                            @endif
                        </div>
                    </div>
                    <div class="ms-auto text-end">
                        <a href="{{ route('admin.muftis.edit', $user) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-pencil me-1"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Main Activity Section --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark mb-0">Academic Impact & Contributions</h5>
                <p class="text-muted small">Comprehensive overview of scholarly workflow and output</p>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row g-4">
                    {{-- 1. Questions --}}
                    <div class="col-md-6">
                        <div class="p-3 border rounded-4 bg-light bg-opacity-50 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                                    <i class="bi bi-chat-left-dots h4 mb-0"></i>
                                </div>
                                <h6 class="fw-bold mb-0">Question Metrics</h6>
                            </div>
                            <div class="space-y-3">
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-dashed">
                                    <span class="text-muted small">Total Assigned</span>
                                    <span class="badge bg-primary rounded-pill font-inter">{{ $stats['assigned'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-dashed">
                                    <span class="text-muted small">Answered Submission</span>
                                    <span class="badge bg-info text-dark rounded-pill font-inter">{{ $stats['answered'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-dashed">
                                    <span class="text-muted small">Publicly Published</span>
                                    <span class="badge bg-success rounded-pill font-inter">{{ $stats['published'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-2">
                                    <span class="text-muted small">System Pool (Unassigned)</span>
                                    <span class="badge bg-secondary rounded-pill font-inter">{{ $stats['unassigned'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Topics Covered --}}
                    <div class="col-md-6">
                        <div class="p-3 border rounded-4 bg-light bg-opacity-50 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-3 p-2 me-3">
                                    <i class="bi bi-tags h4 mb-0"></i>
                                </div>
                                <h6 class="fw-bold mb-0">Topics Covered</h6>
                            </div>
                            <div class="text-center py-4">
                                <h2 class="fw-black mb-1 display-5">{{ $topics_count }}</h2>
                                <p class="text-muted small mb-0">Unique Subject Areas</p>
                            </div>
                            <p class="text-center text-muted small mt-2">
                                Spanning across various categories of Islamic Jurisprudence.
                            </p>
                        </div>
                    </div>

                    {{-- 3. Appreciation & Reads --}}
                    <div class="col-md-12">
                        <div class="p-4 border rounded-4 bg-dark text-white shadow-lg overflow-hidden position-relative">
                            <div class="position-absolute top-0 end-0 p-3 opacity-10">
                                <i class="bi bi-graph-up-arrow display-1"></i>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-6 border-end border-white border-opacity-10">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-heart-fill text-danger me-2"></i>
                                        <span class="text-uppercase small tracking-widest fw-bold opacity-75">Appreciation / Likes</span>
                                    </div>
                                    <h1 class="fw-black mb-0 font-inter">{{ number_format($likes) }}</h1>
                                    <p class="text-white text-opacity-50 small mb-0">Total user endorsements</p>
                                </div>
                                <div class="col-md-6 ps-md-4 mt-3 mt-md-0">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-eye-fill text-info me-2"></i>
                                        <span class="text-uppercase small tracking-widest fw-bold opacity-75">Public Reads / Views</span>
                                    </div>
                                    <h1 class="fw-black mb-0 font-inter">{{ number_format($views) }}</h1>
                                    <p class="text-white text-opacity-50 small mb-0">Reach across the global platform</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Stats --}}
    <div class="col-lg-4">
        {{-- Active Track --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: linear-gradient(135deg, #1a7a4a 0%, #15613c 100%);">
            <div class="card-body p-4 text-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 opacity-75">Active Track</h6>
                    <div class="spinner-grow spinner-grow-sm text-light opacity-50" role="status"></div>
                </div>
                <h1 class="display-4 fw-black mb-0">{{ $active_track }}</h1>
                <p class="mb-0 text-white-50 small">Currently assigned inquiries</p>
                <div class="mt-4 pt-3 border-top border-white border-opacity-10">
                    <div class="progress bg-white bg-opacity-10" style="height: 6px;">
                        <div class="progress-bar bg-white" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ifta Work Score --}}
        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <span class="badge bg-soft-secondary text-secondary rounded-pill px-3 py-1 mb-2">Efficiency & Speed Index</span>
                    <h5 class="fw-bold text-dark">Ifta Work Score</h5>
                </div>
                
                <div class="position-relative d-flex justify-content-center align-items-center mb-4" style="height: 180px;">
                    <div class="work-score-circle border border-5 border-success rounded-circle d-flex flex-column justify-content-center align-items-center shadow-lg" 
                         style="width: 150px; height: 150px; background: #fff;">
                        <span class="display-5 fw-black text-dark mb-0">{{ $work_score }}</span>
                        <span class="text-muted small fw-bold">POINTS</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-sm bg-light text-success rounded-3 p-2 me-3">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold">Speed Index</span>
                                <span class="small text-muted">{{ $avg_days }} Days Avg</span>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: {{ max(10, 100 - ($avg_days * 10)) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mt-3">
                        <div class="icon-sm bg-light text-primary rounded-3 p-2 me-3">
                            <i class="bi bi-stack"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold">Volume Output</span>
                                <span class="small text-muted">{{ $stats['answered'] }} Total</span>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: {{ min(100, $stats['answered'] * 2) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 p-3 rounded-4 bg-light text-center">
                    <p class="text-muted small mb-0">
                        This index measures consistency, submission speed, and scholarly impact.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-success { background-color: rgba(26, 122, 74, 0.1); }
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1); }
    
    .tracking-widest { letter-spacing: 0.1em; }
    .fw-black { font-weight: 900; }
    
    .font-inter { font-family: 'Inter', sans-serif; }
    
    .work-score-circle {
        transition: transform 0.3s ease;
    }
    .work-score-circle:hover {
        transform: scale(1.05);
    }
    
    .border-dashed { border-style: dashed !important; }
</style>
@endsection
