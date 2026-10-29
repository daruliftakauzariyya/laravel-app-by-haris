<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'System Health & Diagnostics') — Darul Ifta Kauzariyya</title>

    <!-- Google Fonts & Bootstrap Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sh-bg: #0b1120;
            --sh-card-bg: #111827;
            --sh-card-border: #1f2937;
            --sh-card-hover: #1e293b;
            --sh-text-primary: #ffffff;
            --sh-text-secondary: #e2e8f0;
            --sh-text-muted: #cbd5e1;
            --sh-accent: #38bdf8;
            --sh-success: #34d399;
            --sh-warning: #fbbf24;
            --sh-danger: #f87171;
            --sh-font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --sh-font-mono: 'JetBrains Mono', monospace;
        }

        /* High-Contrast Readability Overrides */
        .text-muted {
            color: #cbd5e1 !important;
        }
        .text-secondary {
            color: #e2e8f0 !important;
        }
        .text-slate {
            color: #cbd5e1 !important;
        }
        .text-light {
            color: #f8fafc !important;
        }
        .text-white {
            color: #ffffff !important;
        }

        body {
            background-color: var(--sh-bg);
            color: var(--sh-text-primary);
            font-family: var(--sh-font-sans);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .mono {
            font-family: var(--sh-font-mono);
        }

        .sh-navbar {
            background: rgba(17, 24, 39, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--sh-card-border);
            padding: 0.85rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .sh-card {
            background-color: var(--sh-card-bg);
            border: 1px solid var(--sh-card-border);
            border-radius: 12px;
            padding: 1.25rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .sh-card:hover {
            border-color: #334155;
        }

        .pulse-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-ring {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.2); }
        }

        .pulse-healthy { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); }
        .pulse-healthy .pulse-dot { background-color: #10b981; }

        .pulse-degraded { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); }
        .pulse-degraded .pulse-dot { background-color: #f59e0b; }

        .pulse-critical { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); }
        .pulse-critical .pulse-dot { background-color: #ef4444; }

        /* Custom Tabs */
        .sh-nav-tabs {
            border-bottom: 1px solid var(--sh-card-border);
            gap: 4px;
        }
        .sh-nav-tabs .nav-link {
            color: #cbd5e1;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            background: transparent;
            border-radius: 0;
            transition: all 0.2s;
        }
        .sh-nav-tabs .nav-link:hover {
            color: #ffffff;
            border-color: #64748b;
        }
        .sh-nav-tabs .nav-link.active {
            color: #38bdf8;
            background: transparent;
            border-color: #0284c7;
            font-weight: 700;
        }

        .metric-value {
            font-size: 1.65rem;
            font-weight: 700;
            font-family: var(--sh-font-mono);
            line-height: 1.2;
            margin-top: 4px;
        }
        .metric-label {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #e2e8f0;
            font-weight: 700;
        }

        .table-dark-custom {
            --bs-table-bg: transparent;
            --bs-table-color: #f8fafc;
            --bs-table-border-color: #273549;
            font-size: 0.88rem;
        }
        .table-dark-custom th {
            color: #93c5fd !important;
            text-transform: uppercase;
            font-size: 0.76rem;
            letter-spacing: 0.6px;
            font-weight: 700;
            border-bottom: 2px solid #334155;
            padding: 0.85rem 1rem;
        }
        .table-dark-custom td {
            color: #f8fafc !important;
            padding: 0.85rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #1f2937;
        }
        .table-dark-custom td.text-muted,
        .table-dark-custom td.table-label {
            color: #cbd5e1 !important;
            font-weight: 600;
        }
        .table-dark-custom td.mono {
            color: #ffffff !important;
        }
        .table-dark-custom td.mono.text-muted {
            color: #cbd5e1 !important;
        }
        .table-dark-custom tr:hover td {
            background-color: rgba(30, 41, 59, 0.7);
            color: #ffffff !important;
        }

        /* High-contrast form controls */
        .form-control, .form-select {
            background-color: #1e293b !important;
            color: #ffffff !important;
            border-color: #475569 !important;
        }
        .form-control:focus, .form-select:focus {
            background-color: #1e293b !important;
            color: #ffffff !important;
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.25) !important;
        }
        .form-control::placeholder {
            color: #94a3b8 !important;
        }

        .status-pill {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 6px;
            display: inline-block;
        }
        .status-pill-2xx { background: rgba(16, 185, 129, 0.2); color: #34d399; font-weight: 700; }
        .status-pill-3xx { background: rgba(56, 189, 248, 0.2); color: #38bdf8; font-weight: 700; }
        .status-pill-4xx { background: rgba(245, 158, 11, 0.2); color: #fbbf24; font-weight: 700; }
        .status-pill-5xx { background: rgba(239, 68, 68, 0.2); color: #f87171; font-weight: 700; }

        .progress-dark {
            background-color: #1f2937;
            border-radius: 6px;
            overflow: hidden;
            height: 8px;
        }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global AJAX CSRF header configuration
        window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    </script>
    @stack('scripts')
</body>
</html>
