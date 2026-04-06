@extends('layouts.admin')
@section('title', 'Dashboard')
@section('topbar-title', 'Dashboard Overview')
@section('content')

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    @foreach([
        ['Total Questions', $stats['total'], 'bi-question-circle', 'linear-gradient(135deg,#1a7a4a,#145e38)'],
        ['Pending', $stats['pending'], 'bi-clock-history', 'linear-gradient(135deg,#fd7e14,#e8610a)'],
        ['Assigned', $stats['assigned'], 'bi-person-check', 'linear-gradient(135deg,#0dcaf0,#0aa5c5)'],
        ['Answered', $stats['answered'], 'bi-chat-check', 'linear-gradient(135deg,#0d6efd,#0a58ca)'],
        ['Published Fatwas', $stats['published'], 'bi-patch-check', 'linear-gradient(135deg,#c9a84c,#a8883a)'],
    ] as [$label, $val, $icon, $bg])
    <div class="col-6 col-xl">
        <div class="stat-card shadow-sm" style="background:{{ $bg }};">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="opacity-75 small mb-1">{{ $label }}</div>
                    <div class="fs-3 fw-bold">{{ number_format($val) }}</div>
                </div>
                <i class="bi {{ $icon }}" style="font-size:2rem; opacity:.4;"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Questions --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Recent Questions</h6>
        <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-success btn-sm">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>ID</th><th>Questioner</th><th>Question</th><th>Madhab</th><th>Status</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($recentQuestions as $q)
                <tr>
                    <td><span class="badge bg-secondary">{{ $q->question_id }}</span></td>
                    <td>{{ $q->user->name }}</td>
                    <td>{{ Str::limit($q->body, 60) }}</td>
                    <td>{{ $q->madhab == 'Shafii' ? "Shafi'i" : $q->madhab }}</td>
                    <td><span class="badge badge-status-{{ $q->status }}">{{ ucfirst($q->status) }}</span></td>
                    <td><small class="text-muted">{{ $q->created_at->format('d M') }}</small></td>
                    <td><a href="{{ route('admin.questions.show', $q) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No questions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
