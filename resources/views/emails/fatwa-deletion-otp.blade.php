<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>High-Security Fatwa Deletion OTP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Noto Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 20px; background: #f8fafc; }
        .container { max-width: 560px; margin: 20px auto; padding: 32px; border: 1px solid #e2e8f0; border-radius: 16px; background: #ffffff; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .alert-badge { display: inline-flex; align-items: center; gap: 6px; background: #fee2e2; color: #dc2626; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px; }
        .header h2 { color: #0f172a; margin: 0 0 8px 0; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.5rem; font-weight: 800; }
        .fatwa-box { background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #dc2626; border-radius: 8px; padding: 14px 18px; margin: 18px 0; }
        .fatwa-box .title { font-weight: 700; color: #0f172a; font-size: 0.98rem; }
        .fatwa-box .meta { font-size: 0.82rem; color: #64748b; margin-top: 4px; }
        .otp-wrapper { text-align: center; margin: 28px 0; }
        .otp-box { background: linear-gradient(135deg, #fef2f2, #fff1f2); border: 2px dashed #dc2626; padding: 18px 24px; font-size: 32px; font-weight: 800; letter-spacing: 10px; display: inline-block; border-radius: 12px; color: #b91c1c; font-family: 'Courier New', monospace; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.08); }
        .warning-text { background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; padding: 12px 16px; border-radius: 8px; font-size: 0.85rem; margin-top: 20px; line-height: 1.5; }
        .footer { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 32px; border-top: 1px solid #f1f5f9; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert-badge">
            ⚠️ High-Security Action Request
        </div>
        
        <div class="header">
            <h2>Fatwa Deletion Authorization</h2>
            <p style="color: #64748b; margin: 0; font-size: 0.95rem;">Darul Ifta Kauzariyya Security Gateway</p>
        </div>

        <p style="margin-top: 20px;">Dear <strong>{{ $adminName }}</strong>,</p>
        <p>A request was initiated from your account to <strong>permanently delete</strong> the following fatwa from the portal:</p>

        <div class="fatwa-box">
            <div class="title">{{ $fatwaTitle ?: 'Islamic Ruling' }}</div>
            <div class="meta">Question ID: <strong>{{ $questionId }}</strong> &bull; Requested from IP: <code>{{ $ipAddress }}</code></div>
        </div>

        <p style="text-align: center; color: #475569; font-weight: 600; margin-bottom: 8px;">Use this 6-digit verification code to proceed to Identity Verification:</p>
        
        <div class="otp-wrapper">
            <div class="otp-box">
                {{ $otp }}
            </div>
        </div>

        <div class="warning-text">
            <strong>Critical Notice:</strong> This OTP is valid for <strong>10 minutes</strong>. Deletion is irreversible and will purge all scholarly answers, references, and attachments. If you did not initiate this request, change your administrative password immediately.
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Darul Ifta Kauzariyya Security System. All administrative actions are recorded.
        </div>
    </div>
</body>
</html>
