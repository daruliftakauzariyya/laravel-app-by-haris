<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Darul Ifta Portal') — Kauzariyya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app-fonts.css') }}">
    <style>
        :root {
            --green: #1a7a4a;
            --green-dark: #145e38;
            --gold: #c9a84c;
            --bg-light: #f8f9fa;
            --slate-blue: #4A6372;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg-light); }
        
        /* Language Specific Fonts (Local - see public/assets/css/app-fonts.css) */
        .inter-font              { font-family: 'Google Sans Eng', 'Inter', sans-serif; }
        .alyamama-font           { font-family: 'Noto Naskh Arabic', serif; }
        .gulzar-font             { font-family: 'Noto Nastaliq Urdu', serif; }
        .noto-serif-malayalam-font { font-family: 'Noto Serif Malayalam', serif; }
        
        /* Typography Utility Classes */
        .ar-text { font-family: 'Noto Naskh Arabic', serif; direction: rtl; text-align: right; }
        .ur-text { font-family: 'Noto Nastaliq Urdu', serif; direction: rtl; text-align: right; }
        .ml-text { font-family: 'Noto Serif Malayalam', serif; }

        .navbar { background: var(--green) !important; min-height: 45px; padding-top: 0 !important; padding-bottom: 0 !important; }
        .navbar .container { min-height: 45px; }
        .navbar-brand { font-size: 1.1rem; font-weight: 700; color: #fff !important; padding-top: 0; padding-bottom: 0; line-height: 45px; }
        .navbar-brand span { color: var(--gold); }
        .nav-link { color: rgba(255,255,255,.85) !important; font-weight: 500; padding-top: 0 !important; padding-bottom: 0 !important; line-height: 45px; font-size: 0.88rem; }
        .nav-link:hover, .nav-link.active { color: var(--gold) !important; }
        .navbar-nav { align-items: center; }
        .btn-primary { background: var(--green); border-color: var(--green); }
        .btn-primary:hover { background: var(--green-dark); border-color: var(--green-dark); }
        .btn-gold { background: var(--gold); border-color: var(--gold); color: #fff; }
        .text-green { color: var(--green); }
        .badge-status-pending    { background:#ffc107; color:#000; }
        .badge-status-assigned   { background:#0dcaf0; color:#000; }
        .badge-status-answered   { background:#0d6efd; color:#fff; }
        .badge-status-published  { background:#198754; color:#fff; }
        .badge-status-rejected   { background:#dc3545; color:#fff; }
        .arabic-text { font-family: "Alyamama", serif; font-size: 1.25rem; direction:rtl; text-align:right; }
        footer { background: var(--green); color: rgba(255,255,255,.8); padding: 2rem 0; }
        footer a { color: var(--gold); text-decoration:none; }

        @media print {
            .navbar, footer, .btn-print, .btn, .no-print { display: none !important; }
            .container { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
            body { background: #fff !important; }
            .card { border: none !important; shadow: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-book-half me-1"></i>Darul <span>Ifta</span>
        </a>

        {{-- Hijri Date Display (Public) --}}
        @if(\App\Models\SiteSetting::get('show_hijri_public', true))
            <div class="d-none d-md-flex ms-2 border-start ps-3 align-items-center">
                <span class="badge bg-green-dark border border-light-subtle rounded-pill font-Inter" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <i class="bi bi-moon-stars me-1 text-gold"></i>{{ \App\Helpers\HijriHelper::now('j F Y') }}
                </span>
            </div>
        @endif
        <button class="navbar-toggler border-light" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav mx-auto gap-1">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('fatwa.*') ? 'active' : '' }}" href="{{ route('fatwa.archive') }}">Fatwa Archive</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('question.*') ? 'active' : '' }}" href="{{ route('question.create') }}">Ask a Question</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-gold btn-sm">Register</a>
                @else
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" class="rounded-circle me-1" width="22" height="22">
                            @else
                                <i class="bi bi-person-circle me-1"></i>
                            @endif
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if(Auth::user()->hasRole('admin'))
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                            @endif
                            @if(Auth::user()->hasRole('mufti'))
                                <li><a class="dropdown-item" href="{{ route('mufti.dashboard') }}"><i class="bi bi-person-badge me-2"></i>Mufti Panel</a></li>
                            @endif
                            @if(Auth::user()->hasRole('user'))
                                <li><a class="dropdown-item" href="{{ route('user.dashboard') }}"><i class="bi bi-grid-fill me-2"></i>My Dashboard</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

{{-- Main content --}}
@yield('content')

{{-- Footer --}}
<footer class="mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5 class="fw-bold text-white"><i class="bi bi-book-half me-2"></i>Darul Ifta Kauzariyya</h5>
                <p class="small">A platform for authentic Islamic guidance based on Quran and Sunnah.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h6 class="text-white fw-bold">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('fatwa.archive') }}">Fatwa Archive</a></li>
                    <li><a href="{{ route('question.create') }}">Ask a Question</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h6 class="text-white fw-bold">Contact</h6>
                <p class="small"><i class="bi bi-envelope me-1"></i> darulifta.kauzariyya@gmail.com</p>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,.2)">
        <p class="text-center small mb-0">&copy; {{ date('Y') }} Darul Ifta Kauzariyya. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
