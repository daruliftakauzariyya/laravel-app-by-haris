@extends('layouts.mufti')
@section('title', 'Mufti Dashboard')
@section('topbar-title', 'Dashboard')
@section('content')

<style>
    .metric-card { transition: transform 0.2s; }
    .metric-card:hover { transform: translateY(-3px); }
    
    @media (max-width: 767.98px) {
        .q-mobile-card {
            background: #fff;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--brand-blue);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .q-mobile-id { font-size: 0.75rem; font-weight: 700; color: #64748b; }
        .q-mobile-title { font-size: 0.95rem; font-weight: 700; color: #1e293b; margin: 5px 0; }
        .q-mobile-meta { font-size: 0.8rem; color: #94a3b8; display: flex; justify-content: space-between; align-items: center; }
    }
</style>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 metric-card" style="background:linear-gradient(135deg,#1a7a4a,#145e38); color:#fff;">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="opacity-75 small">Active Track</div><div class="fs-3 fw-bold">{{ $assigned }}</div></div>
                <i class="bi bi-question-circle" style="font-size:2rem; opacity:.4;"></i>
            </div>
            <div class="mt-2 small opacity-75">Currently assigned inquiries</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 metric-card" style="background:linear-gradient(135deg,#295a75,#1e4359); color:#fff;">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="opacity-75 small">Answered</div><div class="fs-3 fw-bold">{{ $answered }}</div></div>
                <i class="bi bi-patch-check" style="font-size:2rem; opacity:.4;"></i>
            </div>
            <div class="mt-2 small opacity-75">Lifetime scholarly contributions</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3 metric-card" style="background:linear-gradient(135deg,#c9a84c,#a8883a); color:#fff;">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="opacity-75 small">Ifta Work Score</div><div class="fs-3 fw-bold">{{ $score }}<span class="fs-6 ms-1">%</span></div></div>
                <i class="bi bi-graph-up-arrow" style="font-size:2rem; opacity:.4;"></i>
            </div>
            <div class="mt-2 small opacity-75">Efficiency, Speed & Volume Index</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Recent Scholarly Assignments</h6>
        <a href="{{ route('mufti.questions') }}" class="btn btn-outline-brand btn-sm px-3 rounded-pill">View Repository</a>
    </div>

    {{-- Desktop Table View --}}
    <div class="table-responsive d-none d-md-block">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Inquiry Details</th>
                    <th>Madhab</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $q)
                <tr>
                    <td class="ps-3"><span class="badge bg-light text-dark border">{{ $q->question_id }}</span></td>
                    <td>
                        <div class="fw-bold text-dark">{{ Str::limit(strip_tags($q->title ?: $q->body), 50) }}</div>
                        <div class="text-muted x-small">{{ Str::limit(strip_tags($q->body), 80) }}</div>
                    </td>
                    <td><span class="text-muted">{{ $q->madhab == 'Shafii' ? "Shafi'i" : $q->madhab }}</span></td>
                    <td><small class="text-muted">{{ $q->created_at->format('d M Y') }}</small></td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2">{{ ucfirst(str_replace('_', ' ', $q->status)) }}</span></td>
                    <td class="text-end pe-3">
                        <a href="{{ route('mufti.questions.show', $q) }}" class="btn btn-sm btn-brand px-3">Review</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No inquiries assigned to your profile yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="d-md-none p-3 bg-light">
        @forelse($recent as $q)
        <div class="q-mobile-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="q-mobile-id">#{{ $q->question_id }}</span>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 x-small">{{ ucfirst($q->status) }}</span>
            </div>
            <div class="q-mobile-title">{{ Str::limit(strip_tags($q->title ?: $q->body), 60) }}</div>
            <p class="text-muted x-small mb-3">{{ Str::limit(strip_tags($q->body), 90) }}</p>
            <div class="q-mobile-meta">
                <span><i class="bi bi-calendar3 me-1"></i>{{ $q->created_at->format('d M') }}</span>
                <a href="{{ route('mufti.questions.show', $q) }}" class="btn btn-sm btn-brand px-4">Take Action</a>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">No assignments found.</div>
        @endforelse
    </div>
</div>
@endsection
