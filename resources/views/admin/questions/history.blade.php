@extends('layouts.admin')
@section('title', 'Fatwa History — ' . $question->question_id)
@section('topbar-title', 'Fatwa History & Audit Trail')
@section('topbar-icon', 'bi-clock-history')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
    <a href="{{ route('admin.questions.index') }}">Questions</a>
    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
    <span class="text-dark fw-semibold">Audit History</span>
@endsection
@section('content')

@push('styles')
<style>
/* ─── Timeline Container ─────────────────────────────── */
.tl-wrapper {
    position: relative;
    padding-left: 60px;
}
.tl-wrapper::before {
    content: '';
    position: absolute;
    left: 22px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #1a7a4a, #c9a84c, #0284c7, #dc3545);
    border-radius: 2px;
    opacity: .35;
}

/* ─── Single Event ───────────────────────────────────── */
.tl-event {
    position: relative;
    margin-bottom: 24px;
}
.tl-icon {
    position: absolute;
    left: -49px;
    top: 0;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    color: #fff;
    box-shadow: 0 3px 10px rgba(0,0,0,0.12);
    z-index: 1;
}
.tl-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #edf2f7;
    padding: 16px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.2s ease-in-out;
}
.tl-card:hover { 
    box-shadow: 0 6px 18px rgba(0,0,0,0.08); 
    border-color: #cbd5e1;
}
.tl-card .tl-title { font-weight: 700; font-size: 0.95rem; color: #1e293b; margin-bottom: 4px; }
.tl-card .tl-time { font-size: 0.78rem; color: #64748b; font-family: monospace; }
.tl-card .tl-notes { 
    font-size: 0.84rem; 
    color: #475569; 
    margin-top: 8px; 
    background: #f8fafc; 
    padding: 10px 14px; 
    border-radius: 8px; 
    border-left: 3px solid #3b82f6; 
    line-height: 1.5;
}
.tl-card .tl-actor { font-size: 0.78rem; color: #475569; margin-top: 6px; }

/* Color helpers */
.bg-ev-secondary { background:#64748b; }
.bg-ev-success   { background:#059669; }
.bg-ev-info      { background:#0284c7; }
.bg-ev-primary   { background:#2563eb; }
.bg-ev-warning   { background:#d97706; }
.bg-ev-danger    { background:#dc2626; }
.empty-tl { text-align:center; padding:60px 20px; color:#a0aec0; }
</style>
@endpush

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h5 class="fw-bold mb-1">
            <i class="bi bi-clock-history me-2 text-primary"></i>
            Fatwa Lifecycle History — <span class="badge bg-primary text-white fw-monospace px-2.5 py-1">{{ $question->question_id }}</span>
        </h5>
        <small class="text-muted">Comprehensive, auditable lifecycle logs from questioner account creation to scholarly fatwa publication and readers reach</small>
    </div>
    <a href="{{ route('admin.questions.show', $question) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Question Workspace
    </a>
</div>

<div class="row g-4">

    {{-- ─── Left: Timeline ─────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-diagram-3 me-2 text-primary"></i>Complete Event Timeline (Reverse Chronological)</h6>
                <span class="badge bg-light text-primary border rounded-pill px-3 py-1">{{ $logs->count() }} Total Milestone Events</span>
            </div>
            <div class="card-body pt-2 pb-4">
                @if($logs->isEmpty())
                <div class="empty-tl">
                    <i class="bi bi-hourglass" style="font-size:2.5rem;"></i>
                    <p class="mt-3 mb-0">No events recorded.<br><small>Events will appear here as the fatwa progresses through its lifecycle.</small></p>
                </div>
                @else
                @php
                    $timelineLogs = $logs->sortByDesc(fn($l) => $l->event_at ?? $l->created_at);
                @endphp
                <div class="tl-wrapper mt-3">
                    @foreach($timelineLogs as $log)
                    @php
                        $bgClass = 'bg-ev-' . ($log->color ?? 'secondary');
                    @endphp
                    <div class="tl-event">
                        <div class="tl-icon {{ $bgClass }}">
                            <i class="bi {{ $log->icon }}"></i>
                        </div>
                        <div class="tl-card">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div class="tl-title d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark border px-2 py-0.5 rounded font-monospace text-[11px] fw-bold">
                                        #{{ $log->reverse_step_number ?? $loop->iteration }}
                                    </span>
                                    <span>{{ $log->event_label }}</span>
                                </div>
                                <div class="tl-time">
                                    <i class="bi bi-clock me-1 text-muted"></i>{{ $log->event_at ? $log->event_at->format('d M Y, h:i:s A') : '—' }}
                                    <small class="text-muted ms-1">({{ $log->event_at ? $log->event_at->diffForHumans() : '' }})</small>
                                </div>
                            </div>
                            @if($log->notes)
                            <div class="tl-notes">
                                <i class="bi bi-info-circle me-1 text-primary"></i>
                                {{ $log->notes }}
                            </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-light x-small text-muted">
                                <div class="tl-actor">
                                    <i class="bi bi-person-circle me-1 text-secondary"></i>
                                    <strong>{{ $log->actor->name ?? ($question->user?->name ?? 'System Action') }}</strong>
                                    @if($log->actor?->country)
                                        <span class="text-secondary">({{ $log->actor->country }})</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="badge bg-light text-secondary border text-[10px] uppercase">{{ str_replace('_', ' ', $log->event ?? 'event') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ─── Right: Info Panels ──────────────────────────────── --}}
    <div class="col-lg-4">

        {{-- Question Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Question Summary</h6>
            </div>
            <div class="card-body py-2">
                @php
                    $acceptedAt = $question->statusHistory->where('new_status', 'accepted')->first()?->created_at;
                    $assignedAt = $question->statusHistory->where('new_status', 'assigned')->first()?->created_at;
                    $submittedAt = $question->statusHistory->where('new_status', 'answered')->first()?->created_at;

                    $rows = [
                        ['Questioner', $question->user->name ?? '—'],
                        ['Questioner Registered', $question->user ? $question->user->created_at->format('d M Y, h:i A') : '—'],
                        ['Question ID', $question->question_id],
                        ['Madhab', $question->madhab?->name == 'Shafii' ? "Shafi'i" : ($question->madhab?->name ?? 'General/Other')],
                        ['Language', $question->language ?? '—'],
                        ['Category', $question->category?->name ?? '—'],
                        ['Status', ucfirst($question->status)],
                        ['Inquiry Posted', $question->created_at->format('d M Y, h:i A')],
                    ];
                    if ($acceptedAt) $rows[] = ['Accepted & Categorized', $acceptedAt->format('d M Y, h:i A')];
                    if ($assignedAt) $rows[] = ['Assigned to Mufti', $assignedAt->format('d M Y, h:i A')];
                    if ($submittedAt) $rows[] = ['Answer Submitted', $submittedAt->format('d M Y, h:i A')];
                    if ($question->published_at) $rows[] = ['Published Globally', $question->published_at->format('d M Y, h:i A')];
                @endphp
                @foreach($rows as [$label, $val])
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <small class="text-muted fw-semibold">{{ $label }}</small>
                    <small class="text-dark fw-semibold text-end">{{ $val }}</small>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Publishing Timeline summary --}}
        @if($question->published_at)
        @php
            $daysToPublish = $question->created_at->diffInDays($question->published_at);
        @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-stopwatch me-2 text-primary"></i>Processing & Engagement</h6>
            </div>
            <div class="card-body py-2">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <small class="text-muted">Total Time to Publish</small>
                    <span class="badge {{ $daysToPublish <= 3 ? 'bg-success' : ($daysToPublish <= 7 ? 'bg-warning text-dark' : 'bg-danger') }}">
                        {{ $daysToPublish }} days
                    </span>
                </div>
                @if($assignedAt && $submittedAt)
                @php $muftiDays = $assignedAt->diffInDays($submittedAt); @endphp
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <small class="text-muted">Scholar Response Time</small>
                    <span class="badge {{ $muftiDays <= 2 ? 'bg-success' : ($muftiDays <= 5 ? 'bg-warning text-dark' : 'bg-danger') }}">{{ $muftiDays }} days</span>
                </div>
                @endif
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <small class="text-muted">Total Public Reads</small>
                    <span class="badge bg-info text-white">{{ number_format($question->views) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <small class="text-muted">Total Likes / Favorites</small>
                    <span class="badge bg-danger text-white">{{ number_format($question->likes) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <small class="text-muted">Published by</small>
                    <small class="fw-semibold">{{ $question->publisher?->name ?? 'Admin' }}</small>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
