<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-SZG3N3VKYE"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-SZG3N3VKYE');
    </script>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-NBPW3VHH');</script>
    <!-- End Google Tag Manager -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "w8gk6uy65v");
    </script>
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
        
        .sidebar { 
            width: var(--sidebar-w); 
            min-height: 100vh; 
            background: var(--brand-blue-dark); 
            position: fixed; 
            top: 0; 
            left: 0; 
            z-index: 1060; 
            overflow-y: auto; 
            border-right: 1px solid rgba(255,255,255,0.05); 
            transition: transform 0.3s ease;
        }
        .sidebar-brand { padding:1.25rem 1rem; background:rgba(0,0,0,.15); border-bottom: 5px solid var(--brand-gold); }
        .sidebar-brand h6 { color:#fff; font-weight:700; font-size:1rem; margin:0; }
        .sidebar-brand span { color:var(--brand-gold); }
        
        .sidebar-nav .nav-link { color:rgba(255,255,255,.7); padding:.7rem 1.25rem; display:flex; align-items:center; gap:.6rem; font-size:.88rem; border-left:4px solid transparent; transition: var(--transition-base); }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { color:#fff; background:rgba(255,255,255,.05); border-left-color:var(--brand-gold); }
        .sidebar-nav .nav-link i { font-size:1.1rem; width:22px; opacity: 0.8; }
        
        .sidebar-section { padding:1.2rem 1.25rem .3rem; font-size:.65rem; text-transform:uppercase; color:rgba(255,255,255,.45); letter-spacing:1.5px; font-weight: 700; }
        .main-content { margin-left: var(--sidebar-w); padding: 0; transition: margin-left 0.3s ease; min-height: 100vh; }
        .topbar { background:#fff; border-bottom:1px solid #e5e7eb; padding:.75rem 1.5rem; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; box-shadow: var(--shadow-sm); }
        
        /* Sidebar Responsive Logic */
        @media (max-width: 991px) {
            :root { --sidebar-w: 260px; }
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
            body.sidebar-open .sidebar { transform: translateX(0); }
            body.sidebar-open { overflow: hidden; }
            .sidebar-overlay { 
                position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                background: rgba(0,0,0,0.5); z-index: 1050; display: none; 
            }
            body.sidebar-open .sidebar-overlay { display: block; }
            .topbar { padding: 0.75rem 1rem; }
        }
        
        .page-content { padding:1.5rem; }
        .stat-card { border-radius:.75rem; padding:1.25rem 1.5rem; color:#fff; }
        .arabic-text { font-family: 'Noto Naskh Arabic', 'Amiri', serif; font-size: 1.2rem; direction: rtl; text-align: right; }
        
        .badge-status-pending    { background:#ffc107; color:#000; }
        .badge-status-accepted   { background:#6f42c1; color:#fff; }
        .badge-status-assigned   { background:#0dcaf0; color:#000; }
        .badge-status-answered   { background:#0d6efd; color:#fff; }
        .badge-status-published  { background:var(--brand-blue); color:#fff; }
        .badge-status-rejected   { background:#dc3545; color:#fff; }
        
        .admin-header-box { 
            background: #f8fafc; 
            padding: 5px 15px; 
            border-radius: 8px; 
            border: 1px solid #eef2f7; 
            display: flex; 
            align-items: center;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
        }

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
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NBPW3VHH"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

{{-- Sidebar --}}
<div class="sidebar">
    <div class="sidebar-brand text-center">
        <a href="{{ route('home') }}">
            <img src="{{ asset('assets/logo/logoblueNew.svg') }}" class="mb-2" style="max-height: 45px; filter: brightness(0) invert(1);">
        </a>
        <h6 class="mb-0 text-white opacity-75 small">Admin Console</h6>
    </div>
    <nav class="sidebar-nav mt-3">
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
        <div class="sidebar-section">System Control</div>
        <a href="{{ route('admin.visitor.insights') }}" class="nav-link {{ request()->routeIs('admin.visitor.insights') ? 'active' : '' }}">
            <i class="bi bi-globe-americas"></i> Visitor Insights
        </a>
        <a href="{{ route('admin.settings.system') }}" class="nav-link {{ request()->routeIs('admin.settings.system') ? 'active' : '' }}">
            <i class="bi bi-gear-wide-connected"></i> System Settings
        </a>
        <a href="{{ route('admin.mail-management.index') }}" class="nav-link {{ request()->routeIs('admin.mail-management*') ? 'active' : '' }}">
            <i class="bi bi-envelope-check"></i> Mailing Management
        </a>
        <div class="mt-4 px-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-light w-100 py-2 border-opacity-25 hover-gold"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
            </form>
        </div>
    </nav>
</div>

{{-- Sidebar Overlay for Mobile --}}
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

{{-- Main Content --}}
<div class="main-content">
    {{-- Topbar --}}
    <div class="topbar">
        <div class="d-flex align-items-center gap-2">
            {{-- Mobile Toggle --}}
            <button class="btn btn-brand d-lg-none" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <h6 class="mb-0 fw-bold d-none d-lg-block" style="color: var(--brand-blue);">@yield('topbar-title', 'Dashboard')</h6>
            
            {{-- Digital Clock & Enhanced Date Display --}}
            <div class="d-none d-md-flex align-items-center gap-2 ms-lg-3 border-start ps-lg-3">
                {{-- Clock Box --}}
                <div class="admin-header-box">
                    <i class="bi bi-clock text-brand-dark me-2 small"></i>
                    <span id="admin-live-clock" class="fw-bold text-dark" style="font-family: 'Inter', monospace; font-size: 0.9rem;">00:00:00</span>
                    <span class="text-muted small ms-1" style="font-size: 0.7rem;">IST</span>
                </div>

                {{-- Date Box (Gregorian + Hijri) --}}
                <div class="admin-header-box px-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar3 text-brand-dark me-2 small"></i>
                            <span class="fw-semibold text-dark small">{{ date('d M, Y') }}</span>
                        </div>
                        <div class="vr mx-1" style="height: 15px; opacity: 0.2;"></div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-moon-stars text-gold me-2 small"></i>
                            <span class="fw-semibold text-brand small">{{ \App\Helpers\HijriHelper::now('j F Y') }}</span>
                        </div>
                    </div>
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
    // Sidebar Toggle for Mobile
    function toggleSidebar() {
        document.body.classList.toggle('sidebar-open');
    }

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
