<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f9f9f9; }
        .container { max-width: 600px; margin: 30px auto; padding: 30px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; }
        .logo { color: #1a7a4a; font-size: 26px; font-weight: bold; text-decoration: none; }
        .content { font-size: 16px; color: #4a5568; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #1a7a4a, #15613a); color: #ffffff !important; font-weight: bold; text-decoration: none; padding: 12px 30px; border-radius: 8px; margin: 25px 0; font-size: 16px; transition: background 0.3s; text-align: center; box-shadow: 0 4px 12px rgba(26,122,74,0.2); }
        .cta-button:hover { background: #15613a; }
        .footer { font-size: 12px; color: #a0aec0; text-align: center; margin-top: 40px; border-top: 1px solid #edf2f7; padding-top: 20px; }
        .benefits-card { background-color: #f7fafc; border-left: 4px solid #c9a84c; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0; }
        .benefits-title { font-weight: bold; color: #2d3748; margin-bottom: 5px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="#" class="logo">Darul Ifta Kauzariyya</a>
        </div>
        <div class="content">
            <p>Assalamu Alaikum <strong>{{ $user->name }}</strong>,</p>
            <p>Thank you for submitting your question to Darul Ifta Kauzariyya. Your query has been successfully logged and sent to our Muftis for review. You can track its status under your dashboard.</p>
            
            <p>We noticed that you skipped completing your address and profile details when posting your question. While your question is already submitted, we kindly request you to provide these missing details to complete your scholarly profile.</p>
            
            <div class="benefits-card">
                <div class="benefits-title">Why complete your profile?</div>
                <p style="margin: 0; font-size: 13.5px; color: #718096;">Providing your gender, country, and location details helps our scholars understand context when issuing fatwas, which can be critical for region-specific rulings.</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('user.dashboard') }}#profile" class="cta-button">Complete Your Profile Now</a>
            </div>

            <p>If you have already updated your details, please disregard this message.</p>
            <p>Jazakumullah Khair,<br><strong>Darul Ifta Kauzariyya Team</strong></p>
            <div style="margin-top: 25px; padding-top: 15px; border-top: 1px dashed #e2e8f0; text-align: center;">
                <a href="https://pages.razorpay.com/daruliftakauzariyya" target="_blank" style="color: #1a7a4a; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block;">
                    🎁 send your hadiyya to darul ifta kauzariyya
                </a>
            </div>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} Darul Ifta Kauzariyya. All rights reserved.
        </div>
    </div>
</body>
</html>
