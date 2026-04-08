<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Darul Ifta Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app-fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/logo/fevi.png') }}">

    <style>
        :root { --sidebar-w: 240px; }
        body { font-family:'Inter',sans-serif; background:#f4f6f9; color: var(--text-main); }
        
        .sidebar { width:var(--sidebar-w); min-height:100vh; background:var(--brand-blue-dark); position:fixed; top:0; left:0; z-index:200; overflow-y:auto; border-right: 1px solid rgba(255,255,255,0.05); }
        .sidebar-brand { padding:1.25rem 1rem; background:rgba(0,0,0,.15); border-bottom: 5px solid var(--brand-gold); }
        .sidebar-brand h6 { color:#fff; font-weight:700; font-size:1rem; margin:0; }
        .sidebar-brand span { color:var(--brand-gold); }
        
        .sidebar-nav .nav-link { color:rgba(255,255,255,.7); padding:.7rem 1.25rem; display:flex; align-items:center; gap:.6rem; font-size:.88rem; border-left:4px solid transparent; transition: var(--transition-base); }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { color:#fff; background:rgba(255,255,255,.05); border-left-color:var(--brand-gold); }
        .sidebar-nav .nav-link i { font-size:1.1rem; width:22px; opacity: 0.8; }
        
        .sidebar-section { padding:1.2rem 1.25rem .3rem; font-size:.65rem; text-transform:uppercase; color:rgba(255,255,255,.45); letter-spacing:1.5px; font-weight: 700; }
        .main-content { margin-left:var(--sidebar-w); padding:0; }
        .topbar { background:#fff; border-bottom:1px solid #e5e7eb; padding:.75rem 1.5rem; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; box-shadow: var(--shadow-sm); }
        
        .page-content { padding:1.5rem; }
        .stat-card { border-radius:.75rem; padding:1.25rem 1.5rem; color:#fff; }
        .arabic-text { font-family: 'Noto Naskh Arabic', 'Amiri', serif; font-size: 1.2rem; direction: rtl; text-align: right; }
        
        .badge-status-pending    { background:#ffc107; color:#000; }
        .badge-status-accepted   { background:#6f42c1; color:#fff; }
        .badge-status-assigned   { background:#0dcaf0; color:#000; }
        .badge-status-answered   { background:#0d6efd; color:#fff; }
        .badge-status-published  { background:var(--brand-blue); color:#fff; }
        .badge-status-rejected   { background:#dc3545; color:#fff; }
        
        .admin-clock-box { background: #f8fafc; padding: 4px 12px; border-radius: 6px; border: 1px solid #e2e8f0; }

        @media print {
            .sidebar, .topbar, .btn, .no-print, .modal, .alert { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
            body { background: #fff !important; }
            .page-content { padding: 0 !important; }
            .card { border: none !important; box-shadow: none !important; margin-bottom: 2rem !important; }
            .card-header { background: #f8f9fa !important; color: #000 !important; border-bottom: 2px solid #eee !important; }
        }
    </style>
    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<div class="sidebar">
    <div class="sidebar-brand">
        <h6><i class="bi bi-book-half me-2"></i>Darul <span>Ifta</span></h6>
        <small style="color:rgba(255,255,255,.5); font-size:.75rem;">Admin Panel</small>
    </div>
    <nav class="sidebar-nav mt-2">
        <div class="sidebar-section">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <div class="sidebar-section">Questions</div>
        <a href="{{ route('admin.questions.index') }}" class="nav-link {{ request()->routeIs('admin.questions*') ? 'active' : '' }}">
            <i class="bi bi-question-circle"></i> All Questions
        </a>
        <a href="{{ route('admin.questions.index', ['status'=>'pending']) }}" class="nav-link">
            <i class="bi bi-clock-history"></i> Pending
        </a>
        <a href="{{ route('admin.questions.index', ['status'=>'assigned']) }}" class="nav-link">
            <i class="bi bi-person-check"></i> Assigned
        </a>
        <a href="{{ route('admin.questions.index', ['status'=>'answered']) }}" class="nav-link">
            <i class="bi bi-chat-check"></i> For Review
        </a>
        <div class="sidebar-section">Settings</div>
        <a href="{{ route('admin.muftis.index') }}" class="nav-link {{ request()->routeIs('admin.muftis*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i> Mufti Management
        </a>
        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Categories
        </a>
        <a href="{{ route('admin.questioners.index') }}" class="nav-link {{ request()->routeIs('admin.questioners*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Questioners
        </a>
        <div class="mt-3 px-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-light w-100"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
            </form>
        </div>
    </nav>
</div>

{{-- Main Content --}}
<div class="main-content">
    {{-- Topbar --}}
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <h6 class="mb-0 fw-bold text-muted">@yield('topbar-title', 'Dashboard')</h6>
            
            {{-- Digital Clock & Hijri --}}
            <div class="d-none d-md-flex align-items-center gap-2 ms-3 border-start ps-3">
                <div class="admin-clock-box">
                    <span id="admin-live-clock" class="fw-bold text-dark" style="font-family: 'Inter', monospace; font-size: 0.95rem;">00:00:00</span>
                    <span class="text-muted small ms-1" style="font-size: 0.7rem;">IST</span>
                </div>
                <div class="admin-hijri-box ms-2">
                    <span class="badge bg-light text-green border border-success-subtle fw-bold" style="font-size: 0.75rem;">
                        <i class="bi bi-moon-stars me-1"></i>{{ \App\Helpers\HijriHelper::now('j F Y') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <small class="text-muted">{{ Auth::user()->name }}</small>
            <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=1a7a4a&color=fff&size=40' }}"
                 class="rounded-circle" width="34" height="34">
        </div>
    </div>

    {{-- Flash --}}
    <div class="px-3 pt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show py-2">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <div class="page-content">
        {{-- Print Only Header --}}
        <div class="d-none d-print-block text-center mb-5 border-bottom pb-4">
            <h1 class="fw-bold" style="color:#1a7a4a;">Darul Ifta Kauzariyya</h1>
            <p class="mb-1">Authentic Islamic Guidance - Administrative Record</p>
            <p class="small text-muted mb-0">Confidential Fatwa Documentation</p>
        </div>

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
    // Digital Clock Engine
    function updateAdminClock() {
        const clockElement = document.getElementById('admin-live-clock');
        if (!clockElement) return;
        
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour12: true, 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
        clockElement.innerHTML = timeString;
    }
    setInterval(updateAdminClock, 1000);
    updateAdminClock();
</script>
@stack('scripts')
</body>
</html>
