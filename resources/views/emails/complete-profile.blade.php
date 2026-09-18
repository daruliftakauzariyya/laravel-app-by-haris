<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Noto+Naskh+Arabic:wght@400;600;700&family=Noto+Nastaliq+Urdu:wght@400;600;700&family=Noto+Serif+Malayalam:wght@400;500;600;700&family=Noto+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 20px; background-color: #f9f9f9; }
        .container { max-width: 600px; margin: 20px auto; padding: 30px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; }
        .logo { color: #1a7a4a; font-size: 24px; font-weight: bold; text-decoration: none; font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif; }
        .content { font-size: 15px; color: #4a5568; line-height: 1.7; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #1a7a4a, #15613a); color: #ffffff !important; font-weight: bold; text-decoration: none; padding: 12px 30px; border-radius: 8px; margin: 25px 0; font-size: 15px; text-align: center; box-shadow: 0 4px 12px rgba(26,122,74,0.2); font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif; }
        .footer { font-size: 12px; color: #a0aec0; text-align: center; margin-top: 30px; border-top: 1px solid #edf2f7; padding-top: 20px; }
        .benefits-card { background-color: #f8fafc; border-left: 4px solid #c9a84c; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0; font-size: 0.9rem; }
        .benefits-title { font-weight: bold; color: #2d3748; margin-bottom: 5px; font-size: 14px; font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif; }
        .font-malayalam { font-family: 'Noto Serif Malayalam', 'Gayathri', 'Manjari', 'Noto Sans', serif !important; }
        .font-arabic { font-family: 'Noto Naskh Arabic', 'Amiri', serif !important; direction: rtl; text-align: right; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="https://daruliftakauzariyya.com" class="logo">Darul Ifta Kauzariyya</a>
        </div>
        <div class="content">
            <p>Assalamu Alaikum <strong>{{ $user->name }}</strong>,</p>
            <p>Thank you for submitting your question to Darul Ifta Kauzariyya. Your query has been successfully logged and sent to our Muftis for review. You can track its status under your dashboard.</p>
            
            <p>We noticed that you skipped completing your address and profile details when posting your question. While your question is already submitted, we kindly request you to provide these missing details to complete your scholarly profile.</p>
            
            <div class="benefits-card">
                <div class="benefits-title">Why complete your profile?</div>
                <div>A complete profile ensures accurate location records, tailored fatwa references, and faster verification by the Mufti board.</div>
            </div>

            @php
                $livePortalUrl = 'https://daruliftakauzariyya.com';
                $appUrl = config('app.url');
                $portalUrl = (empty($appUrl) || str_contains($appUrl, '127.0.0.1') || str_contains($appUrl, 'localhost')) ? $livePortalUrl : rtrim($appUrl, '/');
            @endphp

            <div style="text-align: center;">
                <a href="{{ $portalUrl }}/user/dashboard#profile" class="cta-button">Complete Your Profile Now →</a>
            </div>

            <p>If you have already updated your details, please disregard this message.</p>
            <p>Jazakumullah Khair,<br><strong>Darul Ifta Kauzariyya Team</strong></p>
            
            <div style="margin-top: 30px; padding-top: 15px; border-top: 1px dashed #e2e8f0; text-align: center;">
                <a href="https://pages.razorpay.com/daruliftakauzariyya" target="_blank" style="color: #1a7a4a; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block;">
                    <img src="{{ $portalUrl }}/assets/logo/fecher.png" alt="Darul Ifta Kauzariyya" style="width: 100%; max-width: 350px; height: auto; display: block; margin: 0 auto 10px auto; border-radius: 8px;">
                    🎁 send your hadiyya to darul ifta kauzariyya
                </a>
            </div>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} <a href="{{ $portalUrl }}" style="color:#a0aec0; text-decoration:none;">Darul Ifta Kauzariyya</a>. All rights reserved.
        </div>
    </div>
</body>
</html>
