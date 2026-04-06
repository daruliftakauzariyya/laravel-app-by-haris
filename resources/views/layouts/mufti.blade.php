<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mufti Panel') — Darul Ifta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root { --green:#1a7a4a; --green-dark:#145e38; --gold:#c9a84c; --sidebar-w:220px; }
        body { font-family:'Inter',sans-serif; background:#f4f6f9; }
        .sidebar { width:var(--sidebar-w); min-height:100vh; background:var(--green-dark); position:fixed; top:0; left:0; z-index:200; }
        .sidebar-brand { padding:1.25rem 1rem; background:rgba(0,0,0,.2); }
        .sidebar-brand h6 { color:#fff; font-weight:700; margin:0; }
        .sidebar-brand small { color:rgba(255,255,255,.5); font-size:.75rem; }
        .sidebar-nav .nav-link { color:rgba(255,255,255,.75); padding:.65rem 1rem; display:flex; align-items:center; gap:.6rem; font-size:.9rem; border-left:3px solid transparent; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { color:#fff; background:rgba(255,255,255,.08); border-left-color:var(--gold); }
        .main-content { margin-left:var(--sidebar-w); }
        .topbar { background:#fff; border-bottom:1px solid #e5e7eb; padding:.75rem 1.5rem; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; }
        .page-content { padding:1.5rem; }
        .arabic-text { font-family:'Amiri',serif; font-size:1.2rem; direction:rtl; text-align:right; }
    </style>
    @stack('styles')
</head>
<body>
<div class="sidebar">
    <div class="sidebar-brand">
        <h6><i class="bi bi-person-badge me-2"></i>Mufti Panel</h6>
        <small>{{ Auth::user()->name }}</small>
    </div>
    <nav class="sidebar-nav mt-2">
        <a href="{{ route('mufti.dashboard') }}" class="nav-link {{ request()->routeIs('mufti.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('mufti.questions') }}" class="nav-link {{ request()->routeIs('mufti.questions*') ? 'active' : '' }}">
            <i class="bi bi-question-circle"></i> My Questions
        </a>
        <a href="{{ route('mufti.profile') }}" class="nav-link {{ request()->routeIs('mufti.profile') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i> Profile
        </a>
        <a href="{{ route('home') }}" class="nav-link"><i class="bi bi-globe"></i> Public Site</a>
        <div class="px-2 mt-3">
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="btn btn-sm btn-outline-light w-100"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
            </form>
        </div>
    </nav>
</div>
<div class="main-content">
    <div class="topbar">
        <h6 class="mb-0 fw-bold text-muted">@yield('topbar-title', 'Mufti Panel')</h6>
    </div>
    <div class="px-3 pt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>
    <div class="page-content">@yield('content')</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
