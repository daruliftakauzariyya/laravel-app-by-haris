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
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/logo/fevi.png') }}">

    <style>
        body { font-family: 'Inter', sans-serif; background: var(--bg-light); color: var(--text-main); }
        
        /* Typography Utility Classes */
        .ar-text { font-family: 'Noto Naskh Arabic', serif; direction: rtl; text-align: right; }
        .ur-text { font-family: 'Noto Nastaliq Urdu', serif; direction: rtl; text-align: right; }
        .ml-text { font-family: 'Noto Serif Malayalam', serif; }

        /* Premium Header Styling */
        .navbar { 
            background: rgba(255, 255, 255, 0.95) !important; 
            backdrop-filter: blur(10px);
            min-height: var(--header-height); 
            height: var(--header-height);
            padding: 0 !important; 
            border-bottom: 2px solid var(--brand-blue);
            box-shadow: var(--shadow-md);
            z-index: 1050;
        }
        .navbar-sticky { position: sticky; top: 0; }
        
        .navbar-brand img { height: 100%; max-height: 65px; width: auto; transition: var(--transition-base); }
        .navbar-brand:hover img { transform: scale(1.02); }
        
        .nav-link { 
            color: var(--brand-blue) !important; 
            font-weight: 600; 
            padding: 0.5rem 1rem !important;
            font-size: 0.95rem; 
            transition: var(--transition-base);
            border-radius: 6px;
        }
        .nav-link:hover, .nav-link.active { 
            color: var(--brand-gold) !important; 
            background: rgba(41, 90, 117, 0.05);
        }

        .btn-gold { background: var(--brand-gold); border-color: var(--brand-gold); color: #fff; font-weight: 600; }
        .btn-gold:hover { background: var(--brand-gold-dark); border-color: var(--brand-gold-dark); color: #fff; }
        
        .text-brand { color: var(--brand-blue); }
        .text-gold { color: var(--brand-gold); }

        /* Footer Overhaul */
        footer { 
            background: var(--brand-blue-dark); 
            color: rgba(255,255,255,.8); 
            padding: 4rem 0 2rem; 
            border-top: 5px solid var(--brand-gold);
            font-size: 0.9rem;
        }
        footer h5, footer h6 { color: #fff; margin-bottom: 1.5rem; letter-spacing: 0.5px; }
        footer a { color: rgba(255,255,255,.7); text-decoration:none; transition: var(--transition-base); }
        footer a:hover { color: var(--brand-gold); padding-left: 5px; }
        .footer-logo { max-height: 50px; filter: brightness(0) invert(1); margin-bottom: 1rem; }
        .footer-social a { font-size: 1.4rem; color: #fff; margin-right: 15px; opacity: 0.8; }
        .footer-social a:hover { opacity: 1; color: var(--brand-gold); }

        @media screen and (max-width: 991px) {
            .navbar { height: auto; min-height: 70px; }
            .navbar-brand img { max-height: 50px; }
        }

        @media print {
            .navbar, footer, .btn-print, .btn, .no-print { display: none !important; }
            .container { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
            body { background: #fff !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg navbar-light navbar-sticky shadow-sm">
    <div class="container h-100">
        <a class="navbar-brand h-100 d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('assets/logo/logoblueNew.svg') }}" alt="Darul Ifta Kauzariyya Logo">
        </a>

        {{-- Hijri Date Display (Public) --}}
        @if(\App\Models\SiteSetting::get('show_hijri_public', true))
            <div class="d-none d-md-flex ms-3 border-start ps-3 align-items-center h-100">
                <span class="badge bg-light text-brand border border-info-subtle rounded-pill font-Inter" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                    <i class="bi bi-moon-stars me-1 text-gold"></i>{{ \App\Helpers\HijriHelper::now('j F Y') }}
                </span>
            </div>
        @endif

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse h-100" id="navMain">
            <ul class="navbar-nav mx-auto gap-3">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('fatwa.*') ? 'active' : '' }}" href="{{ route('fatwa.archive') }}">Fatwa Archive</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('question.*') ? 'active' : '' }}" href="{{ route('question.create') }}">Ask a Question</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-brand btn-sm fw-bold">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-gold btn-sm">Register Free</a>
                @else
                    <div class="dropdown">
                        <button class="btn btn-outline-brand btn-sm dropdown-toggle fw-bold" data-bs-toggle="dropdown">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" class="rounded-circle me-1" width="24" height="24">
                            @else
                                <i class="bi bi-person-circle me-1 text-brand"></i>
                            @endif
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            @if(Auth::user()->hasRole('admin'))
                                <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                            @endif
                            @if(Auth::user()->hasRole('mufti'))
                                <li><a class="dropdown-item py-2" href="{{ route('mufti.dashboard') }}"><i class="bi bi-person-badge me-2"></i>Mufti Panel</a></li>
                            @endif
                            @if(Auth::user()->hasRole('user'))
                                <li><a class="dropdown-item py-2" href="{{ route('user.dashboard') }}"><i class="bi bi-grid-fill me-2"></i>My Dashboard</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger py-2"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
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
        <div class="row g-4">
            {{-- Column 1: Brand & Mission --}}
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <img src="{{ asset('assets/logo/logoblueNew.svg') }}" class="footer-logo mb-3" alt="Darul Ifta" style="filter: brightness(0) invert(1); max-height: 40px;">
                <p class="small text-white-50 pe-lg-4">
                    Darul Ifta Kauzariyya is dedicated to providing authentic Islamic guidance based on the Quran and Sunnah. Our mission is to illuminate the path of knowledge for the global community.
                </p>
                <div class="footer-social mt-4">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-twitter-x"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            {{-- Column 2: Our Services --}}
            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                <h6 class="text-white fw-bold mb-3">Our Services</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('question.create') }}"><i class="bi bi-chevron-right me-2 small"></i>Ask a Fatwa</a></li>
                    <li class="mb-2"><a href="{{ route('fatwa.archive') }}"><i class="bi bi-chevron-right me-2 small"></i>Browse Archive</a></li>
                    <li class="mb-2"><a href="#"><i class="bi bi-chevron-right me-2 small"></i>Search Madrasas</a></li>
                    <li class="mb-2"><a href="#"><i class="bi bi-chevron-right me-2 small"></i>Scholar Directory</a></li>
                </ul>
            </div>

            {{-- Column 3: Quick Access --}}
            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                <h6 class="text-white fw-bold mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('home') }}"><i class="bi bi-chevron-right me-2 small"></i>Home</a></li>
                    <li class="mb-2"><a href="{{ route('contact') }}"><i class="bi bi-chevron-right me-2 small"></i>Contact Us</a></li>
                    <li class="mb-2"><a href="#"><i class="bi bi-chevron-right me-2 small"></i>Privacy Policy</a></li>
                    <li class="mb-2"><a href="#"><i class="bi bi-chevron-right me-2 small"></i>Terms of Use</a></li>
                </ul>
            </div>

            {{-- Column 4: Contact & Info --}}
            <div class="col-lg-4 col-md-6">
                <h6 class="text-white fw-bold mb-4">Contact Information</h6>
                <div class="d-flex align-items-start mb-3">
                    <i class="bi bi-geo-alt text-gold me-3 mt-1"></i>
                    <p class="small mb-0 text-white-50">Darul Ifta Kauzariyya Complex, <br>South India, Kerala.</p>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-envelope text-gold me-3"></i>
                    <p class="small mb-0"><a href="mailto:darulifta.kauzariyya@gmail.com">darulifta.kauzariyya@gmail.com</a></p>
                </div>
                <div class="d-flex align-items-center">
                    <i class="bi bi-headset text-gold me-3"></i>
                    <p class="small mb-0"><a href="tel:+910000000000">+91 000 000 0000</a></p>
                </div>
            </div>
        </div>
        
        <hr class="my-4" style="border-color:rgba(255,255,255,.1)">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <p class="text-white-50 small mb-0">&copy; {{ date('Y') }} Darul Ifta Kauzariyya. Designed for Enlightenment.</p>
            <div class="small mt-2 mt-md-0">
                <span class="text-white-50">Powered by</span> <span class="text-gold fw-bold">Advanced Fatwa Engine</span>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
