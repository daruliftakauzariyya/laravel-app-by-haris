@extends('layouts.admin')
@section('title', 'Questioners')
@section('topbar-title', 'Questioner Directory')
@section('topbar-icon', 'bi-people-fill')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
    <span class="text-dark fw-semibold">Questioners</span>
@endsection

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <h5 class="mb-0 fw-bold text-dark">All Registered Questioners</h5>
        <div class="text-muted small mt-0.5">Users who have submitted fatwa inquiries through the portal</div>
    </div>
    <form class="d-flex gap-2 w-100 w-md-auto" method="GET" action="{{ route('admin.questioners.index') }}">
        <div class="position-relative flex-grow-1" style="min-width: 280px;">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.85rem;"></i>
            <input type="text" name="search" class="form-control modern-input ps-5" placeholder="Search by name or email..." value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-modern-primary">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.questioners.index') }}" class="btn btn-modern-secondary" title="Clear">
                <i class="bi bi-x-lg"></i>
            </a>
        @endif
    </form>
</div>

<div class="modern-card overflow-hidden">
    <div class="table-responsive">
        <table class="modern-table align-middle">
            <thead>
                <tr>
                    <th class="ps-4" style="width: 60px;">#</th>
                    <th>Questioner Identity</th>
                    <th style="width: 150px;">Country</th>
                    <th style="width: 140px;">Joined Date</th>
                    <th class="text-center" style="width: 100px;">Total</th>
                    <th class="text-center" style="width: 120px;">In Progress</th>
                    <th class="text-center" style="width: 120px;">Published</th>
                    <th class="text-end pe-4" style="width: 120px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questioners as $user)
                @php
                    $qName = $user->name ?? 'User';
                    $words = explode(' ', trim($qName));
                    $initials = '';
                    foreach (array_slice($words, 0, 2) as $w) {
                        $initials .= strtoupper(substr($w, 0, 1));
                    }
                    $initials = $initials ?: 'U';

                    $colorPalette = ['#EFF6FF;#2563EB', '#FAF5FF;#9333EA', '#ECFDF5;#059669', '#FFFBEB;#D97706'];
                    $colorIdx = crc32($qName) % count($colorPalette);
                    [$avatarBg, $avatarText] = explode(';', $colorPalette[$colorIdx]);
                @endphp
                <tr>
                    <td class="ps-4 text-muted small fw-semibold">{{ $questioners->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2.5">
                            @if($user->avatar)
                                <img src="{{ asset($user->avatar) }}" class="rounded-circle border" width="38" height="38" style="object-fit: cover;">
                            @else
                                <div class="avatar-monogram" style="background: {{ $avatarBg }}; color: {{ $avatarText }}; width: 38px; height: 38px;">
                                    {{ $initials }}
                                </div>
                            @endif
                            <div class="overflow-hidden">
                                <div class="fw-bold text-dark text-truncate" style="max-width: 220px; font-size: 0.88rem;">{{ $user->name }}</div>
                                <div class="text-muted text-truncate" style="font-size: 0.75rem; max-width: 220px;">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="small text-dark fw-medium d-inline-flex align-items-center gap-1.5">
                            <i class="bi bi-geo-alt text-muted" style="font-size: 0.8rem;"></i>
                            {{ $user->country ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <div class="text-muted small d-inline-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                            <i class="bi bi-calendar3 text-slate-400"></i>
                            {{ $user->created_at->format('d M Y') }}
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge" style="background: #EFF6FF; color: #2563EB; font-weight: 700; border-radius: 20px; padding: 4px 10px; font-size: 0.76rem;">
                            {{ $user->total_questions }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($user->pending_questions > 0)
                            <span class="badge" style="background: #FFFBEB; color: #D97706; font-weight: 700; border-radius: 20px; padding: 4px 10px; font-size: 0.76rem;">
                                {{ $user->pending_questions }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($user->published_questions > 0)
                            <span class="badge" style="background: #ECFDF5; color: #059669; font-weight: 700; border-radius: 20px; padding: 4px 10px; font-size: 0.76rem;">
                                {{ $user->published_questions }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.questioners.show', $user) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.78rem;">
                            <i class="bi bi-eye me-1"></i>View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1 text-slate-300 d-block mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">No Questioners Found</h6>
                        <p class="small text-muted mb-0">No user accounts matched the query.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($questioners->hasPages())
    <div class="p-3 bg-white border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="small text-muted">
            Showing {{ $questioners->firstItem() }} to {{ $questioners->lastItem() }} of {{ $questioners->total() }} users
        </div>
        <div>
            {{ $questioners->withQueryString()->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
