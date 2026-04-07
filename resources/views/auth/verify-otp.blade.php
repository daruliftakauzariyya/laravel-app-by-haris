@extends('layouts.public')
@section('title', 'Verify OTP')
@section('content')
<div class="container py-5" style="max-width:460px;">
    <div class="card border-0 shadow p-4 text-center">
        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto"
             style="width:60px;height:60px;background:linear-gradient(135deg,#1a7a4a,#c9a84c);">
            <i class="bi bi-envelope-check text-white" style="font-size:1.5rem;"></i>
        </div>
        <h4 class="fw-bold text-green">Enter OTP</h4>
        <p class="text-muted small">We've sent a 6-digit code to <strong>{{ $email ?? 'your email' }}</strong>.</p>
        
        @if(session('success'))
            <div class="alert alert-success small py-2">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger small py-2">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('otp.verify.submit') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email ?? '' }}">
            
            <div class="mb-4">
                <input type="text" name="otp" class="form-control form-control-lg text-center letter-spacing-lg @error('otp') is-invalid @enderror"
                       placeholder="------" maxlength="6" style="letter-spacing: 10px; font-weight: bold;" required autofocus>
                @error('otp')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Verify & Continue</button>
        </form>
    </div>
</div>
@endsection
