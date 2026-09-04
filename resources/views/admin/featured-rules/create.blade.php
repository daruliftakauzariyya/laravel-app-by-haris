@extends('layouts.admin')
@section('title', 'Create Featured Rule')
@section('topbar-title', 'New Featured Fatwa Rule')
@section('content')

@php
$hijriMonths = [
    1=>'Muharram', 2=>'Safar', 3=>"Rabi' al-Awwal", 4=>"Rabi' al-Thani",
    5=>'Jumada al-Awwal', 6=>'Jumada al-Thani', 7=>'Rajab', 8=>"Sha'ban",
    9=>'Ramadan', 10=>'Shawwal', 11=>"Dhul Qa'dah", 12=>'Dhul Hijjah',
];
$commonCountries = [
    'SA'=>'Saudi Arabia','IN'=>'India','PK'=>'Pakistan','BD'=>'Bangladesh',
    'MY'=>'Malaysia','ID'=>'Indonesia','EG'=>'Egypt','TR'=>'Turkey',
    'GB'=>'United Kingdom','US'=>'United States','AE'=>'UAE','QA'=>'Qatar',
    'KW'=>'Kuwait','BH'=>'Bahrain','OM'=>'Oman','LK'=>'Sri Lanka',
    'MV'=>'Maldives','ZA'=>'South Africa','NG'=>'Nigeria','GH'=>'Ghana',
];
$rule = old();
@endphp

<div class="mb-4">
    <a href="{{ route('admin.featured-rules.index') }}" class="text-muted small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i>Back to Rules
    </a>
</div>

<form action="{{ route('admin.featured-rules.store') }}" method="POST">
@csrf

<div class="row g-4">
    {{-- Main Settings --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0">Rule Details</h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Rule Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="e.g. Ramadan Fasting Fatwas" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Section Title on Home Page <span class="text-danger">*</span></label>
                        <input type="text" name="section_label" class="form-control"
                               value="{{ old('section_label', 'Most Useful & Readable Fatwas') }}" required>
                        <small class="text-muted">Shown as the section heading</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Section Subtitle</label>
                        <input type="text" name="section_sub" class="form-control"
                               value="{{ old('section_sub') }}" placeholder="Short description...">
                    </div>
                </div>
            </div>
        </div>

        {{-- Conditions --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between">
                <h6 class="fw-bold mb-0">Trigger Conditions</h6>
                <span class="badge bg-info text-dark small">All checked conditions must match</span>
            </div>
            <div class="card-body p-4">

                {{-- Hijri Month --}}
                <div class="condition-block p-3 mb-3 rounded-3" style="background:#f8fafc; border:1.5px solid #e2e8f0;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="fs-5">📅</span>
                        <span class="fw-semibold">Islamic Calendar (Hijri)</span>
                        <span class="text-muted small">(optional)</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Hijri Month</label>
                            <select name="hijri_month" class="form-select">
                                <option value="">— Any Month —</option>
                                @foreach($hijriMonths as $num => $name)
                                <option value="{{ $num }}" {{ old('hijri_month') == $num ? 'selected' : '' }}>
                                    {{ $num }}. {{ $name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Day Start</label>
                            <input type="number" name="hijri_day_start" class="form-control"
                                   value="{{ old('hijri_day_start') }}" min="1" max="30" placeholder="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Day End</label>
                            <input type="number" name="hijri_day_end" class="form-control"
                                   value="{{ old('hijri_day_end') }}" min="1" max="30" placeholder="30">
                        </div>
                    </div>
                    <small class="text-muted">Leave day fields empty to match entire month.</small>
                </div>

                {{-- Countries --}}
                <div class="condition-block p-3 mb-3 rounded-3" style="background:#f8fafc; border:1.5px solid #e2e8f0;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="fs-5">🌍</span>
                        <span class="fw-semibold">Visitor Location (Country)</span>
                        <span class="text-muted small">(optional)</span>
                    </div>
                    <div class="row g-2">
                        @foreach($commonCountries as $code => $name)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="country_codes[]" value="{{ $code }}"
                                       id="country_{{ $code }}"
                                       {{ in_array($code, old('country_codes', [])) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="country_{{ $code }}">
                                    {{ $name }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Content Filter --}}
                <div class="condition-block p-3 rounded-3" style="background:#f8fafc; border:1.5px solid #e2e8f0;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="fs-5">📂</span>
                        <span class="fw-semibold">Fatwa Content Filter</span>
                        <span class="text-muted small">(optional)</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">— All Categories —</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Madhab</label>
                            <select name="madhab_id" class="form-select">
                                <option value="">— All Madhabs —</option>
                                @foreach($madhabs as $m)
                                <option value="{{ $m->id }}" {{ old('madhab_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Pin specific fatwas (REQUIRED) --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px; border:2px solid #fbbf24 !important;">
            <div class="card-header border-0 py-3 px-4" style="background:linear-gradient(135deg,#fffbeb,#fff8e1);">
                <h6 class="fw-bold mb-0">📌 Pin Specific Fatwas <span class="text-danger">*</span></h6>
                <small class="text-muted">Select at least 1 fatwa. These will be shown in this featured section. Empty slots auto-filled by category/views.</small>
            </div>
            <div class="card-body p-4">
                {{-- Search Box --}}
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="fatwa-search" class="form-control border-start-0"
                           placeholder="Search by title, question content, or ID...">
                    <span class="input-group-text bg-white text-muted small" id="selected-count">0 selected</span>
                </div>

                {{-- Checkbox List --}}
                <div id="fatwa-list" style="max-height:360px; overflow-y:auto; border:1px solid #e2e8f0; border-radius:10px;">
                    @foreach($fatwas as $f)
                    @php $title = strip_tags($f->title ?: $f->body); @endphp
                    <label class="fatwa-pick-item d-flex align-items-start gap-3 p-3 border-bottom"
                           style="cursor:pointer; transition:background 0.15s;"
                           data-title="{{ strtolower($title) }} {{ $f->id }}">
                        <input type="checkbox" name="fatwa_ids[]" value="{{ $f->id }}"
                               class="form-check-input fatwa-checkbox mt-1 flex-shrink-0"
                               {{ in_array($f->id, old('fatwa_ids', [])) ? 'checked' : '' }}>
                        <div>
                            <div class="fw-semibold small" style="color:#1e293b;">{{ Str::limit($title, 80) }}</div>
                            <div class="text-muted" style="font-size:0.72rem;">
                                ID #{{ $f->question_id ?? $f->id }}
                                @if($f->madhab) · {{ $f->madhab?->name }} @endif
                                @if($f->category) · {{ $f->category?->name }} @endif
                            </div>
                        </div>
                    </label>
                    @endforeach
                    <div id="no-results" class="text-center py-4 text-muted small d-none">
                        <i class="bi bi-search d-block fs-4 mb-1 opacity-25"></i>No fatwas match your search.
                    </div>
                </div>

                {{-- Hidden validation trigger --}}
                <input type="hidden" id="fatwa-required-guard" value="" required>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-danger fw-semibold" id="fatwa-error" style="display:none;">
                        <i class="bi bi-exclamation-circle me-1"></i>Please select at least 1 fatwa.
                    </small>
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" id="clear-fatwas">Clear All</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Settings --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4 sticky-top" style="border-radius:14px; top:80px;">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0">Rule Settings</h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Fatwas to Show</label>
                    <select name="limit" class="form-select">
                        @foreach([3,4,6,8,12] as $n)
                        <option value="{{ $n }}" {{ old('limit', 6) == $n ? 'selected' : '' }}>{{ $n }} fatwas</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Priority</label>
                    <input type="number" name="priority" class="form-control"
                           value="{{ old('priority', 0) }}" min="0" max="100">
                    <small class="text-muted">Higher number = checked first. Default: 0</small>
                </div>
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               id="is_active" {{ old('is_active', 1) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">Active</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 fw-bold rounded-pill">
                    <i class="bi bi-plus-circle me-2"></i>Create Rule
                </button>
                <a href="{{ route('admin.featured-rules.index') }}" class="btn btn-link w-100 text-muted mt-2">
                    Cancel
                </a>
            </div>
        </div>

        {{-- Example Rules Hint --}}
        <div class="card border-0 p-3" style="border-radius:14px; background:#fffbeb; border:1px solid #fcd34d !important;">
            <h6 class="fw-bold small mb-2">💡 Example Configurations</h6>
            <div class="small text-dark">
                <div class="mb-2"><strong>Ramadan Fasting:</strong><br>Month = 9, Category = Fasting</div>
                <div class="mb-2"><strong>Hajj Season (SA visitors):</strong><br>Month = 12, Countries = SA, Category = Hajj</div>
                <div><strong>Eid Qurbani:</strong><br>Month = 12, Day 10–13, Category = Slaughter/Qurbani</div>
            </div>
        </div>
    </div>
</div>
</form>

@push('scripts')
<script>
(function() {
    const searchInput = document.getElementById('fatwa-search');
    const items       = document.querySelectorAll('.fatwa-pick-item');
    const noResults   = document.getElementById('no-results');
    const countBadge  = document.getElementById('selected-count');
    const errorMsg    = document.getElementById('fatwa-error');
    const guard       = document.getElementById('fatwa-required-guard');
    const clearBtn    = document.getElementById('clear-fatwas');

    function updateCount() {
        const checked = document.querySelectorAll('.fatwa-checkbox:checked').length;
        countBadge.textContent = checked + ' selected';
        countBadge.style.color = checked > 0 ? '#1a7a4a' : '#94a3b8';
        guard.value = checked > 0 ? 'ok' : '';
        errorMsg.style.display = checked === 0 ? 'block' : 'none';
    }

    // Search filter
    searchInput.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        let visible = 0;
        items.forEach(item => {
            const match = !q || item.dataset.title.includes(q);
            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        noResults.classList.toggle('d-none', visible > 0);
    });

    // Hover style
    items.forEach(item => {
        item.addEventListener('mouseenter', () => { if (!item.querySelector('input').checked) item.style.background = '#f8fafc'; });
        item.addEventListener('mouseleave', () => { if (!item.querySelector('input').checked) item.style.background = ''; });
        item.querySelector('input').addEventListener('change', function() {
            item.style.background = this.checked ? '#f0fdf4' : '';
            updateCount();
        });
        // Apply initial highlight
        if (item.querySelector('input').checked) item.style.background = '#f0fdf4';
    });

    clearBtn.addEventListener('click', () => {
        document.querySelectorAll('.fatwa-checkbox').forEach(cb => {
            cb.checked = false;
            cb.closest('.fatwa-pick-item').style.background = '';
        });
        updateCount();
    });

    // Form submit validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.fatwa-checkbox:checked').length;
        if (checked === 0) {
            e.preventDefault();
            errorMsg.style.display = 'block';
            document.getElementById('fatwa-list').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    updateCount();
}());
</script>
@endpush
@endsection
