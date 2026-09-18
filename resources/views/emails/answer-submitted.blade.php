<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mufti Answer Submitted</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Noto+Naskh+Arabic:wght@400;600;700&family=Noto+Nastaliq+Urdu:wght@400;600;700&family=Noto+Serif+Malayalam:wght@400;500;600;700&family=Noto+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,.08); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #fd7e14, #e8610a); color: #fff; padding: 25px 30px; }
        .header h2 { margin: 0; font-size: 1.3rem; font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif; }
        .body { padding: 25px 30px; line-height: 1.7; color: #333; }
        .btn { display: inline-block; background: #1a7a4a; color: #fff !important; padding: 12px 26px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif; box-shadow: 0 3px 8px rgba(26,122,74,0.25); }
        .footer { background: #f8fafc; padding: 18px 30px; font-size: .8rem; color: #888; text-align: center; border-top: 1px solid #eee; }
        .font-malayalam { font-family: 'Noto Serif Malayalam', 'Gayathri', 'Manjari', 'Noto Sans', serif !important; line-height: 1.65; }
        .font-arabic { font-family: 'Noto Naskh Arabic', 'Amiri', serif !important; direction: rtl; text-align: right; line-height: 1.9; }
        .font-urdu { font-family: 'Noto Nastaliq Urdu', 'Noto Naskh Arabic', serif !important; direction: rtl; text-align: right; line-height: 2.1; }
    </style>
</head>
<body>
<div class="container">
    <div class="header"><h2>📝 Mufti Answer Submitted</h2></div>
    <div class="body">
        <p>A Mufti has submitted an answer for review.</p>
        <p><strong>Question ID:</strong> {{ $answer->question_id ?? ($answer->question->question_id ?? '') }}</p>
        <p><strong>Mufti:</strong> {{ $answer->assignedMufti?->name ?? ($answer->mufti?->name ?? 'Mufti') }}</p>
        @php
            $summaryText = $answer->title ?? ($answer->summary ?? '');
            $sumClass = '';
            if (preg_match('/[\x{0D00}-\x{0D7F}]/u', $summaryText)) {
                $sumClass = 'font-malayalam';
            } elseif (preg_match('/[\x{0600}-\x{06FF}]/u', $summaryText)) {
                $sumClass = 'font-arabic';
            }
        @endphp
        <p><strong>Summary:</strong> <span class="{{ $sumClass }}">{{ $summaryText }}</span></p>
        @php
            $livePortalUrl = 'https://daruliftakauzariyya.com';
            $appUrl = config('app.url');
            $portalUrl = (empty($appUrl) || str_contains($appUrl, '127.0.0.1') || str_contains($appUrl, 'localhost')) ? $livePortalUrl : rtrim($appUrl, '/');
        @endphp
        <a href="{{ $portalUrl }}/admin/questions/{{ $answer->id ?? $answer->question?->id }}/verify" class="btn">Verify & Publish →</a>
        <div style="margin-top: 30px; padding-top: 15px; border-top: 1px dashed #e2e8f0; text-align: center;">
            <a href="https://pages.razorpay.com/daruliftakauzariyya" target="_blank" style="color: #1a7a4a; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block;">
                <img src="{{ $portalUrl }}/assets/logo/fecher.png" alt="Darul Ifta Kauzariyya" style="width: 100%; max-width: 350px; height: auto; display: block; margin: 0 auto 10px auto; border-radius: 8px;">
                🎁 send your hadiyya to darul ifta kauzariyya
            </a>
        </div>
    </div>
    <div class="footer">© {{ date('Y') }} <a href="{{ $portalUrl }}" style="color:#888; text-decoration:none;">Darul Ifta Kauzariyya</a>.</div>
</div>
</body>
</html>
