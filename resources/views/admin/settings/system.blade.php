@extends('layouts.admin')
@section('title', 'System Settings')
@section('topbar-title', 'System Control Panel')
@section('content')

<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-gear-wide-connected me-2 text-brand"></i>Institutional Configuration</h5>
                    <p class="text-muted small mb-0">Manage global system behavior, lunar calendar alignments, and public display preferences.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Moon Sighting & Hijri Manager --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-moon-stars me-2 text-success"></i>Moon Sighting & Hijri Manager</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <p class="text-muted small mb-1">Current Calculated Date (Adjusted):</p>
                            <h4 class="fw-bold text-success mb-0">{{ $currentHijri }}</h4>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">Active</span>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.update') }}" method="POST" class="border-top pt-3">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-7">
                                <label class="form-label small fw-bold text-muted">Manual Date Adjustment (Days)</label>
                                <select name="hijri_adjustment" class="form-select border-success-subtle">
                                    <option value="-2" {{ $hijriAdjustment == -2 ? 'selected' : '' }}>-2 Days (Earlier)</option>
                                    <option value="-1" {{ $hijriAdjustment == -1 ? 'selected' : '' }}>-1 Day (Earlier)</option>
                                    <option value="0"  {{ $hijriAdjustment == 0  ? 'selected' : '' }}>0 (Standard Calculation)</option>
                                    <option value="1"  {{ $hijriAdjustment == 1  ? 'selected' : '' }}>+1 Day (Later)</option>
                                    <option value="2"  {{ $hijriAdjustment == 2  ? 'selected' : '' }}>+2 Days (Later)</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-calendar-check me-1"></i>Apply Shift
                                </button>
                            </div>
                        </div>
                        <p class="mt-3 x-small text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-info-circle me-1"></i> This setting aligns the portal's calendar with local moon sightings for Ramzan and Eid announcements.
                        </p>
                    </form>
                </div>
            </div>
        </div>

        {{-- Public Display Control --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-eye me-2 text-primary"></i>Public Display Control</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-muted small">Determine if the Hijri date should be visible to all visitors on the public header.</p>
                        
                        <form action="{{ route('admin.settings.update') }}" method="POST" id="hijriToggleForm">
                            @csrf
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="show_hijri_public" value="1" id="hijriSwitch" 
                                       {{ \App\Models\SiteSetting::get('show_hijri_public', true) ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <label class="form-check-label fw-bold" for="hijriSwitch">Enable Arabic Calendar for Public</label>
                            </div>
                        </form>
                    </div>

                    <div class="bg-light p-3 rounded text-center border mt-4">
                        <span class="text-muted x-small d-block mb-1">Live Preview:</span>
                        <div class="badge bg-success p-2 px-3 shadow-sm rounded-pill"><i class="bi bi-moon-stars me-2"></i>{{ $currentHijri }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Admin Account Settings --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px; border-left: 4px solid #dc3545 !important;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-shield-lock me-2"></i>Admin Login Credentials</h6>
                    <p class="text-muted small mb-0">Update the primary administrator account's email address and sign-in password.</p>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Admin Login Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="admin_email" class="form-control border-start-0 ps-0 @error('admin_email') is-invalid @enderror" 
                                           value="{{ old('admin_email', auth()->user()->email) }}" placeholder="admin@darulifta.com" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">New Password (Min 8 chars)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                    <input type="password" name="admin_password" class="form-control border-start-0 ps-0 @error('admin_password') is-invalid @enderror" 
                                           placeholder="••••••••" autocomplete="new-password">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Confirm New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-key-fill"></i></span>
                                    <input type="password" name="admin_password_confirmation" class="form-control border-start-0 ps-0" 
                                           placeholder="••••••••" autocomplete="new-password">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 border-top pt-3 text-end">
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="bi bi-shield-check me-1"></i>Update Admin Credentials
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-brand { color: var(--brand-blue); }
    .bg-brand-light { background-color: rgba(41, 90, 117, 0.08); }
    .x-small { font-size: 0.75rem; }
</style>

@endsection
