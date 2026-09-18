<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification OTP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Noto+Naskh+Arabic:wght@400;600;700&family=Noto+Nastaliq+Urdu:wght@400;600;700&family=Noto+Serif+Malayalam:wght@400;500;600;700&family=Noto+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background: #f8fafc; }
        .container { max-width: 550px; margin: 20px auto; padding: 30px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.04); }
        .header { text-align: center; margin-bottom: 25px; }
        .header h2 { color: #1a7a4a; margin: 0; font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif; font-size: 1.4rem; }
        .otp-box { background: #f0f7f3; border: 2px dashed #1a7a4a; padding: 18px; font-size: 28px; font-weight: bold; letter-spacing: 6px; text-align: center; margin: 25px 0; border-radius: 8px; color: #1a7a4a; font-family: 'Courier New', monospace; }
        .footer { font-size: 12px; color: #718096; text-align: center; margin-top: 30px; border-top: 1px solid #edf2f7; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Darul Ifta Kauzariyya</h2>
        </div>
        <p>Hello,</p>
        <p>You requested an OTP to proceed with your verification. Please use the following code:</p>
        <div class="otp-box">
            {{ $otp }}
        </div>
        <p style="color: #666; font-size: 0.9rem;">This OTP will expire in 15 minutes. Do not share this code with anyone.</p>
        <p style="color: #888; font-size: 0.85rem;">If you did not request this OTP, please ignore this email.</p>
        
        @php
            $livePortalUrl = 'https://daruliftakauzariyya.com';
            $appUrl = config('app.url');
            $portalUrl = (empty($appUrl) || str_contains($appUrl, '127.0.0.1') || str_contains($appUrl, 'localhost')) ? $livePortalUrl : rtrim($appUrl, '/');
        @endphp
        <div style="margin-top: 30px; padding-top: 15px; border-top: 1px dashed #e2e8f0; text-align: center;">
            <a href="https://pages.razorpay.com/daruliftakauzariyya" target="_blank" style="color: #1a7a4a; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block;">
                <img src="{{ $portalUrl }}/assets/logo/fecher.png" alt="Darul Ifta Kauzariyya" style="width: 100%; max-width: 350px; height: auto; display: block; margin: 0 auto 10px auto; border-radius: 8px;">
                🎁 send your hadiyya to darul ifta kauzariyya
            </a>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} <a href="{{ $portalUrl }}" style="color:#718096;text-decoration:none;">Darul Ifta Kauzariyya</a>. All rights reserved.
        </div>
    </div>
</body>
</html>
