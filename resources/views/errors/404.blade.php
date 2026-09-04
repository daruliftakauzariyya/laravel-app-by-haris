<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatwa Not Found — Darul Ifta Kauzariyya</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Literata:ital,opsz,wght@0,7..72,400;0,7..72,600;1,7..72,400&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Icons & Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/logo/fevi.png') }}">

    <style>
        :root {
            --brand-green: #1a7a4a;
            --brand-dark: #092217;
            --brand-gold: #c9a84c;
            --bg-parchment: #fef8f5;
        }

        body {
            background-color: var(--bg-parchment);
            font-family: 'Literata', serif;
            color: #1d1b19;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .not-found-card {
            background: #ffffff;
            border: 1px solid #e7e1de;
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(9, 34, 23, 0.06);
            max-width: 640px;
            width: 100%;
            padding: 48px 36px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .not-found-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--brand-gold), var(--brand-green), var(--brand-gold));
        }

        .icon-circle {
            width: 88px;
            height: 88px;
            background: rgba(26, 122, 74, 0.08);
            color: var(--brand-green);
            border: 2px solid rgba(26, 122, 74, 0.2);
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
            border: 1px solid #fecaca;
            font-family: 'Inter', sans-serif;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 14px;
            border-radius: 50px;
            margin-bottom: 16px;
        }

        .error-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--brand-dark);
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .error-desc {
            font-size: 1rem;
            color: #64748b;
            line-height: 1.65;
            margin-bottom: 28px;
        }

        .search-box {
            position: relative;
            margin-bottom: 32px;
        }

        .search-box input {
            width: 100%;
            padding: 14px 48px 14px 20px;
            border-radius: 50px;
            border: 1px solid #cbd5e1;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s;
            background: #f8fafc;
        }

        .search-box input:focus {
            border-color: var(--brand-green);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(26, 122, 74, 0.12);
        }

        .search-box button {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: var(--brand-green);
            color: #fff;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
        }

        .search-box button:hover {
            background: #145a37;
        }

        .action-btn {
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-brand-primary {
            background: var(--brand-green);
            color: #ffffff;
            border: 1px solid transparent;
        }

        .btn-brand-primary:hover {
            background: #145a37;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-brand-outline {
            background: transparent;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-brand-outline:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .arabic-sub {
            font-family: 'Amiri', serif;
            font-size: 1.25rem;
            color: #94a3b8;
            margin-top: 24px;
            direction: rtl;
        }
    </style>
</head>
<body>

    <div class="not-found-card">
        <!-- Logo -->
        <div class="mb-4">
            <a href="{{ route('home') }}">
                <img src="{{ asset('assets/logo/logoblueNew.svg') }}" alt="Darul Ifta Kauzariyya" style="height: 42px; width: auto;" onerror="this.src='{{ asset('assets/logo/Darul-Ifta-Logo.png') }}'">
            </a>
        </div>

        <!-- Visual Icon -->
        <div class="icon-circle">
            <i class="bi bi-journal-x"></i>
        </div>

        <div>
            <span class="error-badge">Record Unavailable</span>
        </div>

        <!-- Message -->
        <h1 class="error-title">The fatwa is not in our platform</h1>
        
        <p class="error-desc">
            The requested fatwa or inquiry record could not be found in our database. It may have been relocated, unpublished, or the reference ID is incorrect.
        </p>

        <!-- Search Bar -->
        <form action="{{ route('fatwa.archive') }}" method="GET" class="search-box">
            <input type="text" name="q" placeholder="Search fatwas by topic, question, or ID (e.g. A0001)..." required>
            <button type="submit" title="Search Archive">
                <i class="bi bi-search"></i>
            </button>
        </form>

        <!-- Quick Action Buttons -->
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('fatwa.archive') }}" class="action-btn btn-brand-primary">
                <i class="bi bi-collection"></i> Browse All Fatwas
            </a>
            <a href="{{ url()->previous() ?: route('home') }}" class="action-btn btn-brand-outline">
                <i class="bi bi-arrow-left"></i> Go Back
            </a>
            <a href="{{ route('home') }}" class="action-btn btn-brand-outline">
                <i class="bi bi-house"></i> Home
            </a>
        </div>

        <div class="arabic-sub">
            لم يتم العثور على الفتوى المطلوبة في قاعدة البيانات
        </div>
    </div>

</body>
</html>
