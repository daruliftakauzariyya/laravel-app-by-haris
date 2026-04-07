@extends('layouts.mufti')
@section('title', 'Profile Settings')
@section('topbar-title', 'Profile Settings')
@section('content')
<div class="card border-0 shadow-sm" style="max-width:540px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('mufti.profile.update') }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email <small class="text-muted">(read-only)</small></label>
                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Mobile / WhatsApp</label>
                <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Country</label>
                <input type="text" name="country" class="form-control" value="{{ old('country', $user->country) }}">
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Mother Language</label>
                <input type="text" name="mother_language" class="form-control" value="{{ old('mother_language', $user->mother_language) }}">
            </div>
            
            <div class="card bg-light border-0 mb-4 p-3">
                <div class="form-check form-switch pt-1">
                    <input class="form-check-input" type="checkbox" name="can_check_approve" id="canCheckApprove" value="1" {{ old('can_check_approve', $user->can_check_approve) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold ms-2" for="canCheckApprove">Has "Checked & Approved" Permission?</label>
                </div>
                <small class="text-muted d-block mt-2">If enabled, you will appear in the "Checked & Approved By" dropdown list when Admins publish Fatwas.</small>
            </div>
            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
        </form>
    </div>
</div>
@endsection
