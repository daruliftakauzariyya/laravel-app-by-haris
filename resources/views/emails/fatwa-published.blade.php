<!DOCTYPE html>
<html><head><style>
body{font-family:Arial,sans-serif;background:#f4f6f9;margin:0;padding:20px;}
.container{max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1);}
.header{background:linear-gradient(135deg,#1a7a4a,#145e38);color:#fff;padding:25px 30px;}
.body{padding:25px 30px;line-height:1.7;color:#333;}
.btn{display:inline-block;background:#c9a84c;color:#fff;padding:10px 24px;border-radius:5px;text-decoration:none;font-weight:bold;margin-top:15px;}
.footer{background:#f8f8f8;padding:15px 30px;font-size:.8rem;color:#888;text-align:center;}
</style></head>
<body>
<div class="container">
    <div class="header">
        <h2 style="margin:0;">✅ Your Fatwa is Published!</h2>
        <p style="margin:.3rem 0 0;opacity:.8;font-size:.9rem;">Darul Ifta Kauzariyya</p>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $fatwa->question->user->name }}</strong>,</p>
        <p>The answer to your question (ID: <strong>{{ $fatwa->question->question_id }}</strong>) has been verified and published.</p>
        <p><strong>Summary:</strong> {{ $fatwa->muftiAnswer->summary }}</p>
        <a href="{{ url('/fatwa-archive/' . $fatwa->id) }}" class="btn">View Full Fatwa →</a>
        <p style="margin-top:20px;color:#888;font-size:.9rem;">Jazakumullahu Khayran for trusting Darul Ifta Kauzariyya.</p>
        <div style="margin-top: 25px; padding-top: 15px; border-top: 1px dashed #e2e8f0; text-align: center;">
            <a href="https://pages.razorpay.com/daruliftakauzariyya" target="_blank" style="color: #1a7a4a; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block;">
                🎁 send your hadiyya to darul ifta kauzariyya
            </a>
        </div>
    </div>
    <div class="footer">© {{ date('Y') }} Darul Ifta Kauzariyya.</div>
</div>
</body></html>
