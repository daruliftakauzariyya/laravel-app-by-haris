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
    <title>@yield('title', 'Mufti Panel') — Darul Ifta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Amiri:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app-fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/logo/fevi.png') }}">

    {{-- PWA Meta --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#043975ff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('assets/logo/fevi.png') }}">

    <style>
        :root {
            --sidebar-w: 220px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f9;
            color: var(--text-main);
        }

        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--brand-blue-dark);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1060;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.25rem 1rem;
            background: rgba(0, 0, 0, .15);
            border-bottom: 5px solid var(--brand-gold);
        }

        .sidebar-brand h6 {
            color: #fff;
            font-weight: 700;
            margin: 0;
        }

        .sidebar-brand small {
            color: rgba(255, 255, 255, .5);
            font-size: .75rem;
        }

        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, .7);
            padding: .7rem 1.25rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .88rem;
            border-left: 4px solid transparent;
            transition: var(--transition-base);
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, .05);
            border-left-color: var(--brand-gold);
        }

        .main-content {
            margin-left: var(--sidebar-w);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .page-content {
            padding: 1.5rem;
        }

        .arabic-text {
            font-family: 'Amiri', serif;
            font-size: 1.2rem;
            direction: rtl;
            text-align: right;
        }

        /* Sidebar Responsive Logic */
        @media (max-width: 991px) {
            :root {
                --sidebar-w: 240px;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }

            body.sidebar-open {
                overflow: hidden;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1050;
                display: none;
            }

            body.sidebar-open .sidebar-overlay {
                display: block;
            }

            .topbar {
                padding: 0.75rem 1rem;
            }
        }

        .sidebar-nav .bg-white {
            background: rgba(255, 255, 255, 0.05) !important;
        }

        .sidebar-nav .nav-link-sub {
            font-size: 0.8rem;
            padding-left: 3.25rem;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NBPW3VHH" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    {{-- Sidebar Overlay --}}
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <div class="sidebar">
        <div class="sidebar-brand">
            <h6><i class="bi bi-person-badge me-2"></i>Mufti Panel</h6>
            <small>{{ Auth::user()->name }}</small>
        </div>
        <nav class="sidebar-nav mt-2">
            <a href="{{ route('mufti.dashboard') }}"
                class="nav-link {{ request()->routeIs('mufti.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <div class="nav-item">
                <a href="{{ route('mufti.questions') }}"
                    class="nav-link {{ request()->routeIs('mufti.questions*') && !request()->has('tab') ? 'active' : '' }}"
                    data-bs-toggle="collapse" data-bs-target="#questionsSub">
                    <i class="bi bi-question-circle"></i> My Questions <i class="bi bi-chevron-down ms-auto small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('mufti.questions*') ? 'show' : '' }}" id="questionsSub">
                    <a href="{{ route('mufti.questions', ['tab' => 'assigned']) }}"
                        class="nav-link nav-link-sub {{ request()->get('tab') == 'assigned' ? 'active' : '' }}">
                        <i class="bi bi-journal-text small"></i> Assigned
                    </a>
                    <a href="{{ route('mufti.questions', ['tab' => 'verified']) }}"
                        class="nav-link nav-link-sub {{ request()->get('tab') == 'verified' ? 'active' : '' }}">
                        <i class="bi bi-shield-check small"></i> Verified by Me
                    </a>
                    @if(Auth::user()->can_check_approve)
                        <a href="{{ route('mufti.questions', ['tab' => 'approved']) }}"
                            class="nav-link nav-link-sub {{ request()->get('tab') == 'approved' ? 'active' : '' }}">
                            <i class="bi bi-patch-check small"></i> Approved by Me
                        </a>
                    @endif
                </div>
            </div>
            <a href="{{ route('mufti.questions.pending') }}"
                class="nav-link {{ request()->routeIs('mufti.questions.pending') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Pending Tasks
            </a>
            <a href="{{ route('mufti.profile') }}"
                class="nav-link {{ request()->routeIs('mufti.profile') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Profile
            </a>
            <a href="{{ route('home') }}" class="nav-link"><i class="bi bi-globe"></i> Public Site</a>
            <div class="px-2 mt-3">
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="btn btn-sm btn-outline-light w-100"><i
                            class="bi bi-box-arrow-right me-1"></i>Logout</button>
                </form>
            </div>
        </nav>
    </div>
    <div class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-brand d-lg-none" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <h6 class="mb-0 fw-bold text-muted">@yield('topbar-title', 'Mufti Panel')</h6>
            </div>

            <div class="d-none d-md-flex align-items-center gap-4">
                <div class="text-end line-height-1">
                    <div class="fw-bold text-primary small"><i
                            class="bi bi-calendar3 me-1"></i>{{ \App\Helpers\HijriHelper::now() }}</div>
                    <div class="text-muted x-small">{{ now()->format('l, d F Y') }}</div>
                </div>
                <div class="border-start ps-4 text-end line-height-1">
                    <div class="fw-bold text-dark small"><i class="bi bi-clock me-1"></i><span
                            id="liveClock">{{ now()->format('h:i:s A') }}</span></div>
                    @php
                        $totalSecs = \App\Models\PortalVisitorLog::getTotalSpentTime(Auth::id());
                        $h = floor($totalSecs / 3600);
                        $m = floor(($totalSecs / 60) % 60);
                        $spent = ($h > 0 ? $h . 'h ' : '') . $m . 'm';
                    @endphp
                    <div class="text-muted x-small">Spent: <span class="fw-semibold text-success">{{ $spent }}</span>
                    </div>
                </div>
            </div>
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
    <script>
        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW Registered', reg))
                    .catch(err => console.log('SW Failed', err));
            });
        }

        function toggleSidebar() {
            document.body.classList.toggle('sidebar-open');
        }

        // Live Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            const clockEl = document.getElementById('liveClock');
            if (clockEl) clockEl.innerText = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    @stack('scripts')
</body>

</html>