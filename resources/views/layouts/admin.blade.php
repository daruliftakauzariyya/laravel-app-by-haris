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
    <style>
        :root { --green:#1a7a4a; --green-dark:#145e38; --gold:#c9a84c; --sidebar-w:240px; }
        body { font-family:'Inter',sans-serif; background:#f4f6f9; }
        .sidebar { width:var(--sidebar-w); min-height:100vh; background:var(--green-dark); position:fixed; top:0; left:0; z-index:200; overflow-y:auto; }
        .sidebar-brand { padding:1.25rem 1rem; background:rgba(0,0,0,.2); }
        .sidebar-brand h6 { color:#fff; font-weight:700; font-size:1rem; margin:0; }
        .sidebar-brand span { color:var(--gold); }
        .sidebar-nav .nav-link { color:rgba(255,255,255,.75); padding:.65rem 1rem; display:flex; align-items:center; gap:.6rem; font-size:.9rem; border-left:3px solid transparent; transition:.2s; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { color:#fff; background:rgba(255,255,255,.08); border-left-color:var(--gold); }
        .sidebar-nav .nav-link i { font-size:1.05rem; width:20px; }
        .sidebar-section { padding:.5rem 1rem .2rem; font-size:.7rem; text-transform:uppercase; color:rgba(255,255,255,.4); letter-spacing:1px; }
        .main-content { margin-left:var(--sidebar-w); padding:0; }
        .topbar { background:#fff; border-bottom:1px solid #e5e7eb; padding:.75rem 1.5rem; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; }
        .page-content { padding:1.5rem; }
        .stat-card { border-radius:.75rem; padding:1.25rem 1.5rem; color:#fff; }
        .arabic-text { font-family:'Amiri',serif; font-size:1.2rem; direction:rtl; text-align:right; }
        .badge-status-pending    { background:#ffc107; color:#000; }
        .badge-status-accepted   { background:#6f42c1; color:#fff; }
        .badge-status-assigned   { background:#0dcaf0; color:#000; }
        .badge-status-answered   { background:#0d6efd; color:#fff; }
        .badge-status-published  { background:#198754; color:#fff; }
        .badge-status-rejected   { background:#dc3545; color:#fff; }

        @media print {
            .sidebar, .topbar, .btn, .no-print, .modal, .alert { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
            body { background: #fff !important; }
            .page-content { padding: 0 !important; }
            .card { border: none !important; box-shadow: none !important; margin-bottom: 2rem !important; }
            .card-header { background: #f8f9fa !important; color: #000 !important; border-bottom: 2px solid #eee !important; }
        }
    </style>
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
        <h6 class="mb-0 fw-bold text-muted">@yield('topbar-title', 'Dashboard')</h6>
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
@stack('scripts')
</body>
</html>
