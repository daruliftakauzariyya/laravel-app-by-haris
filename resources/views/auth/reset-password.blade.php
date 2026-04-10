@extends('layouts.public')
@section('title', 'Reset Password')
@section('content')
<div class="container py-5" style="max-width:460px;">
    <div class="card border-0 shadow p-4">
        <div class="text-center mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:60px;height:60px;background:rgba(26, 122, 74, 0.1);">
                <i class="bi bi-shield-lock text-green" style="font-size:1.5rem;"></i>
            </div>
            <h4 class="fw-bold text-green">Reset Password</h4>
            <p class="text-muted small">Verify the OTP sent to {{ $email }} and set your new password.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success small py-2 mb-4">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-3">
                <label class="form-label fw-semibold">Recovery OTP</label>
                <input type="text" name="otp" class="form-control text-center fw-bold @error('otp') is-invalid @enderror" 
                       placeholder="6-digit OTP" maxlength="6" required autofocus>
                @error('otp')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <hr class="my-4 opacity-10">

            <div class="mb-3">
                <label class="form-label fw-semibold">New Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                       placeholder="At least 8 characters" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control" 
                       placeholder="Repeat new password" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Update & Sign In</button>
        </form>
    </div>
</div>
@endsection
