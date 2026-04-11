@extends('layouts.public')
@section('title', 'My Dashboard')
@section('content')

<style>
    .dashboard-wrapper { background: #f8fafc; min-height: 80vh; padding-bottom: 5rem; }
    
    /* Profile Header */
    .profile-header-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .profile-cover {
        height: 120px;
        background: linear-gradient(135deg, var(--brand-blue-dark) 0%, var(--brand-blue) 100%);
    }
    .profile-avatar-wrapper {
        margin-top: -60px;
        padding: 0 2rem;
        display: flex;
        align-items: flex-end;
        gap: 1.5rem;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 1.5rem;
        border: 5px solid white;
        box-shadow: var(--shadow-md);
        background: white;
        object-fit: cover;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 1.25rem;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid #eef2f7;
        transition: var(--transition-base);
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }
    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    /* Tabs Styling */
    .nav-pills-dashboard {
        display: flex;
        gap: 0.5rem;
        background: #edf2f7;
        padding: 0.4rem;
        border-radius: 1rem;
        margin-bottom: 2.5rem;
    }
    .nav-pills-dashboard .nav-link {
        border-radius: 0.75rem;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.2s;
        border: none;
    }
    .nav-pills-dashboard .nav-link.active {
        background: white;
        color: var(--brand-blue);
        box-shadow: var(--shadow-sm);
    }

    /* Question Grid/Box Style (Mirroring Home) */
    .q-card {
        background: white;
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-base);
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid #f1f5f9;
    }
    .q-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }
    .q-card-header {
        padding: 1.25rem;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .q-card-body { padding: 1.5rem; flex-grow: 1; }
    .q-badge { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; padding: 0.3rem 0.8rem; border-radius: 50px; }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-published { background: #dcfce7; color: #166534; }
    .status-answered { background: #e0f2fe; color: #075985; }
    .status-rejected { background: #fee2e2; color: #991b1b; }

    .q-title { font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; line-height: 1.5; }
    .q-meta { font-size: 0.8rem; color: #64748b; margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem; }
    .q-btn { 
        display: block; 
        text-align: center; 
        background: #f1f5f9; 
        color: #475569; 
        text-decoration: none; 
        padding: 0.6rem; 
        border-radius: 0.75rem; 
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .q-btn:hover { background: var(--brand-blue); color: white; }

    /* Forms Section */
    .form-section-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid #eef2f7;
    }
    .avatar-upload-placeholder {
        width: 100px;
        height: 100px;
        border-radius: 1.5rem;
        border: 2px dashed #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition-base);
        position: relative;
        overflow: hidden;
    }
    .avatar-upload-placeholder:hover { border-color: var(--brand-blue); background: #f8fafc; }
</style>

<div class="dashboard-wrapper">
    <div class="container py-5">
        
        {{-- Profile Header Card --}}
        <div class="profile-header-card">
            <div class="profile-cover"></div>
            <div class="profile-avatar-wrapper position-relative">
                <img src="{{ Auth::user()->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=295A75&color=fff&size=200' }}" class="profile-avatar border shadow-sm" alt="Profile" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=295A75&color=fff&size=200'">
                <div class="pb-3">
                    <h4 class="fw-bold mb-1">{{ Auth::user()->name }}</h4>
                    <p class="text-muted mb-0 small"><i class="bi bi-geo-alt me-1"></i>{{ Auth::user()->country ?: 'Planet Earth' }} • Joined {{ Auth::user()->created_at->format('M Y') }}</p>
                </div>
                <div class="ms-auto pb-4">
                    <a href="{{ route('question.create') }}" class="btn btn-gold px-4 rounded-pill">
                        <i class="bi bi-plus-lg me-2"></i>New Inquiry
                    </a>
                </div>
            </div>
        </div>

        {{-- Navigation Pills --}}
        <ul class="nav nav-pills nav-pills-dashboard justify-content-center mx-auto" id="dashboardTabs" role="tablist" style="max-width: 600px;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab"><i class="bi bi-grid-1x2 me-2"></i>Overview</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile" type="button" role="tab"><i class="bi bi-person-badge me-2"></i>My Profile</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="settings-tab" data-bs-toggle="pill" data-bs-target="#settings" type="button" role="tab"><i class="bi bi-shield-lock me-2"></i>Security</button>
            </li>
        </ul>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Tab Content --}}
        <div class="tab-content" id="dashboardTabsContent">
            
            {{-- Overview Tab --}}
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                {{-- Stats Row --}}
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-journal-text"></i></div>
                            <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                            <p class="text-muted small mb-0 fw-bold">Total Inquiries</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-patch-check"></i></div>
                            <h3 class="fw-bold mb-0">{{ $stats['answered'] }}</h3>
                            <p class="text-muted small mb-0 fw-bold">Answered / Published</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-history"></i></div>
                            <h3 class="fw-bold mb-0">{{ $stats['pending'] }}</h3>
                            <p class="text-muted small mb-0 fw-bold">Active Requests</p>
                        </div>
                    </div>
                </div>

                {{-- Inquiry Grid (Fatwa Box Style) --}}
                <h5 class="fw-bold mb-4">Recent My Inquiries</h5>
                <div class="row g-4">
                    @forelse($questions as $question)
                    <div class="col-md-6 col-xl-4">
                        <div class="q-card">
                            <div class="q-card-header">
                                <span class="small fw-bold text-muted text-uppercase tracking-wider">#{{ $question->question_id }}</span>
                                <span class="q-badge status-{{ $question->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $question->status)) }}
                                </span>
                            </div>
                            <div class="q-card-body">
                                <div class="q-meta">
                                    <span><i class="bi bi-folder2 me-1"></i>{{ $question->category->name ?? 'General' }}</span>
                                    <span><i class="bi bi-calendar3 me-1"></i>{{ $question->created_at->format('d M') }}</span>
                                </div>
                                <h6 class="q-title ml-text">{{ Str::limit(strip_tags($question->title ?: $question->body), 60) }}</h6>
                                <p class="text-muted small mb-4 ml-text">{{ Str::limit(strip_tags($question->body), 85) }}</p>
                                <a href="{{ route('user.question.show', $question->id) }}" class="q-btn">
                                    View Detailed Discussion <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 py-5 text-center bg-white rounded-5 border border-dashed">
                        <i class="bi bi-inbox text-muted opacity-25" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">You haven't submitted any inquires yet. Start by asking a question.</p>
                        <a href="{{ route('question.create') }}" class="btn btn-brand btn-sm mt-2 px-4 rounded-pill">Ask Now</a>
                    </div>
                    @endforelse
                </div>
                <div class="mt-5 d-flex justify-content-center">
                    {{ $questions->links() }}
                </div>
            </div>

            {{-- Profile Tab --}}
            <div class="tab-pane fade" id="profile" role="tabpanel">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="form-section-card">
                            <h4 class="fw-bold mb-4">Scholarly Profile Management</h4>
                            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                {{-- Avatar Upload --}}
                                <div class="mb-5 d-flex align-items-center gap-4">
                                    <div class="avatar-upload-placeholder" onclick="document.getElementById('avatarInput').click()">
                                        <img id="avatarPreview" src="{{ Auth::user()->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=f1f5f9&color=64748b' }}" style="width:100%; height:100%; object-fit:cover;">
                                        <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-25 text-white opacity-0 hover-opacity-100 transition-all">
                                            <i class="bi bi-camera-fill"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Profile Photo</h6>
                                        <p class="text-muted x-small mb-2">JPG, PNG or GIF. Max size of 2MB.</p>
                                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="document.getElementById('avatarInput').click()">Change Photo</button>
                                        <input type="file" id="avatarInput" name="avatar_file" class="d-none" accept="image/*" onchange="previewAvatar(this)">
                                    </div>
                                </div>

                                <h6 class="text-muted text-uppercase x-small fw-bold tracking-wider mb-3">Personal Details</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" name="name" class="form-control" value="{{ old('name', Auth::user()->name) }}" placeholder="Name">
                                            <label>Full Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', Auth::user()->mobile) }}" placeholder="Mobile">
                                            <label>Mobile Number</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select name="gender" class="form-select">
                                                <option value="Male" {{ Auth::user()->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ Auth::user()->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                            <label>Gender</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select name="madhab_id" class="form-select">
                                                <option value="">No Preference</option>
                                                @foreach($madhabs as $m)
                                                    <option value="{{ $m->id }}" {{ Auth::user()->madhab_id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                                @endforeach
                                            </select>
                                            <label>Madhab Preference</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" name="mother_language" class="form-control" value="{{ old('mother_language', Auth::user()->mother_language) }}" placeholder="Language">
                                            <label>Mother Language</label>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="text-muted text-uppercase x-small fw-bold tracking-wider mb-3">Address Information</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" name="address_line_1" class="form-control" value="{{ old('address_line_1', Auth::user()->address_line_1) }}" placeholder="Addr 1">
                                            <label>Address Line 1</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" name="address_line_2" class="form-control" value="{{ old('address_line_2', Auth::user()->address_line_2) }}" placeholder="Addr 2">
                                            <label>Address Line 2 (Optional)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" name="country" class="form-control" value="{{ old('country', Auth::user()->country) }}" placeholder="Country">
                                            <label>Country</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" name="state" class="form-control" value="{{ old('state', Auth::user()->state) }}" placeholder="State">
                                            <label>State</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" name="district" class="form-control" value="{{ old('district', Auth::user()->district) }}" placeholder="District">
                                            <label>District</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mt-5 pt-3 border-top">
                                    <button class="btn btn-brand btn-lg px-5 py-2 fw-bold rounded-pill shadow-sm">Save All Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Security Tab --}}
            <div class="tab-pane fade" id="settings" role="tabpanel">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="form-section-card">
                            <h4 class="fw-bold mb-4">Reset Authentication</h4>
                            <p class="text-muted small mb-4">Securely update your portal access credentials. A strong password helps protect your scholarly history.</p>
                            
                            <form action="{{ route('user.password.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-floating mb-3">
                                    <input type="password" name="password" class="form-control border-0 bg-light rounded-4" placeholder="New" required>
                                    <label>Define New Password</label>
                                </div>
                                <div class="form-floating mb-4">
                                    <input type="password" name="password_confirmation" class="form-control border-0 bg-light rounded-4" placeholder="Confirm" required>
                                    <label>Confirm Defined Password</label>
                                </div>
                                <button class="btn btn-dark w-100 py-3 fw-extrabold rounded-4 shadow-sm border-0 transition-all hover-translate-y">
                                    <i class="bi bi-shield-check me-2"></i>Apply New Credentials
                                </button>
                            </form>

                            <div class="mt-4 p-3 bg-warning bg-opacity-10 rounded-4 border border-warning border-opacity-25">
                                <p class="x-small text-warning-emphasis mb-0">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    Security Notice: Once updated, your previous password will be immediately invalidated across all devices.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection
