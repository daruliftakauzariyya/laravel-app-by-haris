@extends('layouts.public')
@section('title', 'Complete Your Profile')
@section('content')
<div class="container py-5" style="max-width:520px;">
    <div class="card border-0 shadow p-4">
        <div class="text-center mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:60px;height:60px;background:linear-gradient(135deg,#1a7a4a,#c9a84c);">
                <i class="bi bi-person-check text-white" style="font-size:1.4rem;"></i>
            </div>
            <h4 class="fw-bold text-green">Complete Your Profile</h4>
            <p class="text-muted small">Required before submitting any question</p>
        </div>
        <form method="POST" action="{{ route('profile.complete.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', Auth::user()->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                    <option value="">Select gender...</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Country <span class="text-danger">*</span></label>
                <input type="text" name="country" class="form-control @error('country') is-invalid @enderror"
                       value="{{ old('country') }}" placeholder="e.g. India, Pakistan" required>
                @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Mobile / WhatsApp <span class="text-danger">*</span></label>
                <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                       value="{{ old('mobile') }}" placeholder="+91 98765 43210" required>
                @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Mother Language <span class="text-danger">*</span></label>
                <input type="text" name="mother_language" class="form-control @error('mother_language') is-invalid @enderror"
                       value="{{ old('mother_language') }}" placeholder="e.g. Urdu, Arabic, Hindi" required>
                @error('mother_language')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Save & Continue</button>
        </form>
    </div>
</div>
@endsection
