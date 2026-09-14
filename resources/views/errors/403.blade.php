<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Restricted | Darul Ifta Kauzariyya</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons & Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo/fevi.png') }}">

    <style>
        :root {
            --brand-blue: #052961;
            --brand-gold: #c9a84c;
            --bg-canvas: #f8fafc;
        }

        body {
            background-color: var(--bg-canvas);
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .error-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(5, 41, 97, 0.06);
            max-width: 580px;
            width: 100%;
            padding: 48px 36px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--brand-gold), var(--brand-blue), var(--brand-gold));
        }

        .icon-circle {
            width: 88px;
            height: 88px;
            background: rgba(220, 38, 38, 0.08);
            color: #dc2626;
            border: 2px solid rgba(220, 38, 38, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 24px;
        }

        .error-badge {
            display: inline-block;
            background: #fef2f2;
            color: #b91c1c;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 14px;
            border-radius: 50px;
            margin-bottom: 16px;
        }

        .error-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .error-desc {
            font-size: 0.95rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .btn-action-primary {
            background: var(--brand-blue);
            color: #ffffff;
            font-weight: 700;
            border-radius: 9999px;
            padding: 12px 28px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(5, 41, 97, 0.25);
            transition: all 0.2s ease;
        }

        .btn-action-primary:hover {
            background: #031b40;
            color: #ffffff;
            transform: translateY(-2px);
        }

        .btn-action-secondary {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            border-radius: 9999px;
            padding: 12px 24px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-action-secondary:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
    </style>
</head>
<body>

    <div class="error-card">
        <div class="icon-circle">
            <i class="bi bi-shield-lock-fill"></i>
        </div>

        <div>
            <span class="error-badge">Error 403 • Restricted Area</span>
            <h1 class="error-title">Access Authorization Required</h1>
            <p class="error-desc">
                {{ $exception->getMessage() ?: 'You do not have the required role privileges to access this area. If you believe this is a mistake, please sign in with an authorized account.' }}
            </p>
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
            @auth
                @php
                    $role = auth()->user()->role ? strtolower(auth()->user()->role->name) : 'questioner';
                    $dashboardUrl = match($role) {
                        'admin' => route('admin.dashboard'),
                        'mufti' => route('mufti.dashboard'),
                        default => route('user.dashboard'),
                    };
                @endphp
                <a href="{{ $dashboardUrl }}" class="btn-action-primary">
                    <i class="bi bi-speedometer2"></i>
                    <span>Go to My Dashboard</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-action-primary">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Log In to Account</span>
                </a>
            @endauth

            <a href="{{ url('/') }}" class="btn-action-secondary">
                <i class="bi bi-house-door"></i>
                <span>Return to Home</span>
            </a>
        </div>
    </div>

</body>
</html>
