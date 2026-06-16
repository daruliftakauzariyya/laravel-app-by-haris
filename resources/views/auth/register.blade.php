@extends('layouts.public')
@section('title', 'Register')
@section('content')
<div class="container py-5" style="max-width:600px;">
    <div class="card border-0 shadow p-4">
        <div class="text-center mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:60px;height:60px;background:linear-gradient(135deg,#1a7a4a,#c9a84c);">
                <i class="bi bi-person-plus text-white" style="font-size:1.4rem;"></i>
            </div>
            <h4 class="fw-bold text-green">Create Account</h4>
            <p class="text-muted small">Join Darul Ifta Kauzariyya</p>
        </div>

        <form method="POST" action="{{ route('register') }}" id="registrationForm">
            @csrf
            
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="Your full name" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email ID *</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', str_contains($loginId, '@') ? $loginId : '') }}" placeholder="your@email.com" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Mobile Number *</label>
                <div class="iti-wrapper">
                    <input type="tel" id="phone" name="mobile"
                           class="form-control @error('mobile') is-invalid @enderror"
                           value="{{ old('mobile', !str_contains($loginId, '@') ? $loginId : '') }}"
                           placeholder="Mobile Number" required>
                </div>
                <input type="hidden" name="country_code" id="selected_country_code" value="{{ old('country_code', '+91') }}">
                @error('mobile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                @error('country_code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Password *</label>
                <input type="password" name="password" id="reg-password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="Min. 8 characters" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-success w-100 py-2 fw-bold" id="submitBtn">
                Register & Verify <i class="bi bi-check-circle ms-2"></i>
            </button>
        </form>
        <p class="text-center mt-4 small mb-0">Already have an account? <a href="{{ route('login') }}" class="text-green fw-semibold">Sign in</a></p>
    </div>
</div>

@push('styles')
<!-- intl-tel-input CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23/build/css/intlTelInput.css">
<style>
    /* intl-tel-input overrides to match Bootstrap form-control */
    .iti-wrapper {
        width: 100%;
    }
    .iti {
        width: 100%;
    }
    .iti__tel-input {
        width: 100%;
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem 0.375rem 52px;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .iti__tel-input:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
    }
    .iti__tel-input.is-invalid {
        border-color: #dc3545;
    }
    .iti__search-input {
        width: calc(100% - 20px);
        padding: 6px 10px;
        margin: 8px 10px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 0.9rem;
    }
    .iti__search-input:focus {
        outline: none;
        border-color: #1a7a4a;
        box-shadow: 0 0 0 0.2rem rgba(26,122,74,.2);
    }
    .iti__dropdown-content {
        z-index: 9999;
    }
</style>
@endpush

@push('scripts')
<!-- intl-tel-input JS -->
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23/build/js/intlTelInput.min.js"></script>
<script>
    // ---- intl-tel-input initialisation ----
    let itiInstance;
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('phone');
        const hiddenCode = document.getElementById('selected_country_code');

        itiInstance = window.intlTelInput(phoneInput, {
            utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@23/build/js/utils.js',
            initialCountry: 'auto',
            geoIpLookup: function(callback) {
                fetch('https://ipapi.co/json')
                    .then(r => r.json())
                    .then(data => callback(data.country_code))
                    .catch(() => callback('in'));
            },
            separateDialCode: true,
            searchPlaceholder: 'Search country or code...',
            countrySearch: true,
            formatOnDisplay: false,
        });

        // Keep hidden field in sync whenever the user changes the country flag
        phoneInput.addEventListener('countrychange', function () {
            const dialCode = '+' + itiInstance.getSelectedCountryData().dialCode;
            hiddenCode.value = dialCode;
        });

        // On form submit: update the hidden code once more
        document.getElementById('registrationForm').addEventListener('submit', function (e) {
            const dialCode = '+' + itiInstance.getSelectedCountryData().dialCode;
            hiddenCode.value = dialCode;
            
            // Disable register button to prevent double submit
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Registering...`;
        });
    });
</script>
@endpush
@endsection


