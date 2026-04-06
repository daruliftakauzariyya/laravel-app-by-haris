@extends('layouts.mufti')
@section('title', 'Mufti Dashboard')
@section('topbar-title', 'Dashboard')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3" style="background:linear-gradient(135deg,#1a7a4a,#145e38); color:#fff;">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="opacity-75 small">Assigned</div><div class="fs-3 fw-bold">{{ $assigned }}</div></div>
                <i class="bi bi-question-circle" style="font-size:2rem; opacity:.4;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3" style="background:linear-gradient(135deg,#c9a84c,#a8883a); color:#fff;">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="opacity-75 small">Answered</div><div class="fs-3 fw-bold">{{ $answered }}</div></div>
                <i class="bi bi-patch-check" style="font-size:2rem; opacity:.4;"></i>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between">
        <h6 class="mb-0 fw-bold">Recent Assigned Questions</h6>
        <a href="{{ route('mufti.questions') }}" class="btn btn-outline-success btn-sm">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr><th>ID</th><th>Question</th><th>Madhab</th><th>Date</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($recent as $q)
                <tr>
                    <td><span class="badge bg-secondary">{{ $q->question_id }}</span></td>
                    <td>{{ Str::limit($q->body, 70) }}</td>
                    <td>{{ $q->madhab == 'Shafii' ? "Shafi'i" : $q->madhab }}</td>
                    <td><small class="text-muted">{{ $q->created_at->format('d M Y') }}</small></td>
                    <td><span class="badge bg-primary">{{ ucfirst($q->status) }}</span></td>
                    <td><a href="{{ route('mufti.questions.show', $q) }}" class="btn btn-sm btn-outline-primary">Answer</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No questions assigned yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
