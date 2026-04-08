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
</style>

<div class="dashboard-wrapper">
    <div class="container py-5">
        
        {{-- Profile Header Card --}}
        <div class="profile-header-card">
            <div class="profile-cover"></div>
            <div class="profile-avatar-wrapper position-relative">
                <img src="{{ Auth::user()->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=295A75&color=fff&size=200' }}" class="profile-avatar" alt="Profile">
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
                                <h6 class="q-title">{{ Str::limit($question->title ?: $question->body, 60) }}</h6>
                                <p class="text-muted small mb-4">{{ Str::limit(strip_tags($question->body), 85) }}</p>
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
                    <div class="col-lg-8">
                        <div class="form-section-card">
                            <h4 class="fw-bold mb-4">Profile Information</h4>
                            <form action="{{ route('user.profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', Auth::user()->name) }}" placeholder="Name">
                                            <label>Full Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" name="mobile" class="form-control rounded-3" value="{{ old('mobile', Auth::user()->mobile) }}" placeholder="Mobile">
                                            <label>Mobile Number</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select name="madhab" class="form-select rounded-3">
                                                <option value="Hanafi" {{ Auth::user()->madhab == 'Hanafi' ? 'selected' : '' }}>Hanafi</option>
                                                <option value="Shafii" {{ Auth::user()->madhab == 'Shafii' ? 'selected' : '' }}>Shafi'i</option>
                                                <option value="Maliki" {{ Auth::user()->madhab == 'Maliki' ? 'selected' : '' }}>Maliki</option>
                                                <option value="Hanbali" {{ Auth::user()->madhab == 'Hanbali' ? 'selected' : '' }}>Hanbali</option>
                                                <option value="Other" {{ Auth::user()->madhab == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            <label>Preferred Madhab</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" name="country" class="form-control rounded-3" value="{{ old('country', Auth::user()->country) }}" placeholder="Country">
                                            <label>Country of Residence</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" name="mother_language" class="form-control rounded-3" value="{{ old('mother_language', Auth::user()->mother_language) }}" placeholder="Language">
                                            <label>Mother Language</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end mt-4">
                                    <button class="btn btn-brand px-5 py-2 fw-bold">Save Profile Changes</button>
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
                            <h4 class="fw-bold mb-4">Account Security</h4>
                            <p class="text-muted small mb-4">Ensure your account is using a long, random password to stay secure.</p>
                            
                            <form action="{{ route('user.password.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-floating mb-3">
                                    <input type="password" name="current_password" class="form-control rounded-3" placeholder="Current">
                                    <label>Current Password</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" name="password" class="form-control rounded-3" placeholder="New">
                                    <label>New Password</label>
                                </div>
                                <div class="form-floating mb-4">
                                    <input type="password" name="password_confirmation" class="form-control rounded-3" placeholder="Confirm">
                                    <label>Confirm New Password</label>
                                </div>
                                <button class="btn btn-dark w-100 py-2 fw-bold">Update Security Credentials</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
