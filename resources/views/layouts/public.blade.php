<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-SZG3N3VKYE"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-SZG3N3VKYE');
    </script>
    <!-- Google Tag Manager -->
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || []; w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            }); var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-NBPW3VHH');</script>
    <!-- End Google Tag Manager -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="text/javascript">
        (function (c, l, a, r, i, t, y) {
            c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments) };
            t = l.createElement(r); t.async = 1; t.src = "https://www.clarity.ms/tag/" + i;
            y = l.getElementsByTagName(r)[0]; y.parentNode.insertBefore(t, y);
        })(window, document, "clarity", "script", "w8gk6uy65v");
    </script>
    <title>@yield('title', 'Darul Ifta Portal') — Kauzariyya</title>
    
    {{-- High-Priority Preloads --}}
    @if(request()->routeIs('home'))
        <link rel="preload" as="image" href="{{ asset('assets/img/slider/center.png') }}" fetchpriority="high">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Amiri:wght@400;700&display=swap"
        rel="stylesheet">
    
    {{-- Inline Critical CSS (Variables & Fonts) --}}
    <style>
        :root {
            --brand-blue: #295A75;
            --brand-blue-dark: #1D4054;
            --brand-red: #701F18;
            --brand-gold: #c9a84c;
            --brand-gold-dark: #a8883a;
            --primary-color: var(--brand-blue);
            --secondary-color: var(--brand-gold);
            --accent-color: var(--brand-red);
            --bg-light: #F8F4F2;
            --bg-white: #ffffff;
            --surface-light: #ffffff;
            --text-main: #1a1a1a;
            --text-muted: #6b7280;
            --text-on-brand: #ffffff;
            --header-height: 90px;
            --border-radius-sm: 4px;
            --border-radius-md: 8px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.08);
            --transition-base: all 0.3s ease;
        }

        /* Font Registration */
        @font-face {
            font-family: 'Google Sans Eng';
            src: url('{{ asset("assets/font/Google_Sans_eng/GoogleSansFlex-VariableFont_GRAD,ROND,opsz,slnt,wdth,wght.ttf") }}') format('truetype');
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Noto Naskh Arabic';
            src: url('{{ asset("assets/font/Noto_Naskh_Arabic/NotoNaskhArabic-VariableFont_wght.ttf") }}') format('truetype');
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Noto Nastaliq Urdu';
            src: url('{{ asset("assets/font/Noto_Nastaliq_Urdu/NotoNastaliqUrdu-VariableFont_wght.ttf") }}') format('truetype');
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Noto Serif Malayalam';
            src: url('{{ asset("assets/font/Noto_Serif_Malayalam/NotoSerifMalayalam-VariableFont_wght.ttf") }}') format('truetype');
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }

        /* Typography Utilities */
        .font-eng       { font-family: 'Google Sans Eng', 'Inter', sans-serif !important; }
        .font-arabic    { font-family: 'Noto Naskh Arabic', serif !important; direction: rtl; text-align: right; }
        .font-urdu      { font-family: 'Noto Nastaliq Urdu', serif !important; direction: rtl; text-align: right; }
        .font-malayalam { font-family: 'Noto Serif Malayalam', serif !important; }
        .ar-text        { font-family: 'Noto Naskh Arabic', serif !important; direction: rtl; text-align: right; }
        .ur-text        { font-family: 'Noto Nastaliq Urdu', serif !important; direction: rtl; text-align: right; line-height: 2.2; }
        .ml-text        { font-family: 'Noto Serif Malayalam', serif !important; }
        .en-text        { font-family: 'Google Sans Eng', 'Inter', sans-serif !important; }
        .inter-font     { font-family: 'Google Sans Eng', 'Inter', sans-serif !important; }
        .noto-serif-malayalam-font { font-family: 'Noto Serif Malayalam', serif !important; }
        .font-urdu p, .ur-text p  { line-height: 2.4 !important; }
        .font-arabic p, .ar-text p { line-height: 2 !important; }
    </style>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/logo/fevi.png') }}">

    {{-- PWA Meta --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#073a69ff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('assets/logo/fevi.png') }}">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-light);
            color: var(--text-main);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1 0 auto;
        }

        /* Typography Utility Classes */
        .ar-text {
            font-family: 'Noto Naskh Arabic', serif;
            direction: rtl;
            text-align: right;
        }

        .ur-text {
            font-family: 'Noto Nastaliq Urdu', serif;
            direction: rtl;
            text-align: right;
        }

        .ml-text {
            font-family: 'Noto Serif Malayalam', serif;
        }

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

        .navbar-sticky {
            position: sticky;
            top: 0;
        }

        .navbar-brand img {
            height: 100%;
            max-height: 65px;
            width: auto;
            transition: var(--transition-base);
        }

        .navbar-brand:hover img {
            transform: scale(1.02);
        }

        .nav-link {
            color: var(--brand-blue) !important;
            font-weight: 600;
            padding: 0.5rem 1rem !important;
            font-size: 0.95rem;
            transition: var(--transition-base);
            border-radius: 6px;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--brand-gold) !important;
            background: rgba(41, 90, 117, 0.05);
        }

        .btn-gold {
            background: var(--brand-gold);
            border-color: var(--brand-gold);
            color: #fff;
            font-weight: 600;
        }

        .btn-gold:hover {
            background: var(--brand-gold-dark);
            border-color: var(--brand-gold-dark);
            color: #fff;
        }

        .text-brand {
            color: var(--brand-blue);
        }

        .text-gold {
            color: var(--brand-gold);
        }

        /* Footer Overhaul */
        footer {
            background: var(--brand-blue-dark);
            color: rgba(255, 255, 255, .8);
            padding: 4rem 0 2rem;
            border-top: 5px solid var(--brand-gold);
            font-size: 0.9rem;
        }

        footer h5,
        footer h6 {
            color: #fff;
            margin-bottom: 1.5rem;
            letter-spacing: 0.5px;
        }

        footer a {
            color: rgba(255, 255, 255, .7);
            text-decoration: none;
            transition: var(--transition-base);
        }

        footer a:hover {
            color: var(--brand-gold);
            padding-left: 5px;
        }

        .footer-logo {
            max-height: 50px;
            filter: brightness(0) invert(1);
            margin-bottom: 1rem;
        }

        .footer-social a {
            font-size: 1.4rem;
            color: #fff;
            margin-right: 15px;
            opacity: 0.8;
        }

        .footer-social a:hover {
            opacity: 1;
            color: var(--brand-gold);
        }

            {
                {
                -- Mobile Logo Fix & Styling --
            }
        }

        @media screen and (max-width: 991px) {
            .navbar {
                height: 80px !important;
                min-height: 80px;
                padding: 0 10px !important;
            }

            .navbar-brand img {
                max-height: 55px !important;
                opacity: 1 !important;
                visibility: visible !important;
                display: block !important;
            }

            .navbar-brand {
                display: flex !important;
                align-items: center !important;
            }
        }

        /* Offcanvas Sidebar Styling (L-to-R) */
        .offcanvas {
            width: 75% !important;
            border-right: 5px solid var(--brand-gold) !important;
            border-left: none !important;
        }

        .offcanvas-header {
            background: var(--brand-blue-dark);
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .offcanvas-body {
            padding: 0;
            background: var(--bg-light);
        }

        .mobile-nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .mobile-nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: var(--brand-blue);
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1px solid #eef2f7;
            transition: var(--transition-base);
        }

        .mobile-nav-link:hover,
        .mobile-nav-link.active {
            background: #fff;
            color: var(--brand-gold);
            padding-left: 2rem;
        }

        .mobile-nav-link i {
            font-size: 1.2rem;
            width: 30px;
        }

        .mobile-auth-section {
            padding: 2rem 1.5rem;
            background: #fff;
            margin-top: auto;
            border-top: 1px solid #eef2f7;
        }

        @media print {

            .navbar,
            footer,
            .btn-print,
            .btn,
            .no-print {
                display: none !important;
            }

            .container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            body {
                background: #fff !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NBPW3VHH" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light navbar-sticky shadow-sm">
        <div class="container h-100">
            <a class="navbar-brand h-100 d-none d-lg-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('assets/logo/logoblueNew.svg') }}" alt="Darul Ifta Kauzariyya Logo">
            </a>

            {{-- Mobile Hamburger --}}
            <button class="navbar-toggler border-0 shadow-none d-lg-none" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#mobileMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Desktop Navigation --}}
            <div class="collapse navbar-collapse h-100" id="navMain">
                <ul class="navbar-nav mx-auto gap-3">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                            href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('fatwa.*') ? 'active' : '' }}"
                            href="{{ route('fatwa.archive') }}">Fatwa Archive</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('question.*') ? 'active' : '' }}"
                            href="{{ route('question.create') }}">Ask a Question</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('team') ? 'active' : '' }}"
                            href="{{ route('team') }}">Meet the Muftis</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}"
                            href="{{ route('contact') }}">Contact</a></li>
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
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i
                                                class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                                @endif
                                @if(Auth::user()->hasRole('mufti'))
                                    <li><a class="dropdown-item py-2" href="{{ route('mufti.dashboard') }}"><i
                                                class="bi bi-person-badge me-2"></i>Mufti Panel</a></li>
                                @endif
                                @if(Auth::user()->hasRole('user'))
                                    <li><a class="dropdown-item py-2" href="{{ route('user.dashboard') }}"><i
                                                class="bi bi-grid-fill me-2"></i>My Dashboard</a></li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger py-2"><i
                                                class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    {{-- Mobile Offcanvas Menu (L-to-R) --}}
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold" id="mobileMenuLabel">
                <i class="bi bi-grid-3x3-gap me-2"></i>Scholar Menu
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column h-100">
            <div class="mobile-nav-list">
                <a href="{{ route('home') }}" class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i> Home
                </a>
                <a href="{{ route('fatwa.archive') }}"
                    class="mobile-nav-link {{ request()->routeIs('fatwa.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark"></i> Fatwa Archive
                </a>
                <a href="{{ route('question.create') }}"
                    class="mobile-nav-link {{ request()->routeIs('question.*') ? 'active' : '' }}">
                    <i class="bi bi-pencil-square"></i> Ask a Question
                </a>
                <a href="{{ route('team') }}" class="mobile-nav-link {{ request()->routeIs('team') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Meet the Muftis
                </a>
                <a href="{{ route('contact') }}"
                    class="mobile-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                    <i class="bi bi-envelope"></i> Contact Us
                </a>

            </div>

            <div class="mobile-auth-section mt-auto">
                @guest
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-brand fw-bold">Login to Account</a>
                        <a href="{{ route('register') }}" class="btn btn-gold py-2">Create Free Profile</a>
                    </div>
                @else
                    <div class="d-flex align-items-center gap-3 mb-4 p-2 bg-light rounded-3">
                        <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=1a7a4a&color=fff' }}"
                            class="rounded-circle" width="45" height="45">
                        <div>
                            <div class="fw-bold">{{ Auth::user()->name }}</div>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Scholar
                                Panel Access</small>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        @if(Auth::user()->hasRole('admin'))
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-brand btn-sm text-start"><i
                                    class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a>
                        @endif
                        @if(Auth::user()->hasRole('mufti'))
                            <a href="{{ route('mufti.dashboard') }}" class="btn btn-brand btn-sm text-start"><i
                                    class="bi bi-person-badge me-2"></i>Scholars Portal</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-danger btn-sm text-start w-100 mt-2"><i
                                    class="bi bi-box-arrow-right me-2"></i>Sign Out</button>
                        </form>
                    </div>
                @endguest
            </div>
        </div>
    </div>

    @if(session('success') || session('error'))
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
    @endif

    {{-- Main content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-5">
        <div class="container">
            <div class="row g-4">
                {{-- Column 1: Brand & Mission --}}
                <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                    <img src="{{ asset('assets/logo/logoblueNew.svg') }}" class="footer-logo mb-3" alt="Darul Ifta"
                        style="filter: brightness(0) invert(1); max-height: 40px;">
                    <p class="small text-white-50 pe-lg-4">
                        Darul Ifta Kauzariyya is dedicated to providing authentic Islamic guidance based on the Quran
                        and Sunnah. Our mission is to illuminate the path of knowledge for the global community.
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
                        <li class="mb-2"><a href="{{ route('question.create') }}"><i
                                    class="bi bi-chevron-right me-2 small"></i>Ask a Fatwa</a></li>
                        <li class="mb-2"><a href="{{ route('fatwa.archive') }}"><i
                                    class="bi bi-chevron-right me-2 small"></i>Browse Archive</a></li>
                        <li class="mb-2"><a href="#"><i class="bi bi-chevron-right me-2 small"></i>Search Madrasas</a>
                        </li>
                        <li class="mb-2"><a href="#"><i class="bi bi-chevron-right me-2 small"></i>Scholar Directory</a>
                        </li>
                    </ul>
                </div>

                {{-- Column 3: Quick Access --}}
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h6 class="text-white fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('home') }}"><i
                                    class="bi bi-chevron-right me-2 small"></i>Home</a></li>
                        <li class="mb-2"><a href="{{ route('contact') }}"><i
                                    class="bi bi-chevron-right me-2 small"></i>Contact Us</a></li>
                        <li class="mb-2"><a href="#"><i class="bi bi-chevron-right me-2 small"></i>Privacy Policy</a>
                        </li>
                        <li class="mb-2"><a href="#"><i class="bi bi-chevron-right me-2 small"></i>Terms of Use</a></li>
                    </ul>
                </div>

                {{-- Column 4: Contact & Info --}}
                <div class="col-lg-4 col-md-6">
                    <h6 class="text-white fw-bold mb-4">Contact Information</h6>
                    <div class="d-flex align-items-start mb-3">
                        <i class="bi bi-geo-alt text-gold me-3 mt-1"></i>
                        <p class="small mb-0 text-white-50">Darul Ifta Kauzariyya, <br>kauzariyya Compound <br>Edathala
                            North, Aluva, Kerala.</p>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-envelope text-gold me-3"></i>
                        <p class="small mb-0"><a
                                href="mailto:darulifta.kauzariyya@gmail.com">darulifta.kauzariyya@gmail.com</a></p>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-headset text-gold me-3"></i>
                        <p class="small mb-0"><a href="tel:+919495666777">+91 9495 666 777</a></p>
                    </div>

                </div>
            </div>

            <hr class="my-4" style="border-color:rgba(255,255,255,.1)">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="text-white-50 small mb-0">&copy; {{ date('Y') }} Darul Ifta Kauzariyya. Designed for
                    Enlightenment.</p>
                <div class="small mt-2 mt-md-0">
                    <span class="text-white-50">This app created & developed by</span> <span
                        class="text-gold fw-bold">Haris I M</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>