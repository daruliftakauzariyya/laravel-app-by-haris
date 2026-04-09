@extends('layouts.mufti')
@section('title', 'Pending Tasks')
@section('topbar-title', 'Pending Task Repository')
@section('content')

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-primary fw-bold mb-1">Tasks Count</h6>
                        <h3 class="fw-bold mb-0 text-primary">{{ $questions->total() }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-25 rounded-circle p-3 text-primary">
                        <i class="bi bi-stack h4 mb-0"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-3" style="width: 140px;">QU-ID</th>
                    <th>Question Title</th>
                    <th>Category</th>
                    <th>Assigned On</th>
                    <th>Waiting Time</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $q)
                <tr>
                    <td class="ps-3"><span class="badge bg-light text-dark border fw-monospace">{{ $q->question_id }}</span></td>
                    <td>
                        <div class="fw-bold text-dark small">{{ Str::limit(strip_tags($q->title ?: $q->body), 75) }}</div>
                    </td>
                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10">{{ $q->category->name ?? 'Uncategorized' }}</span></td>
                    <td>
                        <div class="small fw-semibold">{{ $q->assigned_at ? $q->assigned_at->format('d M Y') : '—' }}</div>
                        <div class="x-small text-muted">{{ $q->assigned_at ? $q->assigned_at->format('h:i A') : '' }}</div>
                    </td>
                    <td>
                        @if($q->assigned_at)
                            <span class="text-danger fw-bold"><i class="bi bi-hourglass-split me-1"></i>{{ now()->diffForHumans($q->assigned_at, true) }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('mufti.questions.show', $q) }}" class="btn btn-sm btn-brand px-4">
                            <i class="bi bi-pencil-square me-1"></i>Answer Now
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-check2-circle mb-3 d-block text-success opacity-50" style="font-size:3rem;"></i>
                        <h6 class="fw-bold">All caught up!</h6>
                        <p class="mb-0 x-small">No pending questions assigned to you at the moment.</p>
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

@endsection
