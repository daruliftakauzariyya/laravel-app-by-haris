@extends('layouts.public')
@section('title', 'Forgot Password')
@section('content')
<div class="container py-5" style="max-width:460px;">
    <div class="card border-0 shadow p-4">
        <div class="text-center mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:60px;height:60px;background:rgba(26, 122, 74, 0.1);">
                <i class="bi bi-key text-green" style="font-size:1.5rem;"></i>
            </div>
            <h4 class="fw-bold text-green">Forgot Password?</h4>
            <p class="text-muted small">Enter your email or mobile to receive a recovery OTP.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success small py-2 mb-4">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label fw-semibold">Email / Mobile Number</label>
                <input type="text" name="login_id" class="form-control @error('login_id') is-invalid @enderror"
                       value="{{ old('login_id') }}" placeholder="Enter your identifier" required autofocus>
                @error('login_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-3">Send Recovery OTP</button>
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-decoration-none small text-muted"><i class="bi bi-arrow-left me-1"></i>Back to Login</a>
            </div>
        </form>
    </div>
</div>
@endsection
