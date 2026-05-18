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
                        <!-- intl-tel-input: searchable country code + mobile number -->
                        <div class="iti-wrapper">
                            <input type="tel" id="phone" name="mobile"
                                   class="form-control @error('mobile') is-invalid @enderror"
                                   value="{{ old('mobile', !str_contains($loginId, '@') ? $loginId : '') }}"
                                   placeholder="Mobile Number" required>
                        </div>
                        <!-- Hidden field that holds just the dial code for backend validation -->
                        <input type="hidden" name="country_code" id="selected_country_code" value="{{ old('country_code', '+91') }}">
                        @error('mobile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        @error('country_code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
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
<!-- intl-tel-input CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23/build/css/intlTelInput.css">
<!-- Tom Select CSS (searchable dropdowns for Country / State / District) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
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
    /* Search box inside the dropdown */
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
    /* Dropdown z-index fix */
    .iti__dropdown-content {
        z-index: 9999;
    }

    /* Tom Select overrides — match Bootstrap form-control look */
    .ts-wrapper {
        padding: 0;
    }
    .ts-control {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        min-height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        box-shadow: none;
        cursor: text;
    }
    .ts-control:focus-within {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
    }
    .ts-control input {
        color: #212529;
    }
    .ts-dropdown {
        z-index: 9999;
        border-radius: 0.375rem;
        border-color: #ced4da;
        box-shadow: 0 4px 16px rgba(0,0,0,.12);
    }
    .ts-dropdown .ts-dropdown-content {
        max-height: 220px;
    }
    /* Green highlight on selected/active item */
    .ts-dropdown .option.selected,
    .ts-dropdown .option:hover,
    .ts-dropdown .active {
        background-color: #1a7a4a !important;
        color: #fff !important;
    }
    /* Disabled wrapper styling */
    .ts-wrapper.disabled .ts-control {
        background-color: #e9ecef;
        pointer-events: none;
        opacity: 0.7;
    }
</style>
@endpush

@push('scripts')
<!-- intl-tel-input JS -->
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23/build/js/intlTelInput.min.js"></script>
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    // ---- intl-tel-input initialisation ----
    let itiInstance;
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('phone');
        const hiddenCode = document.getElementById('selected_country_code');

        itiInstance = window.intlTelInput(phoneInput, {
            // Use the CDN-hosted utility script for geo-IP detection
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
            // Show search box inside the dropdown
            countrySearch: true,
            formatOnDisplay: false,
        });

        // Keep hidden field in sync whenever the user changes the country flag
        phoneInput.addEventListener('countrychange', function () {
            const dialCode = '+' + itiInstance.getSelectedCountryData().dialCode;
            hiddenCode.value = dialCode;
        });

        // On form submit: update the hidden code once more & validate
        document.getElementById('registrationForm').addEventListener('submit', function (e) {
            const dialCode = '+' + itiInstance.getSelectedCountryData().dialCode;
            hiddenCode.value = dialCode;
        });
    });
    // ----------------------------------------

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
        
        // ---- Tom Select for Country / State / District ----
        // Three long-lived instances. Use Tom Select's own API to update options
        // in-place — no destroy/recreate needed.

        const countryEl  = document.getElementById('country');
        const stateEl    = document.getElementById('state');
        const districtEl = document.getElementById('district');

        // Ensure underlying selects are not disabled before Tom Select wraps them
        stateEl.disabled   = false;
        districtEl.disabled = false;

        const tsOpts = {
            allowEmptyOption: true,
            maxOptions: null,
            searchField: ['text'],
            sortField: { field: 'text', direction: 'asc' },
            create: false,
        };

        const tsCountry  = new TomSelect(countryEl,  Object.assign({}, tsOpts, { placeholder: 'Search or select country…'  }));
        const tsState    = new TomSelect(stateEl,    Object.assign({}, tsOpts, { placeholder: 'Select country first…'      }));
        const tsDistrict = new TomSelect(districtEl, Object.assign({}, tsOpts, { placeholder: 'Select state first…'        }));

        // State & District start disabled
        tsState.disable();
        tsDistrict.disable();

        // Load countries into tsCountry
        fetch('https://countriesnow.space/api/v0.1/countries/positions')
            .then(r => r.json())
            .then(data => {
                if (data.error === false) {
                    const opts = data.data.map(c => ({ value: c.name, text: c.name }));
                    tsCountry.addOptions(opts);
                }
            });

        // Country → States
        tsCountry.on('change', function(countryValue) {
            tsState.clear(true);
            tsState.clearOptions();
            tsState.disable();
            tsDistrict.clear(true);
            tsDistrict.clearOptions();
            tsDistrict.disable();

            if (!countryValue) return;

            fetch('https://countriesnow.space/api/v0.1/countries/states', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ country: countryValue })
            })
            .then(res => res.json())
            .then(data => {
                if (data.error === false && data.data.states && data.data.states.length > 0) {
                    const opts = data.data.states.map(s => ({ value: s.name, text: s.name }));
                    tsState.addOptions(opts);
                    tsState.enable();
                } else {
                    tsState.addOption({ value: 'N/A', text: 'N/A' });
                    tsState.enable();
                    tsDistrict.addOption({ value: 'N/A', text: 'N/A' });
                    tsDistrict.setValue('N/A', true);
                    tsDistrict.enable();
                }
            })
            .catch(() => {
                tsState.addOption({ value: 'Unknown', text: 'Unknown' });
                tsState.enable();
            });
        });

        // State → Districts
        tsState.on('change', function(stateValue) {
            tsDistrict.clear(true);
            tsDistrict.clearOptions();
            tsDistrict.disable();

            if (!stateValue || stateValue === 'N/A') return;

            const countryValue = tsCountry.getValue();

            fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ country: countryValue, state: stateValue })
            })
            .then(res => res.json())
            .then(data => {
                if (data.error === false && data.data && data.data.length > 0) {
                    const opts = data.data.map(c => ({ value: c, text: c }));
                    tsDistrict.addOptions(opts);
                } else {
                    tsDistrict.addOption({ value: stateValue, text: stateValue });
                }
                tsDistrict.enable();
            })
            .catch(() => {
                tsDistrict.addOption({ value: stateValue, text: stateValue });
                tsDistrict.enable();
            });
        });
    });
</script>
@endpush
@endsection


