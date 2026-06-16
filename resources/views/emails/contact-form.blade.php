<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; }
        .header { background: #295A75; color: #ffffff; padding: 15px; border-radius: 6px 6px 0 0; text-align: center; }
        .content { padding: 20px; background: #fdfdfd; }
        .field { margin-bottom: 15px; border-bottom: 1px solid #f0f0f0; padding-bottom: 10px; }
        .label { font-weight: bold; color: #295A75; font-size: 0.9rem; text-transform: uppercase; }
        .value { margin-top: 5px; font-size: 1.05rem; }
        .footer { text-align: center; margin-top: 20px; font-size: 0.8rem; color: #666; }
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
                <div class="value"><a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a></div>
            </div>
            <div class="field">
                <div class="label">Message:</div>
                <div class="value" style="white-space: pre-line;">{{ $data['message'] }}</div>
            </div>
            <div style="margin-top: 25px; padding-top: 15px; border-top: 1px dashed #e2e8f0; text-align: center;">
                <a href="https://pages.razorpay.com/daruliftakauzariyya" target="_blank" style="color: #1a7a4a; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block;">
                    🎁 send your hadiyya to darul ifta kauzariyya
                </a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Darul Ifta Kauzariyya. This message was sent from the public contact portal.
        </div>
    </div>
</body>
</html>
