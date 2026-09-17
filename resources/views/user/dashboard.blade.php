@extends('layouts.public')
@section('title', 'My Inquiry Dashboard — Darul Ifta Kauzariyya')

@section('content')

<style>
    /* ── Mobile-First Responsive Design System ── */
    :root {
        --dash-primary: #052961;
        --dash-primary-dark: #031b40;
        --dash-gold: #c9a84c;
        --dash-gold-light: #fdfaf3;
        --dash-surface: #ffffff;
        --dash-bg: #f8fafc;
        --dash-border: #e2e8f0;
        --dash-text: #0f172a;
        --dash-muted: #64748b;
    }

    body {
        background-color: var(--dash-bg);
    }

    .user-dashboard-canvas {
        min-height: 85vh;
        padding-top: 1.25rem;
        padding-bottom: 4rem;
    }

    @media (min-width: 768px) {
        .user-dashboard-canvas {
            padding-top: 2rem;
            padding-bottom: 5rem;
        }
    }

    /* ── Hero Profile Banner ── */
    .user-hero-card {
        background: linear-gradient(135deg, #052961 0%, #0d3b82 60%, #1e4b9c 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        color: #ffffff;
        box-shadow: 0 12px 36px rgba(5, 41, 97, 0.12);
        margin-bottom: 1.5rem;
    }

    .user-hero-pattern {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: radial-gradient(rgba(201, 168, 76, 0.12) 1px, transparent 1px);
        background-size: 20px 20px;
        opacity: 0.8;
        pointer-events: none;
    }

    .user-hero-inner {
        position: relative;
        z-index: 2;
        padding: 1.5rem 1.25rem;
    }

    @media (min-width: 768px) {
        .user-hero-card {
            border-radius: 24px;
            margin-bottom: 2rem;
        }
        .user-hero-inner {
            padding: 2.25rem 2.5rem;
        }
    }

    .user-avatar-container {
        position: relative;
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: #ffffff;
        padding: 3px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        flex-shrink: 0;
    }

    @media (min-width: 768px) {
        .user-avatar-container {
            width: 100px;
            height: 100px;
            border-radius: 24px;
        }
    }

    .user-avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 17px;
        object-fit: cover;
    }

    @media (min-width: 768px) {
        .user-avatar-img {
            border-radius: 21px;
        }
    }

    .online-indicator {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 16px;
        height: 16px;
        background: #10b981;
        border: 3px solid #ffffff;
        border-radius: 50%;
    }

    .user-hero-name {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        font-weight: 800;
        font-size: 1.35rem;
        line-height: 1.2;
        margin-bottom: 0.25rem;
        color: #ffffff;
    }

    @media (min-width: 768px) {
        .user-hero-name {
            font-size: 1.75rem;
        }
    }

    .user-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(201, 168, 76, 0.2);
        border: 1px solid rgba(201, 168, 76, 0.4);
        color: #fce7b2;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 9999px;
        letter-spacing: 0.3px;
    }

    .user-hero-meta {
        color: rgba(255, 255, 255, 0.75);
        font-size: 0.82rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem 1rem;
    }

    .btn-new-inquiry {
        background: linear-gradient(135deg, #c9a84c 0%, #e5c368 100%);
        color: #052961;
        font-weight: 800;
        font-size: 0.88rem;
        padding: 0.65rem 1.4rem;
        border-radius: 9999px;
        border: none;
        box-shadow: 0 4px 18px rgba(201, 168, 76, 0.35);
        transition: all 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
        width: 100%;
    }

    @media (min-width: 576px) {
        .btn-new-inquiry {
            width: auto;
        }
    }

    .btn-new-inquiry:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(201, 168, 76, 0.45);
        color: #031b40;
    }

    /* ── Bento KPI Stat Cards ── */
    .bento-user-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 18px;
        padding: 1.25rem 1.25rem;
        transition: all 0.25s ease;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
    }

    .bento-user-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        border-color: #cbd5e1;
    }

    .bento-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .bento-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 0.2rem;
    }

    .bento-num {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    /* ── Mobile-First Segmented Navigation Tabs ── */
    .dashboard-nav-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .dashboard-nav-container::-webkit-scrollbar {
        display: none;
    }

    .dashboard-nav-pills {
        display: inline-flex;
        background: #e2e8f0;
        padding: 4px;
        border-radius: 14px;
        gap: 4px;
        min-width: 100%;
    }

    @media (min-width: 576px) {
        .dashboard-nav-pills {
            min-width: auto;
        }
    }

    .dashboard-nav-pills .nav-link {
        border-radius: 11px;
        padding: 0.55rem 1.15rem;
        font-weight: 700;
        font-size: 0.85rem;
        color: #475569;
        border: none;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        white-space: nowrap;
        flex: 1;
        transition: all 0.2s ease;
    }

    .dashboard-nav-pills .nav-link.active {
        background: #ffffff;
        color: #052961;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .dashboard-nav-pills .nav-link .badge-count {
        background: rgba(5, 41, 97, 0.08);
        color: #052961;
        font-size: 0.72rem;
        padding: 2px 7px;
        border-radius: 9999px;
    }

    .dashboard-nav-pills .nav-link.active .badge-count {
        background: #052961;
        color: #ffffff;
    }

    /* ── Inquiries Cards Showcase ── */
    .inquiry-feed-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.85);
        border-radius: 18px;
        padding: 1.25rem;
        transition: all 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        position: relative;
    }

    .inquiry-feed-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(5, 41, 97, 0.06);
        border-color: rgba(5, 41, 97, 0.2);
    }

    .inquiry-code-tag {
        font-family: 'Inter', monospace;
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
        background: #f1f5f9;
        padding: 3px 8px;
        border-radius: 6px;
    }

    .inquiry-status-chip {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 4px 10px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .inquiry-title {
        font-family: 'Noto Serif Malayalam', 'Inter', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        line-height: 1.45;
        color: #0f172a;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .inquiry-body-snippet {
        font-family: 'Noto Serif Malayalam', 'Inter', sans-serif;
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.5;
        margin-bottom: 1.25rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .btn-view-discussion {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #052961;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 0.55rem 1rem;
        border-radius: 12px;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        width: 100%;
    }

    .btn-view-discussion:hover {
        background: #052961;
        color: #ffffff;
        border-color: #052961;
    }

    /* ── Content Card (Profile & Security) ── */
    .dashboard-panel-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 22px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
    }

    @media (min-width: 768px) {
        .dashboard-panel-card {
            padding: 2.25rem;
        }
    }

    .panel-section-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0.35rem;
    }

    .form-floating-clean .form-control,
    .form-floating-clean .form-select {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
        font-size: 0.92rem;
        font-weight: 500;
        color: #0f172a;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }

    .form-floating-clean .form-control:focus,
    .form-floating-clean .form-select:focus {
        border-color: #052961;
        box-shadow: 0 0 0 4px rgba(5, 41, 97, 0.08);
    }

    .form-floating-clean label {
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 1rem 0.85rem;
    }

    /* Interactive Avatar Uploader */
    .avatar-dropzone {
        width: 90px;
        height: 90px;
        border-radius: 22px;
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        position: relative;
        cursor: pointer;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .avatar-dropzone:hover {
        border-color: #052961;
        background: #f1f5f9;
    }

    .avatar-dropzone-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-overlay-btn {
        position: absolute;
        inset: 0;
        background: rgba(5, 41, 97, 0.6);
        color: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 700;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .avatar-dropzone:hover .avatar-overlay-btn {
        opacity: 1;
    }
</style>

<div class="user-dashboard-canvas">
    <div class="container" style="max-width: 1140px;">

        {{-- 1. Hero Identity Banner --}}
        <div class="user-hero-card">
            <div class="user-hero-pattern"></div>
            <div class="user-hero-inner">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="user-avatar-container">
                            <img src="{{ Auth::user()->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=052961&color=fff&size=200' }}" 
                                 class="user-avatar-img" 
                                 alt="{{ Auth::user()->name }}"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=052961&color=fff&size=200'">
                            <span class="online-indicator" title="Active"></span>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <h1 class="user-hero-name mb-0">{{ Auth::user()->name }}</h1>
                                <span class="user-hero-badge">
                                    <i class="bi bi-shield-check"></i> Registered Inquirer
                                </span>
                            </div>
                            <div class="user-hero-meta">
                                <span><i class="bi bi-geo-alt-fill text-warning me-1"></i>{{ Auth::user()->district ? Auth::user()->district . ', ' : '' }}{{ Auth::user()->country ?: 'Global' }}</span>
                                <span><i class="bi bi-calendar3 me-1"></i>Member since {{ Auth::user()->created_at ? Auth::user()->created_at->format('M Y') : '2026' }}</span>
                                <span><i class="bi bi-envelope me-1"></i>{{ Auth::user()->email }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="w-100 w-md-auto mt-2 mt-md-0 d-flex flex-wrap align-items-center gap-2">
                        <a href="{{ route('user.settings') }}" class="btn btn-outline-light rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" style="border-color: rgba(255,255,255,0.4);">
                            <i class="bi bi-gear-fill"></i>
                            <span>Settings</span>
                        </a>
                        <a href="{{ route('question.create') }}" class="btn-new-inquiry">
                            <i class="bi bi-plus-circle-fill fs-5"></i>
                            <span>Ask New Question</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Flash Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center gap-2 mb-1 fw-bold">
                    <i class="bi bi-exclamation-octagon-fill text-danger fs-5"></i> Please correct the following errors:
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- 3. Bento KPI Statistics --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-4">
                <div class="bento-user-card">
                    <div>
                        <div class="bento-title">Total Inquiries</div>
                        <div class="bento-num">{{ number_format($stats['total']) }}</div>
                        <div class="small text-muted mt-1" style="font-size: 0.72rem;">Submitted to Darul Ifta</div>
                    </div>
                    <div class="bento-icon-wrapper" style="background: rgba(5, 41, 97, 0.08); color: #052961;">
                        <i class="bi bi-journal-text"></i>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4">
                <div class="bento-user-card">
                    <div>
                        <div class="bento-title">Answered & Published</div>
                        <div class="bento-num text-success">{{ number_format($stats['answered']) }}</div>
                        <div class="small text-success fw-semibold mt-1" style="font-size: 0.72rem;">
                            <i class="bi bi-patch-check-fill me-1"></i>Scholarly Fatwa Issued
                        </div>
                    </div>
                    <div class="bento-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="bi bi-award-fill"></i>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4">
                <div class="bento-user-card">
                    <div>
                        <div class="bento-title">Under Review</div>
                        <div class="bento-num text-warning">{{ number_format($stats['pending']) }}</div>
                        <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                            <i class="bi bi-hourglass-split me-1 text-warning"></i>Awaiting Mufti Review
                        </div>
                    </div>
                    <div class="bento-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Mobile-First Navigation Pills --}}
        <div class="dashboard-nav-container">
            <ul class="nav dashboard-nav-pills" id="dashboardTabs" role="tablist">
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link active w-100" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab">
                        <i class="bi bi-chat-square-text-fill"></i>
                        <span>My Inquiries</span>
                        <span class="badge-count">{{ $stats['total'] }}</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile" type="button" role="tab">
                        <i class="bi bi-person-circle"></i>
                        <span>Profile Details</span>
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100" id="settings-tab" data-bs-toggle="pill" data-bs-target="#settings" type="button" role="tab">
                        <i class="bi bi-shield-lock-fill"></i>
                        <span>Security & Access</span>
                    </button>
                </li>
            </ul>
        </div>

        {{-- 5. Tab Panels --}}
        <div class="tab-content" id="dashboardTabsContent">
            
            {{-- ── TAB 1: Inquiries List ── --}}
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
                    <h5 class="fw-800 text-dark mb-0 d-flex align-items-center gap-2">
                        <span>Recent Questions & Discussions</span>
                    </h5>
                    <span class="text-muted small fw-semibold">Showing {{ $questions->count() }} of {{ $questions->total() }} records</span>
                </div>

                <div class="row g-3">
                    @forelse($questions as $question)
                        @php
                            $status = strtolower($question->status ?: 'pending');
                            $statusBadgeClass = 'bg-secondary text-white';
                            $statusIcon = 'bi-info-circle';

                            if ($status === 'published') {
                                $statusBadgeClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                $statusIcon = 'bi-check-circle-fill';
                            } elseif ($status === 'answered') {
                                $statusBadgeClass = 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25';
                                $statusIcon = 'bi-chat-left-dots-fill';
                            } elseif ($status === 'assigned') {
                                $statusBadgeClass = 'bg-info bg-opacity-10 text-info border border-info border-opacity-25';
                                $statusIcon = 'bi-person-badge';
                            } elseif ($status === 'pending') {
                                $statusBadgeClass = 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25';
                                $statusIcon = 'bi-hourglass-split';
                            } elseif ($status === 'rejected') {
                                $statusBadgeClass = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                $statusIcon = 'bi-x-circle-fill';
                            }
                        @endphp
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="inquiry-feed-card">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2.5">
                                        <span class="inquiry-code-tag">#{{ $question->question_id }}</span>
                                        <span class="inquiry-status-chip {{ $statusBadgeClass }}">
                                            <i class="bi {{ $statusIcon }}"></i> {{ ucfirst($status) }}
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-center gap-2 small text-muted mb-2" style="font-size: 0.75rem;">
                                        <span class="fw-semibold text-dark"><i class="bi bi-folder2 text-primary me-1"></i>{{ $question->category->name ?? 'General Fiqh' }}</span>
                                        <span>•</span>
                                        <span><i class="bi bi-calendar3 me-1"></i>{{ $question->created_at ? $question->created_at->format('d M Y') : 'Recent' }}</span>
                                    </div>

                                    <h6 class="inquiry-title">
                                        {{ strip_tags($question->title ?: $question->body) }}
                                    </h6>

                                    <p class="inquiry-body-snippet">
                                        {{ strip_tags($question->body) }}
                                    </p>
                                </div>

                                <div class="pt-2 border-top">
                                    <a href="{{ route('user.question.show', $question->id) }}" class="btn-view-discussion">
                                        <span>View Discussion</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="dashboard-panel-card text-center py-5">
                                <div class="mb-3">
                                    <div class="d-inline-flex p-3 rounded-circle bg-light text-muted">
                                        <i class="bi bi-journal-plus fs-1 text-primary"></i>
                                    </div>
                                </div>
                                <h5 class="fw-800 text-dark mb-1">No Inquiries Submitted Yet</h5>
                                <p class="text-muted small mb-4" style="max-width: 420px; margin: 0 auto;">
                                    Have a question regarding Islamic jurisprudence, prayer, fasting, zakat, or daily rulings? Ask our verified scholars.
                                </p>
                                <a href="{{ route('question.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-plus-circle me-1.5"></i> Ask a Question
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($questions->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $questions->links() }}
                    </div>
                @endif
            </div>

            {{-- ── TAB 2: Profile Details ── --}}
            <div class="tab-pane fade" id="profile" role="tabpanel">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <div class="dashboard-panel-card">
                            <div class="border-bottom pb-3 mb-4">
                                <h5 class="panel-section-title">Personal Profile Information</h5>
                                <p class="text-muted small mb-0">Update your contact information and jurisprudence preference for tailored scholarly answers.</p>
                            </div>

                            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- Avatar Section --}}
                                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                                    <div class="avatar-dropzone" onclick="document.getElementById('avatarInput').click()">
                                        <img id="avatarPreview" 
                                             src="{{ Auth::user()->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=052961&color=fff' }}" 
                                             class="avatar-dropzone-img"
                                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=052961&color=fff'">
                                        <div class="avatar-overlay-btn">
                                            <i class="bi bi-camera-fill fs-5 mb-1"></i>
                                            <span>Upload</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">Profile Photo</h6>
                                        <p class="text-muted small mb-2" style="font-size: 0.78rem;">PNG, JPG or WEBP up to 2MB.</p>
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold" onclick="document.getElementById('avatarInput').click()">
                                            <i class="bi bi-upload me-1"></i> Change Photo
                                        </button>
                                        <input type="file" id="avatarInput" name="avatar_file" class="d-none" accept="image/*" onchange="previewAvatar(this)">
                                    </div>
                                </div>

                                {{-- Details Grid --}}
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <input type="text" name="name" class="form-control" id="floatingName" value="{{ old('name', Auth::user()->name) }}" placeholder="Full Name" required>
                                            <label for="floatingName">Full Name <span class="text-danger">*</span></label>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <input type="text" name="mobile" class="form-control" id="floatingMobile" value="{{ old('mobile', Auth::user()->mobile) }}" placeholder="Mobile Number">
                                            <label for="floatingMobile">Mobile Number</label>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <select name="gender" class="form-select" id="floatingGender">
                                                <option value="Male" {{ Auth::user()->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ Auth::user()->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                            <label for="floatingGender">Gender</label>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <select name="madhab_id" class="form-select" id="floatingMadhab">
                                                <option value="">No Preference (General)</option>
                                                @foreach($madhabs as $m)
                                                    <option value="{{ $m->id }}" {{ Auth::user()->madhab_id == $m->id ? 'selected' : '' }}>
                                                        {{ $m->name }} School (Fiqh)
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="floatingMadhab">Madhab School Preference</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating form-floating-clean">
                                            <input type="text" name="mother_language" class="form-control" id="floatingLang" value="{{ old('mother_language', Auth::user()->mother_language) }}" placeholder="Mother Language">
                                            <label for="floatingLang">Primary Language (e.g. Malayalam, English, Arabic, Urdu)</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Address Fields --}}
                                <div class="border-top pt-3 mb-3">
                                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-geo-alt me-1 text-primary"></i> Location & Address</h6>
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <div class="form-floating form-floating-clean">
                                                <input type="text" name="address_line_1" class="form-control" id="floatingAddr1" value="{{ old('address_line_1', Auth::user()->address_line_1) }}" placeholder="Address 1">
                                                <label for="floatingAddr1">Address Line 1</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-floating form-floating-clean">
                                                <input type="text" name="address_line_2" class="form-control" id="floatingAddr2" value="{{ old('address_line_2', Auth::user()->address_line_2) }}" placeholder="Address 2">
                                                <label for="floatingAddr2">Address Line 2 (Optional)</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-floating form-floating-clean">
                                                <input type="text" name="district" class="form-control" id="floatingDistrict" value="{{ old('district', Auth::user()->district) }}" placeholder="District">
                                                <label for="floatingDistrict">District / City</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-floating form-floating-clean">
                                                <input type="text" name="state" class="form-control" id="floatingState" value="{{ old('state', Auth::user()->state) }}" placeholder="State">
                                                <label for="floatingState">State / Province</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <div class="form-floating form-floating-clean">
                                                <input type="text" name="country" class="form-control" id="floatingCountry" value="{{ old('country', Auth::user()->country) }}" placeholder="Country">
                                                <label for="floatingCountry">Country</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm">
                                        <i class="bi bi-check2-circle me-1.5"></i> Save Profile Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── TAB 3: Security & Access ── --}}
            <div class="tab-pane fade" id="settings" role="tabpanel">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        <div class="dashboard-panel-card">
                            <div class="border-bottom pb-3 mb-4 text-center">
                                <div class="d-inline-flex p-3 rounded-circle bg-light text-primary mb-2">
                                    <i class="bi bi-shield-lock-fill fs-3"></i>
                                </div>
                                <h5 class="panel-section-title">Change Account Password</h5>
                                <p class="text-muted small mb-0">Ensure your account uses a strong password with at least 8 characters.</p>
                            </div>

                            <form action="{{ route('user.password.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <div class="form-floating form-floating-clean">
                                        <input type="password" name="password" class="form-control" id="floatingNewPass" placeholder="New Password" required minlength="8">
                                        <label for="floatingNewPass">New Password</label>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-floating form-floating-clean">
                                        <input type="password" name="password_confirmation" class="form-control" id="floatingConfirmPass" placeholder="Confirm Password" required minlength="8">
                                        <label for="floatingConfirmPass">Confirm New Password</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-dark w-100 py-2.5 fw-bold rounded-pill shadow-sm">
                                    <i class="bi bi-key-fill me-1.5"></i> Update Password
                                </button>
                            </form>

                            <div class="mt-4 p-3 rounded-3 bg-light border">
                                <div class="d-flex align-items-center gap-2 small text-muted">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                    <span>Changing password will maintain your current session safely.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (window.location.hash === '#profile') {
            const profileTab = document.getElementById('profile-tab');
            if (profileTab) {
                profileTab.click();
            }
        } else if (window.location.hash === '#settings' || window.location.hash === '#security') {
            const settingsTab = document.getElementById('settings-tab');
            if (settingsTab) {
                settingsTab.click();
            }
        }
    });
</script>
@endpush
@endsection
