@extends('layouts.admin')

@section('title', 'Mailing Management')
@section('topbar-title', 'Mailing Management')
@section('topbar-icon', 'bi-envelope-paper-fill')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
    <span class="text-dark fw-semibold">Mailing Management</span>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card overflow-hidden">
                <div class="p-3.5 px-4 bg-white border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-envelope-paper me-2 text-primary"></i>Mufti Notification Control</h5>
                        <p class="text-muted small mb-0">Monitor pending tasks and manually trigger reminder emails to scholars.</p>
                    </div>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="modern-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Scholar Faculty</th>
                                    <th>Email Address</th>
                                    <th class="text-center">Awaiting Answer</th>
                                    <th class="text-center">Status</th>
                                    <th class="pe-4 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($muftis as $mufti)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-monogram" style="background: #EFF6FF; color: #2563EB; width: 40px; height: 40px;">
                                                {{ substr($mufti->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $mufti->name }}</div>
                                                <small class="text-muted">Mufti Scholar</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $mufti->email }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($mufti->pending_count > 0)
                                            <span class="badge rounded-pill" style="background: #FEE2E2; color: #DC2626; padding: 5px 12px; font-weight: 700; font-size: 0.82rem;">
                                                {{ $mufti->pending_count }} Awaiting
                                            </span>
                                        @else
                                            <span class="badge rounded-pill" style="background: #ECFDF5; color: #059669; padding: 5px 12px; font-weight: 700; font-size: 0.82rem;">
                                                0 Awaiting
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($mufti->is_active)
                                            <span class="status-badge status-published">● Active</span>
                                        @else
                                            <span class="status-badge status-rejected">● Inactive</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.mail-management.show', $mufti->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.8rem;">
                                                <i class="bi bi-eye me-1"></i> View Details
                                            </a>
                                            @if($mufti->pending_count > 0)
                                                <form action="{{ route('admin.mail-management.send', $mufti->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-modern-primary py-1 px-3" onclick="return confirm('Trigger a reminder email to {{ $mufti->name }} immediately?')">
                                                        <i class="bi bi-send-fill me-1 small"></i> Send Mail
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-sm btn-light px-3 text-muted border rounded-pill" disabled style="font-size: 0.8rem;">
                                                    <i class="bi bi-send me-1 small"></i> Send Mail
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-5 text-center text-muted">
                                        <i class="bi bi-person-x display-4 opacity-25 d-block mb-3"></i>
                                        No active Muftis found in the database.
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

    {{-- Global Stats --}}
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid var(--brand-blue) !important;">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold mb-3">Active Scholars</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="mb-0 fw-bold">{{ $muftis->count() }}</h3>
                        <div class="bg-brand-light p-2 rounded text-brand"><i class="bi bi-people-fill fs-4"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid var(--brand-gold) !important;">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold mb-3">Total Pending Tasks</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="mb-0 fw-bold">{{ $muftis->sum('pending_count') }}</h3>
                        <div class="bg-gold-light p-2 rounded text-gold"><i class="bi bi-hourglass-split fs-4"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #10b981 !important;">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase fw-bold mb-3">System Logic</h6>
                    <p class="small text-muted mb-0">Emails are automatically sent every 3 days by the system cron.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-brand-light { background-color: rgba(41, 90, 117, 0.08); }
    .bg-gold-light { background-color: rgba(168, 136, 58, 0.08); }
    .btn-outline-brand { border-color: var(--brand-blue); color: var(--brand-blue); }
    .btn-outline-brand:hover { background-color: var(--brand-blue); color: #fff; }
    .btn-brand { background-color: var(--brand-blue); color: #fff; border: none; }
    .btn-brand:hover { background-color: var(--brand-blue-dark); color: #fff; }
    .text-brand { color: var(--brand-blue); }
    .text-gold { color: var(--brand-gold); }
</style>
@endsection
