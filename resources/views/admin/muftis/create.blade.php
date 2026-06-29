@extends('layouts.admin')
@section('title', 'Add New Mufti')
@section('topbar-title', 'Create Scholar Profile')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2"></i>New Mufti Account</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.muftis.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               placeholder="e.g. Mufti Ahmed Raza" value="{{ old('name') }}" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               placeholder="scholar@darulifta.com" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted small">Select Madhhab(s)</label>
                        <div class="d-flex flex-wrap gap-3 mt-1">
                            @foreach($madhabs as $m)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="madhab[]" value="{{ $m->id }}" id="madhab_{{ $m->id }}" {{ in_array($m->id, (array)old('madhab', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="madhab_{{ $m->id }}">{{ $m->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-4">
                        <label class="form-label fw-semibold text-muted small d-flex align-items-center gap-2">
                            <i class="bi bi-sort-numeric-down"></i> Display Order
                        </label>
                        <input type="number" name="mufti_sort_order" min="0" max="9998"
                               class="form-control @error('mufti_sort_order') is-invalid @enderror"
                               value="{{ old('mufti_sort_order', 0) }}" placeholder="0">
                        <div class="form-text small">
                            <strong>0</strong> = Automatic (sorted by most fatwas first). Set <strong>1, 2, 3…</strong> to pin this Mufti to a specific position on the Team page.
                        </div>
                        @error('mufti_sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-4">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                            <label class="form-check-label fw-semibold" for="isActive">Account Active</label>
                            <div class="form-text text-muted small">Disable this to prevent the Mufti from logging in.</div>
                        </div>
                        <hr class="my-3 opacity-10">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="can_check_approve" id="canApprove">
                            <label class="form-check-label fw-semibold" for="canApprove">Can Check & Approve Fatwas</label>
                            <div class="form-text text-muted small">Enable if this scholar can verify and approve answers written by others.</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-person-check-fill me-2"></i>Create Scholar Account
                        </button>
                        <a href="{{ route('admin.muftis.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
