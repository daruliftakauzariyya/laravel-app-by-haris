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

        <form method="POST" action="{{ route('register') }}" id="registrationForm">
            @csrf
            
            <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">Personal Details</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Your full name" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Email ID</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="your@email.com" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Mobile Number</label>
                    <div class="input-group">
                        <select name="country_code" id="country_code" class="form-select @error('country_code') is-invalid @enderror" style="max-width: 120px;" required>
                            <option value="">Code</option>
                            <!-- Populated via API -->
                        </select>
                        <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                               value="{{ old('mobile') }}" placeholder="Mobile Number" required>
                    </div>
                    @error('mobile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Gender</label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Madhab</label>
                    <select name="madhab" class="form-select @error('madhab') is-invalid @enderror" required>
                        <option value="No Preference">No Preference</option>
                        <option value="Hanafi" {{ old('madhab') == 'Hanafi' ? 'selected' : '' }}>Hanafi</option>
                        <option value="Shafii" {{ old('madhab') == 'Shafii' ? 'selected' : '' }}>Shafii</option>
                        <option value="Maliki" {{ old('madhab') == 'Maliki' ? 'selected' : '' }}>Maliki</option>
                        <option value="Hanbali" {{ old('madhab') == 'Hanbali' ? 'selected' : '' }}>Hanbali</option>
                        <option value="Other" {{ old('madhab') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3 mt-3">Address Details</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Address Line 1</label>
                    <input type="text" name="address_line_1" class="form-control @error('address_line_1') is-invalid @enderror" required value="{{ old('address_line_1') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Address Line 2</label>
                    <input type="text" name="address_line_2" class="form-control" value="{{ old('address_line_2') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Country</label>
                    <select name="country" id="country" class="form-select @error('country') is-invalid @enderror" required>
                        <option value="">Select Country</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">State</label>
                    <select name="state" id="state" class="form-select @error('state') is-invalid @enderror" required disabled>
                        <option value="">Select State</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">District</label>
                    <select name="district" id="district" class="form-select @error('district') is-invalid @enderror" required disabled>
                        <option value="">Select District</option>
                    </select>
                </div>
            </div>

            <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3 mt-3">Security</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="Min. 8 characters" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold mt-3" id="submitBtn">Register & Send OTP to Email</button>
        </form>
        <p class="text-center mt-3 small mb-0">Already have an account? <a href="{{ route('login') }}" class="text-green fw-semibold">Sign in</a></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country');
    const stateSelect = document.getElementById('state');
    const districtSelect = document.getElementById('district');
    const dialCodeSelect = document.getElementById('country_code');

    // Fetch Countries for Dial Codes
    fetch('https://countriesnow.space/api/v0.1/countries/codes')
        .then(response => response.json())
        .then(data => {
            if(data.error === false) {
                // Sort dial codes alphabetially by country
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

    // Country Change Event -> Load States
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
                    // No states found, allow free text or skip
                    let option = new Option("N/A", "N/A");
                    stateSelect.add(option);
                    stateSelect.disabled = false;
                    
                    // Also enable district since no state
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

    // State Change Event -> Load Districts/Cities
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
@endsection
