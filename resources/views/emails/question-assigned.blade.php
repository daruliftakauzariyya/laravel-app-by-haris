<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Assigned – {{ $question->question_id }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Noto+Naskh+Arabic:wght@400;600;700&family=Noto+Nastaliq+Urdu:wght@400;600;700&family=Noto+Serif+Malayalam:wght@400;500;600;700&family=Noto+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 620px; margin: 0 auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,.08); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #0d6efd, #0a58ca); color: #fff; padding: 25px 30px; }
        .header h2 { margin: 0; font-size: 1.3rem; font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif; }
        .body { padding: 25px 30px; line-height: 1.7; color: #333; }
        .badge { display: inline-block; background: #c9a84c; color: #fff; padding: 4px 12px; border-radius: 4px; font-size: .85rem; font-weight: 600; }
        .footer { background: #f8fafc; padding: 18px 30px; font-size: .8rem; color: #888; text-align: center; border-top: 1px solid #eee; }
        .btn { display: inline-block; background: #0d6efd; color: #fff !important; padding: 12px 26px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif; box-shadow: 0 3px 8px rgba(13,110,253,0.25); }
        .question-box { background: #f8fafc; padding: 18px 20px; border-radius: 8px; border-left: 4px solid #0d6efd; font-size: 0.96rem; color: #1f2937; line-height: 1.75; font-family: 'Noto Serif Malayalam', 'Noto Naskh Arabic', 'Amiri', 'Noto Sans', 'Segoe UI', sans-serif; word-break: break-word; margin: 10px 0 20px 0; }
        .question-box p { margin: 0 0 12px 0; line-height: 1.75; }
        .question-box p:last-child { margin-bottom: 0; }
        .font-malayalam { font-family: 'Noto Serif Malayalam', 'Gayathri', 'Manjari', 'Noto Sans', serif !important; line-height: 1.65; }
        .font-arabic { font-family: 'Noto Naskh Arabic', 'Amiri', serif !important; direction: rtl; text-align: right; line-height: 1.9; }
        .font-urdu { font-family: 'Noto Nastaliq Urdu', 'Noto Naskh Arabic', serif !important; direction: rtl; text-align: right; line-height: 2.1; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>🎓 Question Assigned to You</h2>
        <p style="margin:.3rem 0 0;opacity:.85;font-size:.9rem;">Darul Ifta Kauzariyya Scholar Portal</p>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $question->assignedMufti?->name ?? 'Scholar' }}</strong>,</p>
        <p>A question has been assigned to you for Fatwa.</p>
        <p><strong>Question ID:</strong> <span class="badge">{{ $question->question_id }}</span></p>
        @if(!empty($question->title))
            <p><strong>Title:</strong> <span dir="auto">{{ $question->title }}</span></p>
        @endif
        <p><strong>Madhab:</strong> {{ $question->madhab?->name == 'Shafii' ? "Shafi'i" : ($question->madhab?->name ?? 'General/Other') }}</p>
        <p style="margin-top: 18px; margin-bottom: 6px;"><strong>Question Content:</strong></p>
        <div class="question-box">
            @safeEmailHtml($question->body)
        </div>
        @php
            $livePortalUrl = 'https://daruliftakauzariyya.com';
            $appUrl = config('app.url');
            $portalUrl = (empty($appUrl) || str_contains($appUrl, '127.0.0.1') || str_contains($appUrl, 'localhost')) ? $livePortalUrl : rtrim($appUrl, '/');
        @endphp
        <a href="{{ $portalUrl }}/mufti/questions/{{ $question->id }}" class="btn">Submit Answer →</a>
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
