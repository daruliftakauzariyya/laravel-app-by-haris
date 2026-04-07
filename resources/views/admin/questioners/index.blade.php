@extends('layouts.admin')
@section('title', 'Questioners')
@section('topbar-title', 'Questioner Management')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0 fw-bold">All Questioners</h5>
        <small class="text-muted">Users who have submitted questions to the portal</small>
    </div>
    <form class="d-flex gap-2" method="GET">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name or email..." value="{{ request('search') }}">
        <button class="btn btn-sm btn-outline-secondary">Search</button>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Questioner</th>
                        <th>Country</th>
                        <th>Joined</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">In Progress</th>
                        <th class="text-center">Published</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questioners as $user)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $questioners->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=1a7a4a&color=fff&size=40' }}"
                                     class="rounded-circle" width="40" height="40">
                                <div>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="small">{{ $user->country ?? '—' }}</span>
                        </td>
                        <td><small class="text-muted">{{ $user->created_at->format('d M Y') }}</small></td>
                        <td class="text-center">
                            <span class="badge bg-secondary rounded-pill">{{ $user->total_questions }}</span>
                        </td>
                        <td class="text-center">
                            @if($user->pending_questions > 0)
                                <span class="badge bg-warning text-dark rounded-pill">{{ $user->pending_questions }}</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->published_questions > 0)
                                <span class="badge bg-success rounded-pill">{{ $user->published_questions }}</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.questioners.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-5">No questioners found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($questioners->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $questioners->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
