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

        @if(session('error'))
            <div class="alert alert-danger py-2 px-3 small d-flex align-items-center mb-3">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('status'))
            <div class="alert alert-success py-2 px-3 small d-flex align-items-center mb-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Email ID / Mobile Number</label>
                <input type="text" name="login_id" id="login_id" class="form-control @error('login_id') is-invalid @enderror"
                       value="{{ old('login_id') }}" placeholder="Enter Email or Mobile" required>
                @error('login_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            
            <div id="passwordSection" class="d-none">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="Your password">
                    @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label small" for="remember">Remember me</label>
                    </div>
                    <div>
                        <a href="{{ route('password.request') }}" class="small text-green fw-semibold text-decoration-none">Forgot Password?</a>
                    </div>
                </div>
                <div class="d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Sign In with Password</button>
                    <button type="submit" formaction="{{ route('login.otp.send') }}" id="otpBtn" class="btn btn-outline-success w-100 py-2 fw-bold">Send OTP to Email</button>
                </div>
            </div>

            <div id="continueSection" class="d-flex flex-column gap-2 mt-4">
                <button type="button" id="continueBtn" class="btn btn-primary w-100 py-2 fw-bold">Continue</button>
            </div>
        </form>
        <p class="text-center mt-3 small mb-0">Don't have an account? <a href="{{ route('register') }}" class="text-green fw-semibold border-0 bg-transparent p-0">Register</a></p>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const continueBtn = document.getElementById('continueBtn');
    const loginIdInput = document.getElementById('login_id');
    const passwordSection = document.getElementById('passwordSection');
    const continueSection = document.getElementById('continueSection');
    const passwordInput = document.getElementById('password');

    // If there's an error from a previous login attempt, show the password section immediately
    @if($errors->has('login_id') || $errors->has('password'))
        passwordSection.classList.remove('d-none');
        continueSection.classList.add('d-none');
        passwordInput.required = true;
    @endif

    continueBtn.addEventListener('click', function() {
        const loginId = loginIdInput.value.trim();
        if(!loginId) {
            loginIdInput.classList.add('is-invalid');
            return;
        }
        
        continueBtn.disabled = true;
        continueBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Checking...';

        fetch('{{ route("login.check") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ login_id: loginId })
        })
        .then(response => response.json())
        .then(data => {
            if(data.exists) {
                // Account exists, show password
                passwordSection.classList.remove('d-none');
                continueSection.classList.add('d-none');
                passwordInput.required = true;
                passwordInput.focus();
            } else {
                // Account does not exist, redirect to register with auto-fill
                window.location.href = '{{ route("register") }}?login_id=' + encodeURIComponent(loginId);
            }
        })
        .catch(error => {
            continueBtn.disabled = false;
            continueBtn.innerHTML = 'Continue';
            console.error('Error checking account:', error);
        });
    });

    // Allow "Enter" key to press continue
    loginIdInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !continueSection.classList.contains('d-none')) {
            e.preventDefault();
            continueBtn.click();
        }
    });
});
</script>
@endpush
@endsection
