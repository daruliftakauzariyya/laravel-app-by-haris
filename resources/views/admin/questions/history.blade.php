@extends('layouts.admin')
@section('title', 'Fatwa History — ' . $question->question_id)
@section('topbar-title', 'Fatwa History & Timeline')
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
    background: linear-gradient(to bottom, #1a7a4a, #c9a84c, #dc3545);
    border-radius: 2px;
    opacity: .25;
}

/* ─── Single Event ───────────────────────────────────── */
.tl-event {
    position: relative;
    margin-bottom: 28px;
}
.tl-icon {
    position: absolute;
    left: -49px;
    top: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    z-index: 1;
}
.tl-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #edf2f7;
    padding: 14px 18px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.05);
    transition: box-shadow 0.2s;
}
.tl-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.08); }
.tl-card .tl-title { font-weight: 700; font-size: 0.92rem; color: #2d3748; margin-bottom: 3px; }
.tl-card .tl-time { font-size: 0.78rem; color: #a0aec0; }
.tl-card .tl-notes { font-size: 0.83rem; color: #718096; margin-top: 6px; background:#f8fafc; padding:7px 10px; border-radius:6px; border-left:3px solid #e2e8f0; }
.tl-card .tl-actor { font-size: 0.78rem; color: #4a5568; margin-top: 5px; }

/* ─── Snapshot row ───────────────────────────────────── */
.snaps-table td, .snaps-table th { font-size: 0.83rem; padding: 8px 14px; }

/* ─── User reads row ─────────────────────────────────── */
.reads-pill { background:#e0f2fe; color:#0369a1; border-radius:20px; padding:3px 12px; font-size:0.8rem; font-weight:700; display:inline-block; }

/* Color helpers */
.bg-ev-secondary { background:#6c757d; }
.bg-ev-success   { background:#1a7a4a; }
.bg-ev-info      { background:#0284c7; }
.bg-ev-primary   { background:#0d6efd; }
.bg-ev-warning   { background:#c9a84c; }
.bg-ev-danger    { background:#dc3545; }
.empty-tl { text-align:center; padding:60px 20px; color:#a0aec0; }
</style>
@endpush

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h5 class="fw-bold mb-1">
            <i class="bi bi-clock-history me-2 text-green"></i>
            Fatwa History — <span class="badge bg-secondary fw-monospace">{{ $question->question_id }}</span>
        </h5>
        <small class="text-muted">Complete lifecycle audit log for this question</small>
    </div>
    <a href="{{ route('admin.questions.show', $question) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Question
    </a>
</div>

<div class="row g-4">

    {{-- ─── Left: Timeline ─────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-diagram-3 me-2"></i>Event Timeline</h6>
            </div>
            <div class="card-body pt-2 pb-4">
                @if($logs->isEmpty())
                <div class="empty-tl">
                    <i class="bi bi-hourglass" style="font-size:2.5rem;"></i>
                    <p class="mt-3 mb-0">No events have been logged yet.<br><small>Events will appear here as the fatwa progresses through its lifecycle.</small></p>
                </div>
                @else
                <div class="tl-wrapper mt-3">
                    @foreach($logs as $log)
                    @php
                        $bgClass = 'bg-ev-' . ($log->color ?? 'secondary');
                    @endphp
                    <div class="tl-event">
                        <div class="tl-icon {{ $bgClass }}">
                            <i class="bi {{ $log->icon }}"></i>
                        </div>
                        <div class="tl-card">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                                <div class="tl-title">{{ $log->event_label }}</div>
                                <div class="tl-time">
                                    <i class="bi bi-clock me-1"></i>{{ $log->event_at->format('d M Y, h:i A') }}
                                    <small class="text-muted">({{ $log->event_at->diffForHumans() }})</small>
                                </div>
                            </div>
                            @if($log->notes)
                            <div class="tl-notes">{{ $log->notes }}</div>
                            @endif
                            @if($log->actor)
                            <div class="tl-actor">
                                <i class="bi bi-person-circle me-1"></i>{{ $log->actor->name }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    {{-- 45-day view snapshots injected into timeline --}}
                    @foreach($snapshots as $snap)
                    <div class="tl-event">
                        <div class="tl-icon" style="background:#6f42c1;">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div class="tl-card">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                                <div class="tl-title">📊 45-Day Read Count Snapshot</div>
                                <div class="tl-time"><i class="bi bi-calendar me-1"></i>{{ \Carbon\Carbon::parse($snap->snapshot_date)->format('d M Y') }}</div>
                            </div>
                            <div class="tl-notes">
                                Total public reads on this date: <strong>{{ number_format($snap->views) }}</strong> reads
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
                <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2"></i>Question Summary</h6>
            </div>
            <div class="card-body py-2">
                @php
                    $rows = [
                        ['Questioner', $question->user->name ?? '—'],
                        ['Question ID', $question->question_id],
                        ['Madhab', $question->madhab?->name ?? '—'],
                        ['Language', $question->language ?? '—'],
                        ['Category', $question->category?->name ?? '—'],
                        ['Status', ucfirst($question->status)],
                        ['Submitted', $question->created_at->format('d M Y, h:i A')],
                    ];
                    if ($question->accepted_at) $rows[] = ['Accepted', $question->accepted_at->format('d M Y, h:i A')];
                    if ($question->assigned_at) $rows[] = ['Assigned', $question->assigned_at->format('d M Y, h:i A')];
                    if ($question->muftiAnswer?->submitted_at) $rows[] = ['Answer Submitted', $question->muftiAnswer->submitted_at->format('d M Y, h:i A')];
                    if ($question->fatwa?->published_at) $rows[] = ['Published', $question->fatwa->published_at->format('d M Y, h:i A')];
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
        @if($question->fatwa)
        @php
            $daysToPublish = $question->created_at->diffInDays($question->fatwa->published_at);
        @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-stopwatch me-2"></i>Processing Times</h6>
            </div>
            <div class="card-body py-2">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <small class="text-muted">Days to Publish</small>
                    <span class="badge {{ $daysToPublish <= 3 ? 'bg-success' : ($daysToPublish <= 7 ? 'bg-warning text-dark' : 'bg-danger') }}">
                        {{ $daysToPublish }} days
                    </span>
                </div>
                @if($question->assigned_at && $question->muftiAnswer?->submitted_at)
                @php $muftiDays = $question->assigned_at->diffInDays($question->muftiAnswer->submitted_at); @endphp
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <small class="text-muted">Mufti Response Time</small>
                    <span class="badge {{ $muftiDays <= 2 ? 'bg-success' : ($muftiDays <= 5 ? 'bg-warning text-dark' : 'bg-danger') }}">{{ $muftiDays }} days</span>
                </div>
                @endif
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <small class="text-muted">Total Public Reads</small>
                    <span class="badge bg-info text-dark">{{ number_format($question->fatwa->views) }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <small class="text-muted">Published by</small>
                    <small class="fw-semibold">{{ $question->fatwa->publisher?->name ?? '—' }}</small>
                </div>
            </div>
        </div>
        @endif

        {{-- Questioner Read Status --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-eye me-2"></i>Questioner Reads</h6>
            </div>
            <div class="card-body">
                @if($userViews)
                    <div class="text-center py-2">
                        <div class="reads-pill mb-2">{{ $userViews->views }}× opened</div>
                        <small class="text-muted d-block">{{ $question->user->name }} has read their fatwa {{ $userViews->views }} time(s)</small>
                        <small class="text-muted d-block mt-1">Last updated: {{ $userViews->updated_at->format('d M Y') }}</small>
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-eye-slash" style="font-size:1.5rem;"></i>
                        <p class="small mt-2 mb-0">Questioner has not yet read<br>their published fatwa.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- 45-day Read Snapshots Table --}}
        @if(count($snapshots) > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-graph-up me-2"></i>Read Count History (45-day)</h6>
            </div>
            <div class="card-body p-0">
                <table class="table snaps-table mb-0">
                    <thead class="table-light">
                        <tr><th>Date</th><th class="text-end">Total Reads</th></tr>
                    </thead>
                    <tbody>
                        @foreach($snapshots as $snap)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($snap->snapshot_date)->format('d M Y') }}</td>
                            <td class="text-end"><strong>{{ number_format($snap->views) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Scholarly Feedback / Admin Messaging --}}
@auth
@if(Auth::user()->hasRole(['admin', 'mufti']))
<div class="card border-0 shadow-sm mt-4 no-print">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-chat-left-dots me-2 text-warning"></i>
            Internal Scholarly Feedback
        </h6>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ Auth::user()->hasRole('admin')
            ? route('admin.history.comment', $question)
            : route('mufti.questions.history.comment', $question) }}" method="POST">
            @csrf
            <div class="mb-3">
                <textarea name="comment" rows="3" class="form-control" placeholder="Add a private technical note or message for the Admin..." required></textarea>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-brand btn-sm px-4 rounded-pill">
                    <i class="bi bi-send me-2"></i>Send Internal Message
                </button>
            </div>
        </form>
        <div class="mt-3 x-small text-muted">
            <i class="bi bi-info-circle me-1"></i>These comments are internal and strictly invisible to the public and the general questioner.
        </div>
    </div>
</div>
@endif
@endauth
@endsection
