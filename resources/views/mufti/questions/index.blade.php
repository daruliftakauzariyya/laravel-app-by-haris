@extends('layouts.mufti')
@section('title', 'My Scholarly Work')
@section('topbar-title', 'Work Repository')
@section('content')

@push('styles')
<style>
    .work-tabs { border-bottom: 2px solid #edf2f7; margin-bottom: 28px; }
    .work-tabs .nav-link {
        border: none; color: #64748b; font-weight: 600;
        padding: 12px 24px; border-radius: 0; position: relative; transition: 0.2s;
    }
    .work-tabs .nav-link.active { color: #1a7a4a; background: transparent; }
    .work-tabs .nav-link.active::after {
        content: ''; position: absolute; bottom: -2px; left: 0; right: 0;
        height: 2.5px; background: #1a7a4a; border-radius: 2px;
    }
    .work-tabs .nav-link:hover { color: #1a7a4a; }
    .work-tabs .tab-count {
        font-size: 0.7rem; background: #e2e8f0; color: #64748b;
        border-radius: 50px; padding: 1px 8px; margin-left: 5px; font-weight: 700;
    }
    .work-tabs .nav-link.active .tab-count { background: #d1fae5; color: #1a7a4a; }

    /* Mobile Cards */
    @media (max-width: 767.98px) {
        .work-card {
            background: #fff; border-radius: 14px; padding: 18px; margin-bottom: 14px;
            border-left: 4px solid #1a7a4a;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .work-card[data-tab="verified"] { border-left-color: #2c5f8a; }
        .work-card[data-tab="approved"] { border-left-color: #c9a84c; }
        .work-card-id { font-size: 0.72rem; font-weight: 800; color: #94a3b8; }
        .work-card-title { font-size: 0.97rem; font-weight: 700; color: #1e293b; margin: 6px 0 4px; }
        .work-card-meta { font-size: 0.78rem; color: #64748b; display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
    }
</style>
@endpush

{{-- Tab Navigation --}}
<ul class="nav work-tabs">
    <li class="nav-item">
        <a class="nav-link {{ $tab == 'assigned' ? 'active' : '' }}"
           href="{{ route('mufti.questions', ['tab' => 'assigned']) }}">
            <i class="bi bi-journal-text me-1"></i>Assigned
            <span class="tab-count">{{ $counts['assigned'] }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab == 'verified' ? 'active' : '' }}"
           href="{{ route('mufti.questions', ['tab' => 'verified']) }}">
            <i class="bi bi-shield-check me-1"></i>Verified by Me
            <span class="tab-count">{{ $counts['verified'] }}</span>
        </a>
    </li>
    @if(Auth::user()->can_check_approve)
    <li class="nav-item">
        <a class="nav-link {{ $tab == 'approved' ? 'active' : '' }}"
           href="{{ route('mufti.questions', ['tab' => 'approved']) }}">
            <i class="bi bi-patch-check me-1"></i>Approved by Me
            <span class="tab-count">{{ $counts['approved'] }}</span>
        </a>
    </li>
    @endif
</ul>

{{-- Desktop Table --}}
<div class="card border-0 shadow-sm overflow-hidden d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Questioner</th>
                    <th>Inquiry</th>
                    <th>Madhab</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $q)
                <tr>
                    <td class="ps-3"><span class="badge bg-light text-dark border fw-monospace">{{ $q->question_id }}</span></td>
                    <td>
                        <div class="fw-bold small">{{ $q->user->name }}</div>
                        <div class="x-small text-muted">{{ $q->user->country ?? '—' }}</div>
                    </td>
                    <td>
                        <div class="fw-bold text-dark small">{{ Str::limit(strip_tags($q->title ?: $q->body), 55) }}</div>
                        <div class="x-small text-muted">{{ Str::limit(strip_tags($q->body), 80) }}</div>
                    </td>
                    <td><small class="text-muted">{{ $q->madhab?->name ?? '—' }}</small></td>
                    <td><small class="text-muted">{{ $q->created_at->format('d M Y') }}</small></td>
                    <td>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2">
                            {{ ucfirst(str_replace('_', ' ', $q->status)) }}
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        @if($tab == 'assigned')
                            <a href="{{ route('mufti.questions.show', $q) }}" class="btn btn-sm btn-brand px-3">
                                 {{ ($q->answer || $q->draft_answer) ? 'Edit' : 'Answer' }}
                            </a>
                        @else
                            <a href="{{ route('mufti.questions.history', $q) }}" class="btn btn-sm btn-outline-brand px-3">
                                <i class="bi bi-clock-history me-1"></i>History
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox" style="font-size:2rem; opacity:.3;"></i>
                        <p class="mt-2 mb-0 small">No inquiries in this category.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($questions->hasPages())
    <div class="card-footer bg-white d-flex justify-content-center py-3">
        {{ $questions->links() }}
    </div>
    @endif
</div>

{{-- Mobile Cards --}}
<div class="d-md-none">
    @forelse($questions as $q)
    <div class="work-card" data-tab="{{ $tab }}">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="work-card-id">{{ $q->question_id }}</span>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 x-small">
                {{ ucfirst(str_replace('_', ' ', $q->status)) }}
            </span>
        </div>
        <div class="work-card-title">{{ Str::limit(strip_tags($q->title ?: $q->body), 70) }}</div>
        <div class="work-card-meta">
            <span><i class="bi bi-person me-1"></i>{{ $q->user->name }}</span>
            <span><i class="bi bi-calendar3 me-1"></i>{{ $q->created_at->format('d M Y') }}</span>
        </div>
        @if($tab == 'assigned')
            <a href="{{ route('mufti.questions.show', $q) }}" class="btn btn-brand btn-sm w-100">
                <i class="bi bi-pencil-square me-2"></i>{{ ($q->answer || $q->draft_answer) ? 'Edit Answer' : 'Submit Answer' }}
            </a>
        @else
            <a href="{{ route('mufti.questions.history', $q) }}" class="btn btn-outline-brand btn-sm w-100">
                <i class="bi bi-clock-history me-2"></i>View History & Add Comment
            </a>
        @endif
    </div>
    @empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox" style="font-size:2.5rem; opacity:.3;"></i>
        <p class="mt-3 mb-0 small">No inquiries found.</p>
    </div>
    @endforelse

    @if($questions->hasPages())
    <div class="d-flex justify-content-center mt-3">{{ $questions->links() }}</div>
    @endif
</div>

@endsection
