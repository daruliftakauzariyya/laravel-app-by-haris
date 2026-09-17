@extends('layouts.public')
@section('title', 'Account Settings — Darul Ifta Kauzariyya')

@section('content')

<style>
    :root {
        --sett-primary: #052961;
        --sett-primary-dark: #031b40;
        --sett-gold: #c9a84c;
        --sett-gold-light: #fdfaf3;
        --sett-surface: #ffffff;
        --sett-bg: #f8fafc;
        --sett-border: #e2e8f0;
        --sett-text: #0f172a;
        --sett-muted: #64748b;
    }

    body {
        background-color: var(--sett-bg);
    }

    .settings-canvas {
        min-height: 85vh;
        padding-top: 1.5rem;
        padding-bottom: 4rem;
    }

    @media (min-width: 768px) {
        .settings-canvas {
            padding-top: 2.25rem;
            padding-bottom: 5rem;
        }
    }

    /* ── Header Card ── */
    .settings-header-card {
        background: linear-gradient(135deg, #052961 0%, #0d3b82 60%, #1e4b9c 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        color: #ffffff;
        box-shadow: 0 12px 36px rgba(5, 41, 97, 0.12);
        margin-bottom: 2rem;
        padding: 1.75rem 1.5rem;
    }

    @media (min-width: 768px) {
        .settings-header-card {
            border-radius: 24px;
            padding: 2.25rem 2.5rem;
        }
    }

    .settings-pattern {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: radial-gradient(rgba(201, 168, 76, 0.12) 1px, transparent 1px);
        background-size: 20px 20px;
        opacity: 0.8;
        pointer-events: none;
    }

    .settings-header-inner {
        position: relative;
        z-index: 2;
    }

    .settings-user-avatar {
        width: 74px;
        height: 74px;
        border-radius: 20px;
        background: #ffffff;
        padding: 3px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        flex-shrink: 0;
    }

    .settings-user-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 17px;
        object-fit: cover;
    }

    /* ── Navigation Tabs ── */
    .settings-nav-card {
        background: #ffffff;
        border: 1px solid var(--sett-border);
        border-radius: 18px;
        padding: 0.5rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.02);
    }

    .settings-nav-card .nav-link {
        color: var(--sett-muted) !important;
        font-weight: 700;
        font-size: 0.92rem;
        padding: 0.85rem 1.15rem !important;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.2s ease;
        border: none;
        text-align: left;
    }

    .settings-nav-card .nav-link:hover {
        background: #f1f5f9;
        color: var(--sett-primary) !important;
    }

    .settings-nav-card .nav-link.active {
        background: #052961 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(5, 41, 97, 0.2);
    }

    .settings-nav-card .nav-link.active i {
        color: #f6e09e !important;
    }

    /* ── Content Card ── */
    .settings-panel-card {
        background: #ffffff;
        border: 1px solid var(--sett-border);
        border-radius: 22px;
        padding: 1.5rem;
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.03);
    }

    @media (min-width: 768px) {
        .settings-panel-card {
            padding: 2.25rem;
        }
    }

    .panel-header-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--sett-text);
        margin-bottom: 0.25rem;
    }

    .panel-header-sub {
        font-size: 0.88rem;
        color: var(--sett-muted);
        margin-bottom: 1.5rem;
    }

    /* Form Controls */
    .form-floating-clean .form-control,
    .form-floating-clean .form-select {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
        font-size: 0.92rem;
        font-weight: 500;
        color: #0f172a;
        background-color: #ffffff;
        transition: all 0.2s ease;
    }

    .form-floating-clean .form-control:focus,
    .form-floating-clean .form-select:focus {
        border-color: #052961;
        box-shadow: 0 0 0 4px rgba(5, 41, 97, 0.08);
    }

    .form-floating-clean label {
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 1rem 0.85rem;
    }

    /* Avatar Upload Preview */
    .avatar-dropzone {
        width: 86px;
        height: 86px;
        border-radius: 20px;
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        position: relative;
        cursor: pointer;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .avatar-dropzone:hover {
        border-color: #052961;
        background: #f1f5f9;
    }

    .avatar-dropzone-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-dropzone-overlay {
        position: absolute;
        inset: 0;
        background: rgba(5, 41, 97, 0.6);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        opacity: 0;
        transition: opacity 0.2s ease;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .avatar-dropzone:hover .avatar-dropzone-overlay {
        opacity: 1;
    }

    /* Social Card (Google) */
    .oauth-provider-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.35rem 1.5rem;
        background: #ffffff;
        transition: all 0.25s ease;
    }

    .oauth-provider-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    }

    .oauth-icon-badge {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .btn-connect-google {
        background: #ffffff;
        color: #3c4043;
        border: 1.5px solid #dadce0;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 0.55rem 1.25rem;
        border-radius: 12px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-connect-google:hover {
        background: #f8fafc;
        border-color: #052961;
        color: #052961;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .btn-disconnect-google {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.55rem 1.15rem;
        border-radius: 12px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .btn-disconnect-google:hover {
        background: #fecaca;
        color: #991b1b;
    }

    .btn-save-settings {
        background: linear-gradient(135deg, #052961 0%, #0d3b82 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.92rem;
        padding: 0.7rem 1.75rem;
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 14px rgba(5, 41, 97, 0.2);
        transition: all 0.2s ease;
    }

    .btn-save-settings:hover {
        background: #031b40;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(5, 41, 97, 0.28);
    }
</style>

<div class="user-dashboard-canvas settings-canvas">
    <div class="container">

        {{-- 1. Hero / Header Card --}}
        <div class="settings-header-card">
            <div class="settings-pattern"></div>
            <div class="settings-header-inner">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="settings-user-avatar">
                            <img src="{{ $user->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=052961&color=fff&size=160' }}" 
                                 alt="{{ $user->name }}"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=052961&color=fff&size=160'">
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <h1 class="h3 fw-bold mb-0 text-white">{{ $user->name }}</h1>
                                <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1 rounded-pill small fw-bold">
                                    <i class="bi bi-shield-check me-1 text-warning"></i>{{ $user->role?->name ?? 'Questioner' }}
                                </span>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-3 text-white-50 small">
                                <span><i class="bi bi-envelope me-1"></i>{{ $user->email }}</span>
                                <span><i class="bi bi-calendar3 me-1"></i>Joined {{ $user->created_at ? $user->created_at->format('M Y') : '2026' }}</span>
                                @if($user->email_verified_at)
                                    <span class="text-success-emphasis bg-success-subtle px-2 py-0.5 rounded-pill border border-success-subtle fw-semibold" style="font-size: 0.72rem;">
                                        <i class="bi bi-check-circle-fill me-1"></i>Verified
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                            <i class="bi bi-grid-fill"></i>
                            <span>Inquiry Dashboard</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Flash Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show rounded-4 mb-4 border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center gap-2 mb-1 fw-bold">
                    <i class="bi bi-exclamation-octagon-fill text-danger fs-5"></i> Please resolve the following errors:
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- 3. Settings Tabs Layout --}}
        <div class="row g-4">
            {{-- Left Navigation Pills --}}
            <div class="col-12 col-lg-3">
                <div class="settings-nav-card">
                    <div class="nav flex-column nav-pills" id="settingsTabs" role="tablist">
                        <button class="nav-link active" id="tab-profile-btn" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button" role="tab">
                            <i class="bi bi-person-bounding-box fs-5"></i>
                            <div>
                                <div>Profile Details</div>
                                <small class="text-muted d-block fw-normal" style="font-size: 0.74rem;">Name, Madhab & Contact</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-social-btn" data-bs-toggle="pill" data-bs-target="#tab-social" type="button" role="tab">
                            <i class="bi bi-google fs-5"></i>
                            <div>
                                <div>Connected Accounts</div>
                                <small class="text-muted d-block fw-normal" style="font-size: 0.74rem;">Google OAuth linking</small>
                            </div>
                        </button>

                        <button class="nav-link" id="tab-security-btn" data-bs-toggle="pill" data-bs-target="#tab-security" type="button" role="tab">
                            <i class="bi bi-shield-lock-fill fs-5"></i>
                            <div>
                                <div>Password & Security</div>
                                <small class="text-muted d-block fw-normal" style="font-size: 0.74rem;">Change login password</small>
                            </div>
                        </button>
                    </div>
                </div>

                {{-- Help Card --}}
                <div class="card border-0 rounded-4 shadow-sm p-3 mt-3 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-bold small">
                        <i class="bi bi-patch-question-fill fs-5 text-warning"></i>
                        <span>Need Assistance?</span>
                    </div>
                    <p class="text-muted small mb-2" style="font-size: 0.8rem;">
                        If you need to update your registered email or merge accounts, please contact our support desk.
                    </p>
                    <a href="{{ route('contact') }}" class="small fw-semibold text-decoration-none text-primary">
                        Contact Darul Ifta &rarr;
                    </a>
                </div>
            </div>

            {{-- Right Content Panels --}}
            <div class="col-12 col-lg-9">
                <div class="tab-content" id="settingsTabsContent">

                    {{-- =========================================================================
                         TAB 1: PROFILE DETAILS
                         ========================================================================= --}}
                    <div class="tab-pane fade show active" id="tab-profile" role="tabpanel">
                        <div class="settings-panel-card">
                            <div class="panel-header-title">Personal Profile & Preferences</div>
                            <div class="panel-header-sub">Manage your personal information, public display name, and jurisprudential madhab preference.</div>

                            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- Avatar & Basic Info --}}
                                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 p-3 rounded-4 bg-light mb-4 border border-light-subtle">
                                    <label class="avatar-dropzone" for="avatar_file_input" title="Click to upload profile picture">
                                        <img src="{{ $user->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=052961&color=fff&size=160' }}" 
                                             id="avatarPreviewImg" 
                                             class="avatar-dropzone-img" 
                                             alt="{{ $user->name }}">
                                        <div class="avatar-dropzone-overlay">
                                            <i class="bi bi-camera-fill fs-5 mb-1"></i>
                                            <span>Change</span>
                                        </div>
                                    </label>
                                    <input type="file" name="avatar_file" id="avatar_file_input" class="d-none" accept="image/jpeg,image/png,image/jpg,image/webp">
                                    
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">Profile Photo</h6>
                                        <p class="text-muted small mb-2">Upload JPG, PNG or WEBP (Max 2MB). Recommended square aspect ratio.</p>
                                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="document.getElementById('avatar_file_input').click();">
                                            <i class="bi bi-upload me-1"></i>Select File
                                        </button>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    {{-- Full Name --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required placeholder="Full Name">
                                            <label for="name">Full Legal Name *</label>
                                        </div>
                                    </div>

                                    {{-- Public Display Name --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <input type="text" class="form-control" id="despl_name" name="despl_name" value="{{ old('despl_name', $user->despl_name) }}" placeholder="Public Display Name">
                                            <label for="despl_name">Public Display Name (Shown on Inquiries)</label>
                                        </div>
                                        <div class="form-text small text-muted px-1">
                                            <i class="bi bi-info-circle me-1"></i>Shown on published fatwas to protect your privacy.
                                        </div>
                                    </div>

                                    {{-- Email (Read-Only) --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <input type="email" class="form-control bg-light" id="email" value="{{ $user->email }}" readonly disabled>
                                            <label for="email">Registered Email Address</label>
                                        </div>
                                        <div class="form-text small text-muted px-1">
                                            <i class="bi bi-lock-fill me-1"></i>Email address cannot be modified directly for security.
                                        </div>
                                    </div>

                                    {{-- Mobile Number --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <input type="tel" class="form-control" id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}" placeholder="Mobile Number">
                                            <label for="mobile">Mobile / WhatsApp Number</label>
                                        </div>
                                    </div>

                                    {{-- Madhab Preference --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <select class="form-select" id="madhab_id" name="madhab_id">
                                                <option value="">-- Select Jurisprudential Madhab --</option>
                                                @foreach($madhabs as $madhab)
                                                    <option value="{{ $madhab->id }}" {{ old('madhab_id', $user->madhab_id) == $madhab->id ? 'selected' : '' }}>
                                                        {{ $madhab->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="madhab_id">Jurisprudential Madhab (ഫിഖ്ഹ് മദ്ഹബ്)</label>
                                        </div>
                                    </div>

                                    {{-- Gender --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <select class="form-select" id="gender" name="gender">
                                                <option value="">-- Select Gender --</option>
                                                <option value="Male" {{ old('gender', $user->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ old('gender', $user->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                            <label for="gender">Gender</label>
                                        </div>
                                    </div>

                                    {{-- Mother Language --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <input type="text" class="form-control" id="mother_language" name="mother_language" value="{{ old('mother_language', $user->mother_language) }}" placeholder="Mother Language">
                                            <label for="mother_language">Mother Tongue / Language</label>
                                        </div>
                                    </div>

                                    {{-- Country --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $user->country) }}" placeholder="Country">
                                            <label for="country">Country of Residence</label>
                                        </div>
                                    </div>

                                    {{-- State --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $user->state) }}" placeholder="State">
                                            <label for="state">State / Province</label>
                                        </div>
                                    </div>

                                    {{-- District --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean">
                                            <input type="text" class="form-control" id="district" name="district" value="{{ old('district', $user->district) }}" placeholder="District">
                                            <label for="district">District / City</label>
                                        </div>
                                    </div>

                                    {{-- Address Lines --}}
                                    <div class="col-12">
                                        <div class="form-floating form-floating-clean">
                                            <input type="text" class="form-control" id="address_line_1" name="address_line_1" value="{{ old('address_line_1', $user->address_line_1) }}" placeholder="Address Line 1">
                                            <label for="address_line_1">Address Line</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-2 d-flex justify-content-end">
                                    <button type="submit" class="btn-save-settings">
                                        <i class="bi bi-check2-circle me-1"></i>Save Profile Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- =========================================================================
                         TAB 2: CONNECTED ACCOUNTS (GOOGLE OAUTH)
                         ========================================================================= --}}
                    <div class="tab-pane fade" id="tab-social" role="tabpanel">
                        <div class="settings-panel-card">
                            <div class="panel-header-title">Connected Accounts & Social Login</div>
                            <div class="panel-header-sub">Link your third-party accounts for fast, seamless one-click sign-in without passwords.</div>

                            {{-- Google OAuth Card --}}
                            <div class="oauth-provider-card mb-4">
                                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="oauth-icon-badge">
                                            <svg width="26" height="26" viewBox="0 0 18 18">
                                                <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.616z"/>
                                                <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                                                <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.175 0 7.55 0 9s.347 2.825.957 4.039l3.007-2.332z"/>
                                                <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.166 6.656 3.58 9 3.58z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                <h6 class="fw-bold mb-0 text-dark fs-6">Google Account</h6>
                                                @if($user->google_id)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-bold">
                                                        <i class="bi bi-check-circle-fill me-1"></i>Connected
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                                        Not Connected
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-muted small">
                                                @if($user->google_id)
                                                    Your Google account is linked to this profile. You can sign in using "Continue with Google".
                                                @else
                                                    Connect your Google account to log in with one click without entering your password.
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex-shrink-0">
                                        @if($user->google_id)
                                            <form action="{{ route('user.google.unlink') }}" method="POST" onsubmit="return confirm('Are you sure you want to disconnect your Google account? You will still be able to sign in with your email or password.');">
                                                @csrf
                                                <button type="submit" class="btn-disconnect-google">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                    <span>Disconnect Google</span>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('user.google.link') }}" id="btnConnectGoogle" onclick="handleGoogleConnectClick(event)" class="btn-connect-google">
                                                <svg width="18" height="18" viewBox="0 0 18 18">
                                                    <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.616z"/>
                                                    <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                                                    <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.175 0 7.55 0 9s.347 2.825.957 4.039l3.007-2.332z"/>
                                                    <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.166 6.656 3.58 9 3.58z"/>
                                                </svg>
                                                <span>Connect with Google</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Informational banner --}}
                            <div class="p-3 rounded-4 bg-light border border-light-subtle d-flex align-items-start gap-3">
                                <i class="bi bi-shield-check text-success fs-4 mt-1"></i>
                                <div class="small text-muted">
                                    <strong class="text-dark d-block mb-1">Privacy & Security Guarantee</strong>
                                    Darul Ifta Kauzariyya only requests basic profile verification (your email, name, and profile picture) from Google. We never access your contacts, private drive files, or emails.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- =========================================================================
                         TAB 3: PASSWORD & SECURITY
                         ========================================================================= --}}
                    <div class="tab-pane fade" id="tab-security" role="tabpanel">
                        <div class="settings-panel-card">
                            <div class="panel-header-title">Password & Login Security</div>
                            <div class="panel-header-sub">Manage your account authentication credentials and security options.</div>

                            <form action="{{ route('user.password.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    @if(!empty($user->password))
                                        {{-- Current Password --}}
                                        <div class="col-12">
                                            <div class="form-floating form-floating-clean position-relative">
                                                <input type="password" class="form-control pe-5" id="current_password" name="current_password" required placeholder="Current Password">
                                                <label for="current_password">Current Password *</label>
                                                <button type="button" class="btn btn-link text-muted position-absolute end-0 top-50 translate-middle-y me-2 p-1" onclick="togglePassView('current_password', this)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Google-Only Account Note --}}
                                        <div class="col-12">
                                            <div class="alert alert-info border-0 rounded-4 d-flex align-items-center gap-2 mb-0">
                                                <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                                                <div class="small">
                                                    You registered via Google and currently do not have a password set. You can set a password below to allow sign-in via email and password.
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- New Password --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean position-relative">
                                            <input type="password" class="form-control pe-5" id="new_password" name="password" required minlength="8" placeholder="New Password">
                                            <label for="new_password">New Password *</label>
                                            <button type="button" class="btn btn-link text-muted position-absolute end-0 top-50 translate-middle-y me-2 p-1" onclick="togglePassView('new_password', this)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text small text-muted px-1">Must be at least 8 characters.</div>
                                    </div>

                                    {{-- Confirm New Password --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating form-floating-clean position-relative">
                                            <input type="password" class="form-control pe-5" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder="Confirm Password">
                                            <label for="password_confirmation">Confirm New Password *</label>
                                            <button type="button" class="btn btn-link text-muted position-absolute end-0 top-50 translate-middle-y me-2 p-1" onclick="togglePassView('password_confirmation', this)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-2 d-flex justify-content-end">
                                    <button type="submit" class="btn-save-settings">
                                        <i class="bi bi-key-fill me-1"></i>
                                        <span>{{ empty($user->password) ? 'Set Account Password' : 'Update Password' }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Live Avatar Preview
    document.getElementById('avatar_file_input')?.addEventListener('change', function (e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function (event) {
                const img = document.getElementById('avatarPreviewImg');
                if (img) img.src = event.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Toggle Password Visibility
    function togglePassView(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Auto switch to tab if hash in URL (e.g. /user/settings#tab-social)
    window.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash;
        if (hash) {
            const tabBtn = document.querySelector(`button[data-bs-target="${hash}"]`);
            if (tabBtn) {
                const tab = new bootstrap.Tab(tabBtn);
                tab.show();
            }
        }
    });

    // ── Google Connect Popup Handler ──
    let googleConnectTokenClient = null;

    function initGoogleConnect() {
        const clientId = "{{ config('services.google.client_id') }}";
        if (!clientId || typeof google === 'undefined' || !google.accounts) return;

        try {
            if (google.accounts.oauth2) {
                googleConnectTokenClient = google.accounts.oauth2.initTokenClient({
                    client_id: clientId,
                    scope: 'email profile https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
                    callback: handleGoogleConnectResponse,
                });
            }
        } catch (err) {
            console.warn('Google Connect Init Warning:', err);
        }
    }

    function handleGoogleConnectClick(e) {
        if (e) e.preventDefault();
        if (googleConnectTokenClient) {
            googleConnectTokenClient.requestAccessToken({ prompt: 'select_account' });
        } else {
            window.location.href = "{{ route('user.google.link') }}";
        }
    }

    function handleGoogleConnectResponse(tokenResponse) {
        if (tokenResponse && tokenResponse.access_token) {
            const btn = document.getElementById('btnConnectGoogle');
            if (btn) {
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Connecting...`;
            }

            fetch("{{ route('auth.google.token') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    access_token: tokenResponse.access_token,
                    action: 'link'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to connect Google account.');
                    if (btn) {
                        btn.innerHTML = `<span>Connect with Google</span>`;
                    }
                }
            })
            .catch(err => {
                alert('Network error connecting Google account.');
                if (btn) {
                    btn.innerHTML = `<span>Connect with Google</span>`;
                }
            });
        }
    }

    window.addEventListener('load', function() {
        initGoogleConnect();
    });
</script>
<script src="https://accounts.google.com/gsi/client" async defer></script>

@endsection
