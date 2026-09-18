@extends('layouts.admin')

@section('title', 'Scholar Work History — ' . $user->name)
@section('topbar-title', 'Scholar Work History & Activity Timeline')

@section('content')
<div class="mufti-history-hub pb-5">
    {{-- Scholar Profile & Live Timer Ribbon --}}
    <div class="card border-0 shadow-lg mb-4 history-hero-card">
        <div class="card-body p-4 p-lg-5 position-relative text-white">
            <div class="hero-backdrop-pattern"></div>
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4 position-relative z-index-1">
                {{-- Left: Avatar & Scholar Details --}}
                <div class="d-flex align-items-center gap-4">
                    <div class="position-relative">
                        <img src="{{ $user->avatar ? asset($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1a7a4a&color=fff&size=100' }}" 
                             alt="{{ $user->name }}" class="rounded-circle shadow-lg border border-3 border-white border-opacity-25" width="90" height="90" style="object-fit: cover;">
                        @if($isLive)
                            <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle pulse-dot" style="width: 18px; height: 18px;" title="Online Now"></span>
                        @endif
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h2 class="fw-black mb-0 display-6">{{ $user->name }}</h2>
                            @if($isLive)
                                <span class="badge bg-success bg-opacity-80 text-white rounded-pill px-3 py-1 fw-bold fs-7 shadow-xs">
                                    <span class="live-pulse-dot me-1"></span>LIVE ONLINE
                                </span>
                            @else
                                <span class="badge bg-dark bg-opacity-40 text-white-50 rounded-pill px-3 py-1 fs-7">
                                    Offline (Last seen {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never' }})
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center opacity-75 small gap-3 flex-wrap">
                            <span><i class="bi bi-geo-alt-fill me-1"></i>{{ $user->state ? $user->state . ', ' . ($user->country ?? 'India') : ($user->country ?? 'Kerala, India') }}</span>
                            <span><i class="bi bi-envelope-fill me-1"></i>{{ $user->email }}</span>
                            <span><i class="bi bi-book-fill me-1"></i>{{ $user->madhab?->name ?? 'Fiqh Scholar' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Right: Live Session Running Timer & Total Work Time Box --}}
                <div class="d-flex flex-wrap align-items-center gap-3">
                    {{-- Running Timer for Active Session --}}
                    @if($isLive && $user->last_login_at)
                    <div class="timer-box bg-success bg-opacity-25 border border-success border-opacity-50 p-3 rounded-4 text-center shadow-xs">
                        <div class="text-uppercase tracking-wider text-success-light fw-bold x-small mb-1">
                            <i class="bi bi-stopwatch-fill me-1 text-warning"></i>Current Live Session
                        </div>
                        <div class="fs-3 fw-black font-monospace text-white running-session-timer" 
                             data-start-time="{{ $user->last_login_at->timestamp * 1000 }}" 
                             id="liveSessionTimer">
                            00:00:00
                        </div>
                        <div class="x-small opacity-75 mt-1">Logged in @ {{ $user->last_login_at->format('h:i A') }}</div>
                    </div>
                    @endif

                    {{-- Total Calculated Work Time --}}
                    <div class="timer-box bg-white bg-opacity-10 border border-white border-opacity-20 p-3 rounded-4 text-center">
                        <div class="text-uppercase tracking-wider text-white-50 fw-bold x-small mb-1">
                            <i class="bi bi-hourglass-split me-1 text-gold"></i>Total Logged Time
                        </div>
                        <div class="fs-3 fw-black font-monospace text-white">
                            {{ $formattedTotalTime }}
                        </div>
                        <div class="x-small opacity-75 mt-1">{{ number_format($user->login_count ?? 0) }} Total Sessions</div>
                    </div>

                    {{-- Navigation Buttons --}}
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('admin.muftis.activity', $user) }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                            <i class="bi bi-graph-up-arrow me-1 text-primary"></i>Performance View
                        </a>
                        <a href="{{ route('admin.muftis.edit', $user) }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                            <i class="bi bi-pencil-fill me-1"></i>Edit Scholar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Metrics Bar: Quick Counters --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 col-xl-2">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white text-center h-100">
                <span class="text-muted x-small text-uppercase fw-bold">Total Words Authored</span>
                <h3 class="fw-black text-dark mb-0 font-monospace mt-1">{{ number_format($totalWordsAuthored) }}</h3>
                <small class="text-success x-small fw-semibold mt-1">{{ number_format($totalAnswerWords) }} in answers</small>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white text-center h-100">
                <span class="text-muted x-small text-uppercase fw-bold">Fatwas Answered</span>
                <h3 class="fw-black text-primary mb-0 font-monospace mt-1">{{ number_format($counts['answered']) }}</h3>
                <small class="text-muted x-small mt-1">{{ number_format($counts['published']) }} published</small>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white text-center h-100">
                <span class="text-muted x-small text-uppercase fw-bold">Drafts Saved</span>
                <h3 class="fw-black text-warning mb-0 font-monospace mt-1">{{ number_format($counts['drafted']) }}</h3>
                <small class="text-muted x-small mt-1">{{ number_format($totalDraftWords) }} draft words</small>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white text-center h-100">
                <span class="text-muted x-small text-uppercase fw-bold">Inquiries Opened</span>
                <h3 class="fw-black text-info mb-0 font-monospace mt-1">{{ number_format($counts['opened']) }}</h3>
                <small class="text-muted x-small mt-1">Read & Inspected</small>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white text-center h-100">
                <span class="text-muted x-small text-uppercase fw-bold">Reviews & Returns</span>
                <h3 class="fw-black text-danger mb-0 font-monospace mt-1">{{ number_format($counts['reviews']) }}</h3>
                <small class="text-muted x-small mt-1">Returned for review</small>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-white text-center h-100">
                <span class="text-muted x-small text-uppercase fw-bold">Total Logins</span>
                <h3 class="fw-black text-success mb-0 font-monospace mt-1">{{ number_format($user->login_count ?? 0) }}</h3>
                <small class="text-muted x-small mt-1">Across devices</small>
            </div>
        </div>
    </div>

    {{-- Filter, Search & Tab Controls --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <form method="GET" action="{{ route('admin.muftis.history', $user) }}" class="row g-2 align-items-center justify-content-between">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap gap-1">
                    <a href="{{ route('admin.muftis.history', array_merge(['user' => $user->id], request()->except('event', 'page'))) }}" 
                       class="btn btn-sm rounded-pill px-3 {{ !request('event') ? 'btn-dark fw-bold' : 'btn-light text-muted' }}">
                        All History
                    </a>
                    <a href="{{ route('admin.muftis.history', array_merge(['user' => $user->id, 'event' => 'answered'], request()->except('event', 'page'))) }}" 
                       class="btn btn-sm rounded-pill px-3 {{ request('event') === 'answered' ? 'btn-primary fw-bold' : 'btn-light text-muted' }}">
                        <i class="bi bi-check-circle-fill me-1"></i>Answers Submitted
                    </a>
                    <a href="{{ route('admin.muftis.history', array_merge(['user' => $user->id, 'event' => 'drafted'], request()->except('event', 'page'))) }}" 
                       class="btn btn-sm rounded-pill px-3 {{ request('event') === 'drafted' ? 'btn-warning text-dark fw-bold' : 'btn-light text-muted' }}">
                        <i class="bi bi-pencil-square me-1"></i>Drafts Saved
                    </a>
                    <a href="{{ route('admin.muftis.history', array_merge(['user' => $user->id, 'event' => 'opened'], request()->except('event', 'page'))) }}" 
                       class="btn btn-sm rounded-pill px-3 {{ request('event') === 'opened' ? 'btn-info text-dark fw-bold' : 'btn-light text-muted' }}">
                        <i class="bi bi-eye-fill me-1"></i>Opened Inquiries
                    </a>
                    <a href="{{ route('admin.muftis.history', array_merge(['user' => $user->id, 'event' => 'review'], request()->except('event', 'page'))) }}" 
                       class="btn btn-sm rounded-pill px-3 {{ request('event') === 'review' ? 'btn-danger fw-bold' : 'btn-light text-muted' }}">
                        <i class="bi bi-arrow-repeat me-1"></i>Reviews & Corrections
                    </a>
                    <a href="{{ route('admin.muftis.history', array_merge(['user' => $user->id, 'event' => 'logins'], request()->except('event', 'page'))) }}" 
                       class="btn btn-sm rounded-pill px-3 {{ request('event') === 'logins' ? 'btn-success fw-bold' : 'btn-light text-muted' }}">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Logins / Sessions
                    </a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="input-group input-group-sm">
                    @if(request('event'))
                        <input type="hidden" name="event" value="{{ request('event') }}">
                    @endif
                    <input type="text" name="search" class="form-control rounded-start-pill px-3" placeholder="Search fatwa number or keyword..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary rounded-end-pill px-3"><i class="bi bi-search"></i></button>
                    @if(request('search') || request('event'))
                        <a href="{{ route('admin.muftis.history', $user) }}" class="btn btn-outline-secondary rounded-pill ms-1" title="Reset Filters"><i class="bi bi-x"></i></a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Main Content: Unified Work History Feed & Recent Device Sessions --}}
    <div class="row g-4">
        {{-- History Feed Column --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-black mb-1 text-dark">Scholarly Action Feed</h5>
                        <p class="text-muted small mb-0">Detailed timeline fetched directly from institutional fatwa history & activity records</p>
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-monospace">
                        {{ $activities->total() }} Events Recorded
                    </span>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if($activities->count() > 0)
                        <div class="history-timeline">
                            @foreach($activities as $act)
                                @php
                                    $eventDate = $act->event_at ?? $act->created_at;
                                    $icon = $act->icon ?: 'bi-circle-fill';
                                    $color = $act->color ?: 'primary';
                                    $q = $act->questionAndAnswer;
                                    $qid = $q?->question_id ?? ('#' . $act->question_id);
                                @endphp

                                <div class="timeline-item mb-4 pb-3">
                                    <div class="timeline-marker bg-{{ $color }} text-white shadow-xs">
                                        <i class="bi {{ $icon }}"></i>
                                    </div>
                                    <div class="timeline-content p-3 p-md-4 rounded-4 bg-light border shadow-2xs">
                                        {{-- Header: Event Title & Timestamp --}}
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-{{ $color }} bg-opacity-15 text-{{ $color }} rounded-pill px-2 py-1 fw-bold text-uppercase x-small">
                                                    {{ ucfirst(str_replace('_', ' ', $act->event)) }}
                                                </span>
                                                @if($q)
                                                    <a href="{{ route('admin.questions.show', $q) }}" class="fw-800 text-dark text-decoration-none hover-primary">
                                                        Fatwa Number {{ $qid }}
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="text-muted x-small font-monospace">
                                                <i class="bi bi-clock me-1"></i>{{ $eventDate->format('h:i A') }} : {{ $eventDate->format('d - m - Y') }}
                                            </div>
                                        </div>

                                        {{-- Event Description & Context (Matching User Requirements) --}}
                                        <div class="mb-2">
                                            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                                                {{ $act->event_label }}
                                            </h6>
                                            @if($act->notes)
                                                <p class="text-muted small mb-0 lh-base bg-white p-2 rounded-3 border border-light">
                                                    {{ $act->notes }}
                                                </p>
                                            @endif
                                        </div>

                                        {{-- Additional Details: Word counts, Language, Question Title --}}
                                        @if($q)
                                            <div class="d-flex flex-wrap align-items-center gap-3 mt-3 pt-2 border-top border-light x-small text-muted">
                                                @if($q->title)
                                                    <span class="text-truncate" style="max-width: 320px;" title="{{ $q->title }}">
                                                        <strong>Topic:</strong> {{ $q->title }}
                                                    </span>
                                                @endif
                                                @if($q->category)
                                                    <span><i class="bi bi-tag-fill me-1 text-secondary"></i>{{ $q->category->name }}</span>
                                                @endif
                                                @if($q->madhab)
                                                    <span><i class="bi bi-book me-1"></i>{{ $q->madhab->name }}</span>
                                                @endif
                                                <div class="ms-auto d-flex gap-2">
                                                    <a href="{{ route('admin.questions.show', $q) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2">
                                                        View Details
                                                    </a>
                                                    <a href="{{ route('admin.questions.history', $q) }}" class="btn btn-xs btn-outline-secondary rounded-pill px-2">
                                                        Fatwa Audit Log
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ $activities->total() }} records</span>
                            {{ $activities->links() }}
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x display-4 opacity-25 d-block mb-3"></i>
                            <h5>No work history events found</h5>
                            <p class="small">No logs recorded matching the selected filter.</p>
                            <a href="{{ route('admin.muftis.history', $user) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Reset All Filters</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Side Column: Device Session Logs & Scholar Info --}}
        <div class="col-lg-4">
            {{-- Device & Login Session Logs --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-phone-laptop me-2 text-primary"></i>Device & Session Logs</h6>
                    <span class="badge bg-light text-muted border">Recent 15</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($loginLogs as $log)
                            @php
                                $isMobile = str_contains(strtolower($log->device), 'mobile') || str_contains(strtolower($log->device), 'phone') || in_array(strtolower($log->platform), ['android', 'ios']);
                                $devIcon = $isMobile ? 'bi-phone' : 'bi-laptop';
                            @endphp
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="device-badge bg-light border p-1 rounded-3">
                                            <i class="bi {{ $devIcon }} text-primary fs-6"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">{{ $log->device }} ({{ $log->platform ?: 'OS' }})</div>
                                            <div class="text-muted x-small">{{ $log->browser ?: 'Browser' }} · {{ $log->location ?? 'Kerala, India' }}</div>
                                        </div>
                                    </div>
                                    @if($log->is_active && $isLive)
                                        <span class="badge bg-success bg-opacity-15 text-success rounded-pill px-2 py-0 x-small fw-bold">ACTIVE</span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0 x-small">
                                            {{ $log->duration_seconds > 0 ? max(1, round($log->duration_seconds / 60)) . ' mins' : 'Ended' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between text-muted x-small mt-2 pt-1 border-top border-light font-monospace">
                                    <span>In: {{ $log->login_at->format('h:i A : d-m-Y') }}</span>
                                    @if($log->logout_at)
                                        <span>Out: {{ $log->logout_at->format('h:i A') }}</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">
                                <i class="bi bi-shield-check display-6 opacity-25 d-block mb-2"></i>
                                Sessions are logged automatically upon login/logout.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Scholar Overview Box --}}
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-award-fill me-2 text-warning"></i>Scholar Profile Summary</h6>
                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Seniority & Roles</span>
                        <span class="fw-bold text-dark">{{ $user->can_check_approve ? 'Senior Approver & Scholar' : 'Mufti Faculty' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Account Status</span>
                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">{{ $user->is_active ? 'Active' : 'Disabled' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Jurisdiction / Madhab</span>
                        <span class="fw-bold text-dark">{{ $user->madhab?->name ?? 'Standard Fiqh' }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Member Since</span>
                        <span class="fw-bold text-dark">{{ $user->created_at->format('M Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Styles --}}
<style>
    .fw-black { font-weight: 900; }
    .fw-800 { font-weight: 800; }
    .x-small { font-size: 0.75rem; }
    .fs-7 { font-size: 0.8rem; }
    .shadow-xs { box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .shadow-2xs { box-shadow: 0 1px 4px rgba(0,0,0,0.03); }

    .history-hero-card {
        background: linear-gradient(135deg, #163625 0%, #0d2217 50%, #06110b 100%);
        border-radius: 24px;
        overflow: hidden;
    }

    .hero-backdrop-pattern {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-image: radial-gradient(rgba(201, 168, 76, 0.15) 1px, transparent 1px);
        background-size: 20px 20px;
        opacity: 0.4;
        pointer-events: none;
    }

    .timer-box {
        min-width: 150px;
    }

    .live-pulse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: #4ade80;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7);
        animation: live-pulse 1.8s infinite;
    }

    @keyframes live-pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(74, 222, 128, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0); }
    }

    /* History Timeline */
    .history-timeline {
        position: relative;
        padding-left: 20px;
    }

    .history-timeline::before {
        content: '';
        position: absolute;
        top: 15px;
        bottom: 15px;
        left: 20px;
        width: 2px;
        background: #e2e8f0;
    }

    .timeline-item {
        position: relative;
        padding-left: 36px;
    }

    .timeline-marker {
        position: absolute;
        left: 8px;
        top: 12px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        border: 3px solid #fff;
        z-index: 2;
    }
</style>

{{-- Real-time Running Timer JavaScript --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const liveTimerElem = document.getElementById('liveSessionTimer');
        if (liveTimerElem) {
            const startTime = parseInt(liveTimerElem.getAttribute('data-start-time'), 10);
            
            function updateRunningTimer() {
                if (isNaN(startTime)) return;
                const now = Date.now();
                let diffSecs = Math.max(0, Math.floor((now - startTime) / 1000));
                
                const hours = Math.floor(diffSecs / 3600);
                const minutes = Math.floor((diffSecs % 3600) / 60);
                const seconds = diffSecs % 60;
                
                const formatted = 
                    String(hours).padStart(2, '0') + ':' +
                    String(minutes).padStart(2, '0') + ':' +
                    String(seconds).padStart(2, '0');
                
                liveTimerElem.textContent = formatted;
            }

            updateRunningTimer();
            setInterval(updateRunningTimer, 1000);
        }
    });
</script>
@endpush
@endsection
