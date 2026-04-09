@extends('layouts.mufti')
@section('title', 'Profile Settings')
@section('topbar-title', 'Profile Settings')
@section('content')
<div class="row g-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle border shadow-sm" style="width:120px; height:120px; object-fit:cover;">
                    @else
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center border" style="width:120px; height:120px;">
                            <i class="bi bi-person h1 mb-0"></i>
                        </div>
                    @endif
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-0">{{ $user->country ?? 'No Country Provided' }}</p>
                <div class="mt-3">
                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $user->is_active ? 'text-success' : 'text-danger' }} border {{ $user->is_active ? 'border-success' : 'border-danger' }} border-opacity-25 px-3">
                        {{ $user->is_active ? 'Active Account' : 'Inactive Account' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Administration Info <small class="text-muted fw-normal">(Read-only)</small></h6>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="mb-3">
                    <label class="small text-muted fw-semibold d-block mb-1">Has "Checked & Approved" Permission?</label>
                    <div class="form-check form-switch opacity-75">
                        <input class="form-check-input" type="checkbox" disabled {{ $user->can_check_approve ? 'checked' : '' }}>
                        <span class="small fw-medium">{{ $user->can_check_approve ? 'Yes' : 'No' }}</span>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="small text-muted fw-semibold d-block mb-1">Scholar Madhhab(s)</label>
                    @if($user->madhab && is_array($user->madhab))
                        @foreach($user->madhab as $m)
                            <span class="badge bg-light text-dark border me-1">{{ $m }}</span>
                        @endforeach
                    @elseif($user->madhab)
                        <span class="badge bg-light text-dark border">{{ $user->madhab }}</span>
                    @else
                        <span class="text-muted small">Not specified</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2"></i>Personal Settings</h6>
            </div>
            <div class="card-body p-4 pt-0">
                <form method="POST" action="{{ route('mufti.profile.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">WhatsApp / Mobile</label>
                        <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $user->mobile) }}">
                        @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Profile Image</label>
                        <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror">
                        <div class="form-text small">Recommended: Square image, max 2MB.</div>
                        @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-4 mt-4">
                        <h6 class="fw-bold small mb-2"><i class="bi bi-lock me-2"></i>Change Password</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">New Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                        <div class="form-text small">Leave blank if you don't want to change your password.</div>
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-2"></i>Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
