@extends('layouts.public')
@section('title', 'Complete Your Profile')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
<style>
    .ts-wrapper { padding: 0; }
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
    .ts-dropdown {
        z-index: 9999;
        border-radius: 0.375rem;
        border-color: #ced4da;
        box-shadow: 0 4px 16px rgba(0,0,0,.12);
    }
    .ts-dropdown .ts-dropdown-content {
        max-height: 200px;
    }
    .ts-dropdown .option.selected,
    .ts-dropdown .option:hover,
    .ts-dropdown .active {
        background-color: #1a7a4a !important;
        color: #fff !important;
    }
    .ts-wrapper.disabled .ts-control {
        background-color: #e9ecef;
        pointer-events: none;
        opacity: 0.7;
    }
    .text-green {
        color: #1a7a4a !important;
    }
    .btn-green {
        background-color: #1a7a4a;
        border-color: #1a7a4a;
        color: white;
    }
    .btn-green:hover {
        background-color: #15613a;
        border-color: #15613a;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container py-5" style="max-width: 650px;">
    @if($hasPendingQuestion)
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4" style="background-color: #eaf6ef; color: #1a7a4a;">
            <i class="bi bi-check-circle-fill me-3 fs-4 text-green"></i>
            <div>
                <strong class="d-block mb-0.5">Draft Question Saved!</strong> 
                Please provide your profile details below to complete your question submission.
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow p-4 rounded-3">
        <div class="text-center mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width:60px;height:60px;background:linear-gradient(135deg,#1a7a4a,#c9a84c);">
                <i class="bi bi-person-check text-white" style="font-size:1.6rem;"></i>
            </div>
            <h4 class="fw-bold text-green">Complete Your Profile</h4>
            @if($isSecondQuestion)
                <span class="badge bg-danger px-3 py-2 mt-2"><i class="bi bi-shield-lock me-1"></i> Mandatory Profile Completion</span>
                <p class="text-muted small mt-2">Completing your profile details is mandatory before asking your second or subsequent questions.</p>
            @else
                <p class="text-muted small">Completing details is highly recommended as location-specific rulings depend on your locale.</p>
            @endif
        </div>

        <form method="POST" action="{{ route('profile.complete.store') }}" id="completeProfileForm">
            @csrf
            
            <div class="row g-3 mb-4">
                @if($showMobile)
                <div class="col-12">
                    <label class="form-label fw-semibold small text-muted">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', Auth::user()->mobile) }}" placeholder="Enter mobile number with country code" required>
                    @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                @endif

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender', Auth::user()->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', Auth::user()->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted">Madhab Preference <span class="text-danger">*</span></label>
                    <select name="madhab_id" class="form-select @error('madhab_id') is-invalid @enderror" required>
                        <option value="">Select Madhab...</option>
                        @foreach($madhabs as $m)
                            <option value="{{ $m->id }}" {{ old('madhab_id', $preferredMadhabId) == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('madhab_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted">Address Line 1 <span class="text-danger">*</span></label>
                    <input type="text" name="address_line_1" class="form-control @error('address_line_1') is-invalid @enderror" value="{{ old('address_line_1', Auth::user()->address_line_1) }}" placeholder="Street address, P.O. Box" required>
                    @error('address_line_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted">Address Line 2 <span class="text-muted">(Optional)</span></label>
                    <input type="text" name="address_line_2" class="form-control @error('address_line_2') is-invalid @enderror" value="{{ old('address_line_2', Auth::user()->address_line_2) }}" placeholder="Apartment, suite, unit, etc.">
                    @error('address_line_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-muted">Country <span class="text-danger">*</span></label>
                    <select name="country" id="profile_country" class="form-select @error('country') is-invalid @enderror" required>
                        <option value="">Select Country</option>
                    </select>
                    @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-muted">State <span class="text-danger">*</span></label>
                    <select name="state" id="profile_state" class="form-select @error('state') is-invalid @enderror" required disabled>
                        <option value="">Select State</option>
                    </select>
                    @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-muted">District <span class="text-danger">*</span></label>
                    <select name="district" id="profile_district" class="form-select @error('district') is-invalid @enderror" required disabled>
                        <option value="">Select District</option>
                    </select>
                    @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold small text-muted">Mother Language <span class="text-muted">(Optional)</span></label>
                    <input type="text" name="mother_language" class="form-control @error('mother_language') is-invalid @enderror" value="{{ old('mother_language', Auth::user()->mother_language) }}" placeholder="e.g. Urdu, Arabic, English, Malayalam">
                    @error('mother_language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex flex-column gap-2">
                <button type="submit" class="btn btn-green w-100 py-2.5 fw-bold shadow-sm">
                    @if($hasPendingQuestion)
                        <i class="bi bi-check2-circle me-1"></i> Save Profile & Submit Question
                    @else
                        <i class="bi bi-save me-1"></i> Save Profile Details
                    @endif
                </button>

                @if($hasPendingQuestion && !$isSecondQuestion)
                    <button type="button" class="btn btn-outline-secondary w-100 py-2.5 fw-semibold" onclick="submitSkipForm()">
                        Skip Profile Details & Post Question
                    </button>
                @endif

                <a href="{{ route('home') }}" class="btn btn-link text-muted small text-decoration-none text-center">Cancel</a>
            </div>
        </form>

        @if($hasPendingQuestion && !$isSecondQuestion)
        <form id="skipProfileForm" method="POST" action="{{ route('profile.complete.skip') }}" style="display: none;">
            @csrf
        </form>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countryEl = document.getElementById('profile_country');
        const stateEl = document.getElementById('profile_state');
        const districtEl = document.getElementById('profile_district');
        
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

        tsState.disable();
        tsDistrict.disable();

        // Load countries
        fetch('https://countriesnow.space/api/v0.1/countries/positions')
            .then(r => r.json())
            .then(data => {
                if (data.error === false) {
                    const opts = data.data.map(c => ({ value: c.name, text: c.name }));
                    tsCountry.addOptions(opts);
                    
                    // Preselect if already set on user
                    const userCountry = "{{ old('country', Auth::user()->country) }}";
                    if (userCountry && userCountry !== 'Not set') {
                        tsCountry.setValue(userCountry);
                    }
                }
            });

        // Country change -> load states
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
                    
                    // Preselect state if already set
                    const userState = "{{ old('state', Auth::user()->state) }}";
                    if (userState && userState !== 'Not set') {
                        tsState.setValue(userState);
                    }
                } else {
                    tsState.addOption({ value: 'N/A', text: 'N/A' });
                    tsState.setValue('N/A');
                    tsState.enable();
                    tsDistrict.addOption({ value: 'N/A', text: 'N/A' });
                    tsDistrict.setValue('N/A', true);
                    tsDistrict.enable();
                }
            })
            .catch(() => {
                tsState.addOption({ value: 'Unknown', text: 'Unknown' });
                tsState.setValue('Unknown');
                tsState.enable();
            });
        });

        // State change -> load cities/districts
        tsState.on('change', function(stateValue) {
            tsDistrict.clear(true);
            tsDistrict.clearOptions();
            tsDistrict.disable();

            if (!stateValue || stateValue === 'N/A' || stateValue === 'Unknown') return;

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
                    tsDistrict.enable();
                    
                    // Preselect district if already set
                    const userDistrict = "{{ old('district', Auth::user()->district) }}";
                    if (userDistrict && userDistrict !== 'Not set') {
                        tsDistrict.setValue(userDistrict);
                    }
                } else {
                    tsDistrict.addOption({ value: stateValue, text: stateValue });
                    tsDistrict.setValue(stateValue);
                    tsDistrict.enable();
                }
            })
            .catch(() => {
                tsDistrict.addOption({ value: stateValue, text: stateValue });
                tsDistrict.setValue(stateValue);
                tsDistrict.enable();
            });
        });
    });

    let isProfileSubmitting = false;
    const completeForm = document.getElementById('completeProfileForm');
    if (completeForm) {
        completeForm.addEventListener('submit', function(e) {
            if (isProfileSubmitting) {
                e.preventDefault();
                return false;
            }
            isProfileSubmitting = true;
            const submitBtn = completeForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Saving & Submitting...`;
            }
        });
    }

    function submitSkipForm() {
        if (isProfileSubmitting) return;
        if (confirm('Are you sure you want to skip completing your profile details for now? Region-specific rulings (Fatwas) often depend heavily on your locale and context.')) {
            isProfileSubmitting = true;
            document.getElementById('skipProfileForm').submit();
        }
    }
</script>
@endpush
@endsection
