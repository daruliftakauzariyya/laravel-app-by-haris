<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Submitted Successfully</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Noto+Naskh+Arabic:wght@400;600;700&family=Noto+Nastaliq+Urdu:wght@400;600;700&family=Noto+Serif+Malayalam:wght@400;500;600;700&family=Noto+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f9; color: #333; line-height: 1.6; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header { border-bottom: 2px solid #1a7a4a; padding-bottom: 15px; margin-bottom: 25px; }
        .header h2 { margin: 0; color: #1a7a4a; font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif; font-size: 1.35rem; }
        .footer { margin-top: 30px; font-size: 0.85rem; color: #777; border-top: 1px solid #eee; padding-top: 15px; text-align: center; }
        .btn { display: inline-block; background: #1a7a4a; color: #fff !important; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 20px; font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif; box-shadow: 0 3px 8px rgba(26,122,74,0.25); }
        .highlight { font-weight: bold; color: #1a7a4a; }
        .badge { display: inline-block; background: #c9a84c; color: #fff; padding: 3px 10px; border-radius: 4px; font-size: .85rem; font-weight: 600; }
        .font-malayalam { font-family: 'Noto Serif Malayalam', 'Gayathri', 'Manjari', 'Noto Sans', serif !important; line-height: 1.65; }
        .font-arabic { font-family: 'Noto Naskh Arabic', 'Amiri', serif !important; direction: rtl; text-align: right; line-height: 1.9; }
        .font-urdu { font-family: 'Noto Nastaliq Urdu', 'Noto Naskh Arabic', serif !important; direction: rtl; text-align: right; line-height: 2.1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Question Submitted Successfully</h2>
        </div>
        
        <p>Your question has been submitted successfully!</p>
        
        <p><span class="highlight">Question ID:</span> <span class="badge">{{ $question->question_id }}</span></p>
        
        <p>You can track the status of your query and view the answer (once provided) by clicking the link below:</p>
        
        @php
            $livePortalUrl = 'https://daruliftakauzariyya.com';
            $appUrl = config('app.url');
            $portalUrl = (empty($appUrl) || str_contains($appUrl, '127.0.0.1') || str_contains($appUrl, 'localhost')) ? $livePortalUrl : rtrim($appUrl, '/');
        @endphp
        <a href="{{ $portalUrl }}/user/questions/{{ $question->id }}" class="btn">View Your Question</a>
        
        <div style="margin-top: 30px; padding-top: 15px; border-top: 1px dashed #e2e8f0; text-align: center;">
            <a href="https://pages.razorpay.com/daruliftakauzariyya" target="_blank" style="color: #1a7a4a; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block;">
                <img src="{{ $portalUrl }}/assets/logo/fecher.png" alt="Darul Ifta Kauzariyya" style="width: 100%; max-width: 350px; height: auto; display: block; margin: 0 auto 10px auto; border-radius: 8px;">
                🎁 send your hadiyya to darul ifta kauzariyya
            </a>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} <a href="{{ $portalUrl }}" style="color:#777; text-decoration:none;">Darul Ifta Kauzariyya</a>. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
