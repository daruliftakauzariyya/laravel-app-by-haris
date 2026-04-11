@extends('layouts.mufti')
@section('title', 'Mufti Dashboard')
@section('topbar-title', 'Dashboard')
@section('content')

@php
$scoreColor = $score >= 80 ? '#1a7a4a' : ($score >= 50 ? '#c9a84c' : '#dc3545');
$scoreBg    = $score >= 80 ? 'rgba(26,122,74,0.12)' : ($score >= 50 ? 'rgba(201,168,76,0.12)' : 'rgba(220,53,69,0.12)');
$scoreLabel = $score >= 80 ? 'Excellent' : ($score >= 50 ? 'Good' : 'Needs Improvement');
@endphp

<style>
    :root {
        --brand-blue:      #295a75;
        --brand-blue-dark: #1e3a4a;
        --brand-gold:      #c9a84c;
        --brand-green:     #1a7a4a;
    }

    /* ── Metric Cards ── */
    .kpi-card {
        border-radius: 16px;
        padding: 22px 24px;
        position: relative;
        overflow: hidden;
        transition: transform 0.25s, box-shadow 0.25s;
        border: none;
        color: #fff;
    }
    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,0.15); }
    .kpi-card .kpi-icon {
        position: absolute; right: 20px; top: 50%;
        transform: translateY(-50%);
        font-size: 3.5rem; opacity: 0.12;
    }
    .kpi-label  { font-size: 0.78rem; font-weight: 600; letter-spacing: 0.8px; text-transform: uppercase; opacity: 0.8; }
    .kpi-value  { font-size: 2.6rem; font-weight: 800; line-height: 1.1; margin: 4px 0; }
    .kpi-sub    { font-size: 0.78rem; opacity: 0.7; }

    /* ── Score Ring ── */
    .score-ring-wrap {
        display: flex; align-items: center; gap: 18px;
    }
    .score-ring {
        width: 72px; height: 72px;
        border-radius: 50%;
        background: conic-gradient({{ $scoreColor }} {{ $score }}%, #e9ecef {{ $score }}%);
        display: flex; align-items: center; justify-content: center;
        position: relative; flex-shrink: 0;
    }
    .score-ring::after {
        content: ''; position: absolute;
        width: 56px; height: 56px;
        background: #fff; border-radius: 50%;
    }
    .score-ring-inner { position: relative; z-index: 1; font-weight: 800; font-size: 0.95rem; color: {{ $scoreColor }}; }

    /* ── Table ── */
    .data-table thead th {
        font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.8px;
        color: #64748b; background: #f8fafc; border: none; padding: 12px 14px;
        font-weight: 600;
    }
    .data-table tbody td { padding: 13px 14px; vertical-align: middle; border-color: #f1f5f9; }
    .data-table tbody tr:hover { background: #f8fafc; }

    .status-pill {
        font-size: 0.68rem; font-weight: 700; padding: 4px 10px;
        border-radius: 50px; letter-spacing: 0.5px; text-transform: uppercase;
    }
    .mdh-badge {
        display: inline-block; font-size: 0.7rem; font-weight: 700;
        padding: 3px 9px; border-radius: 20px;
        background: rgba(41,90,117,0.09); color: var(--brand-blue);
        border: 1px solid rgba(41,90,117,0.15);
    }

    /* ── Mobile Cards ── */
    @media (max-width: 767.98px) {
        .mob-q-card {
            background: #fff; border-radius: 14px; padding: 16px;
            margin-bottom: 14px; border-left: 4px solid var(--brand-blue);
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .mob-q-id   { font-size: 0.72rem; font-weight: 700; color: #64748b; }
        .mob-q-title{ font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 6px 0; }
    }
</style>

{{-- ══════════════════ KPI Row ══════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Active Track --}}
    <div class="col-md-4">
        <div class="kpi-card" style="background: linear-gradient(135deg, var(--brand-green) 0%, #10563a 100%);">
            <div class="kpi-label">Active Track</div>
            <div class="kpi-value">{{ $assigned }}</div>
            <div class="kpi-sub">Currently assigned inquiries</div>
            <i class="bi bi-journal-text kpi-icon"></i>
        </div>
    </div>

    {{-- Answered --}}
    <div class="col-md-4">
        <div class="kpi-card" style="background: linear-gradient(135deg, var(--brand-blue) 0%, var(--brand-blue-dark) 100%);">
            <div class="kpi-label">Fatwa Contributions</div>
            <div class="kpi-value">{{ $answered }}</div>
            <div class="kpi-sub">Lifetime scholarly contributions</div>
            <i class="bi bi-patch-check kpi-icon"></i>
        </div>
    </div>

    {{-- Ifta Score --}}
    <div class="col-md-4">
        <div class="kpi-card d-flex align-items-center gap-3"
             style="background: linear-gradient(135deg, var(--brand-gold) 0%, #a8883a 100%);">
            <div class="flex-grow-1">
                <div class="kpi-label">Ifta Work Score</div>
                <div class="kpi-value">{{ $score }}<span class="fs-5">%</span></div>
                <div class="kpi-sub">{{ $scoreLabel }} · Efficiency & Speed Index</div>
            </div>
            <div class="score-ring flex-shrink-0" style="background: conic-gradient(#fff {{ $score }}%, rgba(255,255,255,0.25) {{ $score }}%); width:64px; height:64px;">
                <div class="score-ring-inner" style="color:#fff; font-size:0.85rem; z-index:1; position:relative;">{{ $score }}%</div>
                <style>.score-ring::after { background: rgba(168,136,58,0.0) !important; }</style>
            </div>
            <i class="bi bi-graph-up-arrow kpi-icon"></i>
        </div>
    </div>

</div>

{{-- ══════════════════ Recent Assignments Table ══════════════════ --}}
<div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0 fw-bold" style="color: var(--brand-blue);">
                <i class="bi bi-clock-history me-2 text-gold"></i>Recent Scholarly Assignments
            </h6>
            <div class="text-muted" style="font-size:0.72rem;">Your last {{ $recent->count() }} assigned questions</div>
        </div>
        <a href="{{ route('mufti.questions') }}" class="btn btn-sm px-4 fw-semibold rounded-pill"
           style="background: var(--brand-blue); color: #fff; font-size: 0.8rem;">
            <i class="bi bi-grid-3x3-gap me-1"></i>View All
        </a>
    </div>

    {{-- Desktop Table --}}
    <div class="table-responsive d-none d-md-block">
        <table class="table data-table mb-0">
            <thead>
                <tr>
                    <th style="width:130px;">Question ID</th>
                    <th>Inquiry Details</th>
                    <th style="width:110px;">Madhab</th>
                    <th style="width:110px;">Date</th>
                    <th style="width:110px;">Status</th>
                    <th style="width:90px;" class="text-end pe-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $q)
                <tr>
                    <td>
                        <span class="badge border fw-semibold"
                              style="background:#f0f7ff; color:var(--brand-blue); font-size:0.72rem; border-color:rgba(41,90,117,0.2)!important;">
                            {{ $q->question_id }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark" style="font-size:0.88rem; line-height:1.4;">
                            {{ Str::limit(strip_tags($q->title ?: $q->body), 55) }}
                        </div>
                        <div class="text-muted mt-1" style="font-size:0.75rem;">
                            {{ Str::limit(strip_tags($q->body), 80) }}
                        </div>
                    </td>
                    <td>
                        <span class="mdh-badge">
                            {{ $q->madhab?->name ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">{{ $q->created_at->format('d M Y') }}</small>
                    </td>
                    <td>
                        @php
                            $st = $q->status;
                            $stStyle = match($st) {
                                'published'  => 'background:#d1fae5; color:#065f46;',
                                'answered'   => 'background:#dbeafe; color:#1e40af;',
                                'assigned'   => 'background:#fef9c3; color:#854d0e;',
                                'rejected'   => 'background:#fee2e2; color:#991b1b;',
                                default      => 'background:#f1f5f9; color:#475569;',
                            };
                        @endphp
                        <span class="status-pill" style="{{ $stStyle }}">{{ ucfirst(str_replace('_', ' ', $st)) }}</span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('mufti.questions.show', $q) }}"
                           class="btn btn-sm px-3 fw-semibold rounded-pill"
                           style="background: var(--brand-blue); color:#fff; font-size:0.75rem;">
                            Review
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                        <span class="text-muted small">No inquiries assigned to your profile yet.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="d-md-none p-3" style="background:#f8fafc;">
        @forelse($recent as $q)
        <div class="mob-q-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="mob-q-id">#{{ $q->question_id }}</span>
                @php $stStyle = match($q->status) {
                    'published' => 'background:#d1fae5; color:#065f46;',
                    'answered'  => 'background:#dbeafe; color:#1e40af;',
                    'assigned'  => 'background:#fef9c3; color:#854d0e;',
                    default     => 'background:#f1f5f9; color:#475569;',
                }; @endphp
                <span class="status-pill" style="{{ $stStyle }}">{{ ucfirst($q->status) }}</span>
            </div>
            <div class="mob-q-title">{{ Str::limit(strip_tags($q->title ?: $q->body), 65) }}</div>
            <div class="text-muted mb-3" style="font-size:0.78rem;">{{ Str::limit(strip_tags($q->body), 90) }}</div>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="mdh-badge">{{ $q->madhab?->name ?? '—' }}</span>
                    <small class="text-muted ms-2">{{ $q->created_at->format('d M') }}</small>
                </div>
                <a href="{{ route('mufti.questions.show', $q) }}"
                   class="btn btn-sm px-4 rounded-pill fw-semibold"
                   style="background: var(--brand-blue); color:#fff; font-size:0.75rem;">
                    Review
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted small">No assignments found.</div>
        @endforelse
    </div>
</div>

@endsection
