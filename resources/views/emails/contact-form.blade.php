<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Inquiry</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Noto+Naskh+Arabic:wght@400;600;700&family=Noto+Nastaliq+Urdu:wght@400;600;700&family=Noto+Serif+Malayalam:wght@400;500;600;700&family=Noto+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background: #f8fafc; }
        .container { max-width: 600px; margin: 20px auto; padding: 25px; border: 1px solid #e2e8f0; border-radius: 10px; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.04); }
        .header { background: #295A75; color: #ffffff; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
        .header h3 { margin: 0 0 5px; font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif; font-size: 1.25rem; }
        .header p { margin: 0; opacity: 0.85; font-size: 0.85rem; }
        .content { padding: 20px 10px; background: #fff; }
        .field { margin-bottom: 15px; border-bottom: 1px solid #f0f4f8; padding-bottom: 12px; }
        .label { font-weight: bold; color: #295A75; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif; }
        .value { margin-top: 6px; font-size: 0.95rem; color: #2d3748; }
        .footer { text-align: center; margin-top: 25px; font-size: 0.8rem; color: #718096; border-top: 1px solid #edf2f7; padding-top: 15px; }
        .font-malayalam { font-family: 'Noto Serif Malayalam', 'Gayathri', 'Manjari', 'Noto Sans', serif !important; line-height: 1.65; }
        .font-arabic { font-family: 'Noto Naskh Arabic', 'Amiri', serif !important; direction: rtl; text-align: right; line-height: 1.9; }
        .font-urdu { font-family: 'Noto Nastaliq Urdu', 'Noto Naskh Arabic', serif !important; direction: rtl; text-align: right; line-height: 2.1; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h3>New Contact Inquiry</h3>
            <p>Darul Ifta Kauzariyya Portal</p>
        </div>
        <div class="content">
            <div class="field">
                <div class="label">Name:</div>
                <div class="value">{{ $data['first_name'] }} {{ $data['last_name'] }}</div>
            </div>
            <div class="field">
                <div class="label">Email Address:</div>
                <div class="value"><a href="mailto:{{ $data['email'] }}" style="color:#295A75;text-decoration:none;font-weight:600;">{{ $data['email'] }}</a></div>
            </div>
            <div class="field">
                <div class="label">Message:</div>
                @php
                    $msgText = $data['message'] ?? '';
                    $msgClass = '';
                    if (preg_match('/[\x{0D00}-\x{0D7F}]/u', $msgText)) {
                        $msgClass = 'font-malayalam';
                    } elseif (preg_match('/[\x{0600}-\x{06FF}]/u', $msgText)) {
                        $msgClass = 'font-arabic';
                    }
                @endphp
                <div class="value {{ $msgClass }}" style="white-space: pre-line; background: #f8fafc; padding: 12px; border-radius: 6px; border-left: 3px solid #295A75;">{{ $msgText }}</div>
            </div>
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
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} <a href="{{ $portalUrl }}" style="color:#718096;text-decoration:none;">Darul Ifta Kauzariyya</a>. This message was sent from the public contact portal.
        </div>
    </div>
</body>
</html>
