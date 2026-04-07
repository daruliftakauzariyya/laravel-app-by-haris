@extends('layouts.admin')
@section('title', 'Edit Mufti Profile')
@section('topbar-title', 'Updating Scholar Profile')
@section('content')

<div class="row justify-content-center g-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                <i class="bi bi-pencil-square me-2 text-primary"></i>
                <h6 class="mb-0 fw-bold">Update Account — {{ $user->name }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.muftis.update', $user) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-4 mt-4">
                        <h6 class="fw-bold small mb-3"><i class="bi bi-shield-lock me-2"></i>Change Password (Optional)</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <label class="form-label small fw-semibold text-muted">New Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                        <div class="form-text mt-1 small">Leave blank to keep the current password.</div>
                    </div>

                    <div class="bg-info bg-opacity-10 border border-info border-opacity-10 p-3 rounded-3 mb-4">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ $user->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActive">Account Status Active</label>
                        </div>
                        <hr class="my-2 border-info border-opacity-25 opacity-25">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="can_check_approve" id="canApprove" {{ $user->can_check_approve ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="canApprove">Can Verify & Approve Fatwas</label>
                            <div class="form-text small opacity-75">Senior status for checking other muftis' work.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Save Changes
                        </button>
                        <a href="{{ route('admin.muftis.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Side Stats --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold small mb-3">Scholar Activity Summary</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Assigned Questions</span>
                    <span class="badge bg-secondary rounded-pill">{{ $user->assignedQuestions()->count() }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Fatwas Answered</span>
                    <span class="badge bg-primary rounded-pill">{{ $user->muftiAnswers()->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
