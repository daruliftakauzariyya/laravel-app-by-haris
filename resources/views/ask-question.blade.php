@extends('layouts.public')
@section('title', 'Ask a Question')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
<style>
    .ql-editor { font-size: 1rem; line-height: 1.6; min-height: 200px; }
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
</style>
@endpush

@section('content')
<div class="container py-5" style="max-width:720px;">
    <div class="mb-4">
        <h2 class="fw-bold text-green"><i class="bi bi-pencil-square me-2"></i>Ask a Question</h2>
        <p class="text-muted">Submit your Islamic question and receive a verified Fatwa from a qualified Mufti.</p>
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>
    <div class="card border-0 shadow p-4">
        <form method="POST" action="{{ route('question.store') }}" enctype="multipart/form-data" id="askForm">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Question Title / Summary <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                       placeholder="A brief summary of your question (max 200 chars)" maxlength="200" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Your Full Question <span class="text-danger">*</span></label>
                <div id="editor-container" style="background: #fff; min-height: 200px;">{!! old('body') !!}</div>
                <input type="hidden" name="body" id="body-input" value="{{ old('body') }}">
                @error('body')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Madhab <span class="text-danger">*</span></label>
                    <select name="madhab_id" class="form-select @error('madhab_id') is-invalid @enderror" required>
                        <option value="">Select Madhab...</option>
                        @foreach($madhabs as $m)
                            <option value="{{ $m->id }}" {{ old('madhab_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('madhab_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>


            @if(!Auth::user()->profile_completed || empty(Auth::user()->mobile))
            <div class="card border-0 bg-light p-4 mb-4 rounded-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-green" style="color: #1a7a4a;">
                        <i class="bi bi-person-badge me-2"></i>Complete Your Profile Details
                    </h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="skip_profile" id="skipProfileSwitch" value="1" {{ old('skip_profile') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold text-muted small" for="skipProfileSwitch">Skip details</label>
                    </div>
                </div>
                <p class="text-muted small mb-3">Completing these details is highly recommended as region-specific rulings (Fatwas) often depend on your locale and context.</p>
                
                <div id="profileDetailsSection" class="row g-3">
                    @if(empty(Auth::user()->mobile))
                    <div class="col-md-12">
                        <label class="form-label fw-semibold small">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" name="mobile" id="profile_mobile" class="form-control profile-input" value="{{ old('mobile') }}" placeholder="Enter your mobile number (with country code)" required>
                        @error('mobile')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Gender <span class="text-danger">*</span></label>
                        <select name="gender" id="profile_gender" class="form-select profile-input" required>
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Address Line 1 <span class="text-danger">*</span></label>
                        <input type="text" name="address_line_1" id="profile_address_1" class="form-control profile-input" value="{{ old('address_line_1') }}" placeholder="Street address, P.O. Box" required>
                        @error('address_line_1')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Address Line 2 <span class="text-muted">(Optional)</span></label>
                        <input type="text" name="address_line_2" id="profile_address_2" class="form-control" value="{{ old('address_line_2') }}" placeholder="Apartment, suite, unit, etc.">
                        @error('address_line_2')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Country <span class="text-danger">*</span></label>
                        <select name="country" id="profile_country" class="form-select profile-input" required>
                            <option value="">Select Country</option>
                        </select>
                        @error('country')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">State <span class="text-danger">*</span></label>
                        <select name="state" id="profile_state" class="form-select profile-input" required disabled>
                            <option value="">Select State</option>
                        </select>
                        @error('state')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">District <span class="text-danger">*</span></label>
                        <select name="district" id="profile_district" class="form-select profile-input" required disabled>
                            <option value="">Select District</option>
                        </select>
                        @error('district')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            @endif

            <div class="mb-4">
                <label class="form-label fw-semibold">Supporting Files <small class="text-muted">(Optional, PDF/DOC/Images, max 10MB each)</small></label>
                <input type="file" id="file-uploader" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                <div id="upload-status-list" class="mt-2"></div>
                <div id="hidden-uploads-container"></div>
                <div class="form-text mt-1"><i class="bi bi-info-circle me-1"></i>Accepted: PDF, DOC, DOCX, JPG, PNG — Max 10MB per file</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" id="submit-btn" class="btn btn-primary px-5 py-2 fw-bold">
                    <i class="bi bi-send me-2"></i>Submit Question
                </button>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 text-decoration-none">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    // --- Quill Editor Setup ---
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: { toolbar: [['bold'], [{ 'list': 'ordered'}, { 'list': 'bullet' }], ['clean']] },
        placeholder: 'Write your full question in detail here...'
    });

    // --- Async File Upload Logic ---
    const fileInput = document.getElementById('file-uploader');
    const statusList = document.getElementById('upload-status-list');
    const hiddenContainer = document.getElementById('hidden-uploads-container');
    const submitBtn = document.getElementById('submit-btn');
    let activeUploads = 0;

    fileInput.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files.length === 0) return;

        Array.from(files).forEach(file => {
            uploadFile(file);
        });
        
        // Reset file input so same file can be re-selected if removed
        fileInput.value = '';
    });

    function uploadFile(file) {
        activeUploads++;
        updateSubmitButton();

        // Create UI for this file
        const fileId = 'file-' + Math.random().toString(36).substr(2, 9);
        const item = document.createElement('div');
        item.id = fileId;
        item.className = 'd-flex justify-content-between align-items-center p-2 mb-1 border rounded bg-light small';
        item.innerHTML = `
            <span><i class="bi bi-file-earmark me-2"></i>${file.name}</span>
            <span class="upload-status text-primary"><span class="spinner-border spinner-border-sm me-1"></span>Uploading...</span>
        `;
        statusList.appendChild(item);

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("question.files.upload") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            activeUploads--;
            if (data.success) {
                // Success UI
                item.querySelector('.upload-status').innerHTML = `
                    <span class="text-success me-2"><i class="bi bi-check-circle-fill"></i> Uploaded</span>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 remove-btn" data-path="${data.path}">✕</button>
                `;
                // Add hidden input
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'uploaded_files[]';
                hidden.value = JSON.stringify({
                    path: data.path,
                    name: data.original_name,
                    type: data.file_type,
                    size: data.file_size
                });
                item.setAttribute('data-hidden-id', fileId + '-hidden');
                hidden.id = fileId + '-hidden';
                hiddenContainer.appendChild(hidden);
            } else {
                item.querySelector('.upload-status').innerHTML = `<span class="text-danger">Upload failed</span>`;
            }
            updateSubmitButton();
        })
        .catch(error => {
            activeUploads--;
            item.querySelector('.upload-status').innerHTML = `<span class="text-danger">Error</span>`;
            updateSubmitButton();
        });
    }

    function updateSubmitButton() {
        if (activeUploads > 0) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Uploading files...`;
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<i class="bi bi-send me-2"></i>Submit Question`;
        }
    }

    // Handle removal
    statusList.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-btn')) {
            const btn = e.target;
            const path = btn.getAttribute('data-path');
            const item = btn.closest('div');
            const hiddenId = item.getAttribute('data-hidden-id');

            // Optional: Tell server to delete the temp file
            fetch('{{ route("question.files.delete") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ path: path })
            });

            // Remove UI and hidden input
            item.remove();
            const hidden = document.getElementById(hiddenId);
            if (hidden) hidden.remove();
        }
    });

    document.getElementById('askForm').onsubmit = function() {
        var body = document.querySelector('input[name=body]');
        body.value = quill.root.innerHTML;
        if(quill.getText().trim().length === 0) {
            alert('Please enter your question.');
            return false;
        }
        
        // Disable button to prevent double-click
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Posting Question...`;
        
        return true;
    };
</script>

@if(!Auth::user()->profile_completed || empty(Auth::user()->mobile))
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const skipSwitch = document.getElementById('skipProfileSwitch');
        const profileInputs = document.querySelectorAll('.profile-input');
        const profileSection = document.getElementById('profileDetailsSection');
        
        // Setup country, state, district selects
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

        // State change -> load cities/districts
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

        if (skipSwitch) {
            function toggleProfileRequired() {
                if (skipSwitch.checked) {
                    profileSection.style.opacity = '0.4';
                    profileSection.style.pointerEvents = 'none';
                    profileInputs.forEach(input => {
                        input.removeAttribute('required');
                        if (input.tomselect) {
                            input.tomselect.disable();
                        } else {
                            input.disabled = true;
                        }
                    });
                } else {
                    profileSection.style.opacity = '1';
                    profileSection.style.pointerEvents = 'auto';
                    profileInputs.forEach(input => {
                        if (input.id === 'profile_gender' || input.id === 'profile_address_1' || input.id === 'profile_country' || input.id === 'profile_mobile') {
                            input.setAttribute('required', '');
                        }
                        if (input.tomselect) {
                            if (input.id === 'profile_country') {
                                input.tomselect.enable();
                            } else if (input.id === 'profile_state' && tsCountry.getValue()) {
                                input.tomselect.enable();
                            } else if (input.id === 'profile_district' && tsState.getValue()) {
                                input.tomselect.enable();
                            }
                        } else {
                            input.disabled = false;
                        }
                    });
                }
            }
            
            skipSwitch.addEventListener('change', toggleProfileRequired);
            // Run on load
            toggleProfileRequired();
        }
    });
</script>
@endif
@endpush
@endsection
