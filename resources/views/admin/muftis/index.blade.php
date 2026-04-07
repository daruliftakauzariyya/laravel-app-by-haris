@extends('layouts.admin')
@section('title', 'Mufti Management')
@section('topbar-title', 'Mufti User Profiles')
@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Scholar Profiles</h6>
        <a href="{{ route('admin.muftis.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Add New Mufti
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Scholar Name</th>
                    <th>Email Address</th>
                    <th>Account Status</th>
                    <th>Check & Approve</th>
                    <th>Created On</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($muftis as $mufti)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{ $mufti->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($mufti->name).'&background=1a7a4a&color=fff&size=32' }}" 
                                 class="rounded-circle me-2" width="32" height="32">
                            <span class="fw-semibold text-dark">{{ $mufti->name }}</span>
                        </div>
                    </td>
                    <td>{{ $mufti->email }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.muftis.toggle-status', $mufti) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $mufti->is_active ? 'btn-success' : 'btn-outline-danger' }} py-0 px-2" style="font-size:0.75rem;">
                                {{ $mufti->is_active ? 'Active' : 'Disabled' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        @if($mufti->can_check_approve)
                            <span class="badge bg-info bg-opacity-10 text-info fw-bold border border-info border-opacity-25 py-1 px-2">
                                <i class="bi bi-shield-check me-1"></i>Yes
                            </span>
                        @else
                            <span class="text-muted small">— No —</span>
                        @endif
                    </td>
                    <td><small class="text-muted">{{ $mufti->created_at->format('d M Y') }}</small></td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border-0 shadow-none" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border shadow-sm sm-small">
                                <li><a class="dropdown-item" href="{{ route('admin.muftis.edit', $mufti) }}"><i class="bi bi-pencil me-2"></i>Edit Profile</a></li>
                                @if(!$mufti->muftiAnswers()->exists() && !$mufti->assignedQuestions()->exists())
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('admin.muftis.destroy', $mufti) }}" onsubmit="return confirm('Detele Mufti profile?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete Scholar</button>
                                    </form>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No scholars found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
