<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { text-align: center; margin-bottom: 20px; }
        .otp-box { background: #f4f4f4; padding: 15px; font-size: 24px; font-weight: bold; letter-spacing: 5px; text-align: center; margin: 20px 0; border-radius: 5px; color: #1a7a4a; }
        .footer { font-size: 12px; color: #777; text-align: center; margin-top: 30px; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #1a7a4a;">Darul Ifta Kauzariyya</h2>
        </div>
        <p>Hello,</p>
        <p>You requested an OTP to proceed with your verification. Please use the following code:</p>
        <div class="otp-box">
            {{ $otp }}
        </div>
        <p>This OTP will expire in 15 minutes. Do not share this code with anyone.</p>
        <p>If you did not request this OTP, please ignore this email.</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} Darul Ifta Kauzariyya. All rights reserved.
        </div>
    </div>
</body>
</html>
