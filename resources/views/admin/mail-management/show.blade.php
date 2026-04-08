@extends('layouts.admin')

@section('title', 'Pending Details — ' . $mufti->name)
@section('topbar-title', 'Mufti Pending Work')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-4">
        <a href="{{ route('admin.mail-management.index') }}" class="btn btn-sm btn-link text-decoration-none text-muted p-0 mb-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Mailing Management
        </a>
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-brand-light text-brand fw-bold fs-3 shadow-sm border border-brand-subtle" style="width: 54px; height: 54px;">
                    {{ substr($mufti->name, 0, 1) }}
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-dark">{{ $mufti->name }}</h4>
                    <p class="text-muted small mb-0"><i class="bi bi-envelope me-1"></i>{{ $mufti->email }}</p>
                </div>
            </div>
            
            @if($pendingQuestions->isNotEmpty())
                <form action="{{ route('admin.mail-management.send', $mufti->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-brand px-4 py-2 shadow" style="border-radius: 8px;">
                        <i class="bi bi-send-fill me-2 fs-5"></i> Send Reminder Email
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-list-task me-2 text-gold"></i>Pending Question Details</h5>
                    <span class="badge bg-danger rounded-pill px-3">{{ $pendingQuestions->count() }} Tasks</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 small fw-bold text-muted text-uppercase">QU-ID</th>
                                    <th class="py-3 small fw-bold text-muted text-uppercase">Question Title</th>
                                    <th class="py-3 small fw-bold text-muted text-uppercase">Assigned On</th>
                                    <th class="py-3 small fw-bold text-muted text-uppercase text-center">Waiting Time</th>
                                    <th class="pe-4 py-3 small fw-bold text-muted text-uppercase text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingQuestions as $question)
                                @php
                                    $daysWaiting = $question->assigned_at ? (int) $question->assigned_at->diffInDays(now()) : 0;
                                    $waitColor = $daysWaiting > 7 ? 'text-danger' : ($daysWaiting > 3 ? 'text-warning' : 'text-success');
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <span class="qu-id">{{ $question->question_id }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ Str::limit($question->title ?: 'No Title Provided', 80) }}</div>
                                        @if($question->category)
                                            <small class="text-muted"><i class="bi bi-tag me-1 small"></i>{{ $question->category->name_en }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="small">{{ $question->assigned_at?->format('d M, Y') ?? '—' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold {{ $waitColor }}" style="font-size: 0.95rem;">
                                            {{ $daysWaiting }} Days
                                        </div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('admin.questions.show', $question->id) }}" target="_blank" class="btn btn-sm btn-light border px-3">
                                            Open <i class="bi bi-box-arrow-up-right small ms-1"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-5 text-center text-muted">
                                        <div class="opacity-25 mb-3">
                                            <i class="bi bi-check2-circle display-4"></i>
                                        </div>
                                        This Mufti has no pending questions.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-brand-light { background-color: rgba(41, 90, 117, 0.08); }
    .text-brand { color: var(--brand-blue); }
    .text-gold { color: var(--brand-gold); }
    .btn-brand { background-color: var(--brand-blue); color: #fff; border: none; }
    .btn-brand:hover { background-color: var(--brand-blue-dark); color: #fff; }
    .qu-id { font-family: 'Inter', monospace; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; color: var(--brand-blue); font-weight: 700; }
</style>
@endsection
