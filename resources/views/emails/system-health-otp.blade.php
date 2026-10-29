<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Health Verification OTP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 20px; background: #f8fafc; }
        .container { max-width: 560px; margin: 20px auto; padding: 32px; border: 1px solid #e2e8f0; border-radius: 16px; background: #ffffff; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .alert-badge { display: inline-flex; align-items: center; gap: 6px; background: #fef3c7; color: #b45309; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px; }
        .header h2 { color: #0f172a; margin: 0 0 8px 0; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.4rem; font-weight: 800; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #0284c7; border-radius: 8px; padding: 14px 18px; margin: 18px 0; font-size: 0.88rem; }
        .info-box .title { font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .info-box .meta { font-size: 0.82rem; color: #64748b; }
        .otp-wrapper { text-align: center; margin: 28px 0; }
        .otp-box { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border: 2px dashed #16a34a; padding: 16px 28px; font-size: 34px; font-weight: 800; letter-spacing: 8px; display: inline-block; border-radius: 12px; color: #15803d; font-family: 'Courier New', monospace; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.08); }
        .warning-text { background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; padding: 12px 16px; border-radius: 8px; font-size: 0.84rem; margin-top: 20px; line-height: 1.5; }
        .footer { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 32px; border-top: 1px solid #f1f5f9; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert-badge">
            🔒 Developer Identity Verification
        </div>
        
        <div class="header">
            <h2>System Health & Diagnostics Portal</h2>
            <p style="color: #64748b; margin: 0; font-size: 0.95rem;">Darul Ifta Kauzariyya Infrastructure Security Gateway</p>
        </div>

        <p style="margin-top: 20px;">An access request has been initiated for the <strong>System Health & Diagnostics Portal</strong>.</p>

        <div class="info-box">
            <div class="title">Access Request Telemetry</div>
            <div class="meta">
                <strong>IP Address:</strong> <code>{{ $ipAddress }}</code><br>
                <strong>Device/Agent:</strong> {{ Str::limit($userAgent, 120) }}<br>
                <strong>Target Identity:</strong> <code>haris.imran.etpa@gmail.com</code>
            </div>
        </div>

        <p style="text-align: center; color: #475569; font-weight: 600; margin-bottom: 8px;">Use the single-use 6-digit verification code below to authenticate:</p>
        
        <div class="otp-wrapper">
            <div class="otp-box">
                {{ $otp }}
            </div>
        </div>

        <div class="warning-text">
            <strong>Security Notice:</strong> This OTP expires in <strong>{{ $expiresInMinutes }} minutes</strong> and will be invalidated after 5 failed attempts or upon single successful verification. If you did not initiate this request, your network credentials may be compromised.
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Darul Ifta Kauzariyya &bull; All administrative access attempts are cryptographically audited.
        </div>
    </div>
</body>
</html>

