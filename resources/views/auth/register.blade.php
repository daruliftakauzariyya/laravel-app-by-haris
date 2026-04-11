@extends('layouts.public')
@section('title', 'Register')
@section('content')
<div class="container py-5" style="max-width:800px;">
    <div class="card border-0 shadow p-4">
        <div class="text-center mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:60px;height:60px;background:linear-gradient(135deg,#1a7a4a,#c9a84c);">
                <i class="bi bi-person-plus text-white" style="font-size:1.4rem;"></i>
            </div>
            <h4 class="fw-bold text-green">Create Account</h4>
            <p class="text-muted small">Join Darul Ifta Kauzariyya</p>
        </div>

        <div class="position-relative mb-5">
            <div class="progress" style="height: 4px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: 33%;" id="progressBar"></div>
            </div>
            <div class="d-flex justify-content-between position-absolute w-100 top-50 translate-middle-y">
                <div class="step-indicator active" id="indicator-1">1</div>
                <div class="step-indicator" id="indicator-2">2</div>
                <div class="step-indicator" id="indicator-3">3</div>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" id="registrationForm">
            @csrf
            
            <!-- STEP 1: Personal Details -->
            <div id="step-1" class="wizard-step">
                <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">Step 1: Personal Details</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Full Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="Your full name" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Email ID *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', str_contains($loginId, '@') ? $loginId : '') }}" placeholder="your@email.com" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">Mobile Number *</label>
                        <div class="input-group">
                            <select name="country_code" id="country_code" class="form-select @error('country_code') is-invalid @enderror" style="max-width: 120px;" required>
                                <option value="">Code</option>
                            </select>
                            <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                                   value="{{ old('mobile', !str_contains($loginId, '@') ? $loginId : '') }}" placeholder="Mobile Number" required>
                        </div>
                        @error('mobile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-primary px-4 py-2" onclick="nextStep(2)">Next Step <i class="bi bi-arrow-right ms-2"></i></button>
                </div>
            </div>

            <!-- STEP 2: Location -->
            <div id="step-2" class="wizard-step d-none">
                <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">Step 2: Address Details</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Address Line 1 *</label>
                        <input type="text" name="address_line_1" class="form-control @error('address_line_1') is-invalid @enderror" value="{{ old('address_line_1') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Address Line 2</label>
                        <input type="text" name="address_line_2" class="form-control" value="{{ old('address_line_2') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Country *</label>
                        <select name="country" id="country" class="form-select @error('country') is-invalid @enderror" required>
                            <option value="">Select Country</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">State *</label>
                        <select name="state" id="state" class="form-select @error('state') is-invalid @enderror" required disabled>
                            <option value="">Select State</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">District *</label>
                        <select name="district" id="district" class="form-select @error('district') is-invalid @enderror" required disabled>
                            <option value="">Select District</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="prevStep(1)"><i class="bi bi-arrow-left me-2"></i> Previous</button>
                    <button type="button" class="btn btn-primary px-4 py-2" onclick="nextStep(3)">Next Step <i class="bi bi-arrow-right ms-2"></i></button>
                </div>
            </div>

            <!-- STEP 3: Security & Preferences -->
            <div id="step-3" class="wizard-step d-none">
                <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">Step 3: Security & Preferences</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Gender *</label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Madhab *</label>
                        <select name="madhab_id" class="form-select @error('madhab_id') is-invalid @enderror" required>
                            <option value="">Select Madhab</option>
                            @foreach($madhabs as $m)
                                <option value="{{ $m->id }}" {{ old('madhab_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Password *</label>
                        <input type="password" name="password" id="reg-password" class="form-control @error('password') is-invalid @enderror"
                               placeholder="Min. 8 characters" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Confirm Password *</label>
                        <input type="password" name="password_confirmation" id="reg-password-confirm" class="form-control" placeholder="Repeat password" required>
                        <div class="invalid-feedback">Passwords do not match.</div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="prevStep(2)"><i class="bi bi-arrow-left me-2"></i> Previous</button>
                    <button type="submit" class="btn btn-success px-4 py-2 fw-bold" id="submitBtn">Register & Verify <i class="bi bi-check-circle ms-2"></i></button>
                </div>
            </div>
        </form>
        <p class="text-center mt-4 small mb-0">Already have an account? <a href="{{ route('login') }}" class="text-green fw-semibold">Sign in</a></p>
    </div>
</div>

@push('styles')
<style>
    .step-indicator {
        width: 32px;
        height: 32px;
        background: #fff;
        border: 2px solid #dee2e6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #6c757d;
        z-index: 1;
        transition: all 0.3s ease;
    }
    .step-indicator.active {
        border-color: #1a7a4a;
        background: #1a7a4a;
        color: #fff;
    }
</style>
@endpush

@push('scripts')
<script>
    function validateStep(step) {
        let isValid = true;
        const stepEl = document.getElementById('step-' + step);
        const requiredInputs = stepEl.querySelectorAll('[required]');
        
        requiredInputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (step === 3) {
            const pass = document.getElementById('reg-password').value;
            const confirm = document.getElementById('reg-password-confirm').value;
            if (pass !== confirm) {
                isValid = false;
                document.getElementById('reg-password-confirm').classList.add('is-invalid');
            } else {
                document.getElementById('reg-password-confirm').classList.remove('is-invalid');
            }
        }

        return isValid;
    }

    function nextStep(step) {
        if (!validateStep(step - 1)) return;
        
        document.querySelectorAll('.wizard-step').forEach(el => el.classList.add('d-none'));
        document.getElementById('step-' + step).classList.remove('d-none');
        
        document.getElementById('progressBar').style.width = (step * 33.33) + '%';
        for(let i=1; i<=3; i++) {
            if(i <= step) {
                document.getElementById('indicator-' + i).classList.add('active');
            } else {
                document.getElementById('indicator-' + i).classList.remove('active');
            }
        }
    }

    function prevStep(step) {
        document.querySelectorAll('.wizard-step').forEach(el => el.classList.add('d-none'));
        document.getElementById('step-' + step).classList.remove('d-none');
        
        document.getElementById('progressBar').style.width = (step * 33.33) + '%';
        for(let i=1; i<=3; i++) {
            if(i <= step) {
                document.getElementById('indicator-' + i).classList.add('active');
            } else {
                document.getElementById('indicator-' + i).classList.remove('active');
            }
        }
    }

    // Auto-advance step on load if there are validation errors on subsequent steps
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->has('address_line_1') || $errors->has('country') || $errors->has('state') || $errors->has('district'))
            nextStep(2);
        @elseif($errors->has('gender') || $errors->has('madhab') || $errors->has('password'))
            validateStep(1) ? nextStep(3) : nextStep(1);
        @endif
        
        const countrySelect = document.getElementById('country');
        const stateSelect = document.getElementById('state');
        const districtSelect = document.getElementById('district');
        const dialCodeSelect = document.getElementById('country_code');

        // Fetch Countries for Dial Codes
        fetch('https://countriesnow.space/api/v0.1/countries/codes')
            .then(response => response.json())
            .then(data => {
                if(data.error === false) {
                    data.data.sort((a,b) => a.name.localeCompare(b.name)).forEach(country => {
                        let option = new Option(`${country.dial_code} (${country.code})`, country.dial_code);
                        dialCodeSelect.add(option);
                    });
                }
            });

        // Fetch Countries for Address
        fetch('https://countriesnow.space/api/v0.1/countries/positions')
            .then(response => response.json())
            .then(data => {
                if(data.error === false) {
                    data.data.forEach(country => {
                        let option = new Option(country.name, country.name);
                        countrySelect.add(option);
                    });
                }
            });

        countrySelect.addEventListener('change', function() {
            stateSelect.innerHTML = '<option value="">Loading...</option>';
            stateSelect.disabled = true;
            districtSelect.innerHTML = '<option value="">Select District</option>';
            districtSelect.disabled = true;

            if (this.value) {
                fetch('https://countriesnow.space/api/v0.1/countries/states', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ country: this.value })
                })
                .then(res => res.json())
                .then(data => {
                    stateSelect.innerHTML = '<option value="">Select State</option>';
                    if(data.error === false && data.data.states.length > 0) {
                        data.data.states.forEach(state => {
                            let option = new Option(state.name, state.name);
                            stateSelect.add(option);
                        });
                        stateSelect.disabled = false;
                    } else {
                        let option = new Option("N/A", "N/A");
                        stateSelect.add(option);
                        stateSelect.disabled = false;
                        districtSelect.innerHTML = '<option value="N/A">N/A</option>';
                        districtSelect.disabled = false;
                    }
                })
                .catch(err => {
                    stateSelect.innerHTML = '<option value="Unknown">Unknown</option>';
                    stateSelect.disabled = false;
                });
            }
        });

        stateSelect.addEventListener('change', function() {
            districtSelect.innerHTML = '<option value="">Loading...</option>';
            districtSelect.disabled = true;

            if (this.value && this.value !== "N/A") {
                fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ country: countrySelect.value, state: this.value })
                })
                .then(res => res.json())
                .then(data => {
                    districtSelect.innerHTML = '<option value="">Select District</option>';
                    if(data.error === false && data.data.length > 0) {
                        data.data.forEach(city => {
                            let option = new Option(city, city);
                            districtSelect.add(option);
                        });
                        districtSelect.disabled = false;
                    } else {
                        let option = new Option(this.value, this.value);
                        districtSelect.add(option);
                        districtSelect.disabled = false;
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection
