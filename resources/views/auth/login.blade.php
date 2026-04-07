@extends('layouts.public')
@section('title', 'Login')
@section('content')
<div class="container py-5" style="max-width:460px;">
    <div class="card border-0 shadow p-4">
        <div class="text-center mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:60px;height:60px;background:linear-gradient(135deg,#1a7a4a,#c9a84c);">
                <i class="bi bi-book-half text-white" style="font-size:1.5rem;"></i>
            </div>
            <h4 class="fw-bold text-green">Sign In</h4>
            <p class="text-muted small">Welcome back to Darul Ifta Kauzariyya</p>
        </div>


        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Email ID / Mobile Number</label>
                <input type="text" name="login_id" class="form-control @error('login_id') is-invalid @enderror"
                       value="{{ old('login_id') }}" placeholder="Enter Email or Mobile" required>
                @error('login_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="Your password" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember">
                    <label class="form-check-label small" for="remember">Remember me</label>
                </div>
            </div>
            <div class="d-flex flex-column gap-2">
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Sign In with Password</button>
                <button type="submit" formaction="{{ route('login.otp.send') }}" class="btn btn-outline-success w-100 py-2 fw-bold">Send OTP to Email</button>
            </div>
        </form>
        <p class="text-center mt-3 small mb-0">Don't have an account? <a href="{{ route('register') }}" class="text-green fw-semibold">Register</a></p>
    </div>
</div>
@endsection
