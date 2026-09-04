<!DOCTYPE html>
<html><head><style>
body{font-family:Arial,sans-serif;background:#f4f6f9;margin:0;padding:20px;}
.container{max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1);}
.header{background:linear-gradient(135deg,#fd7e14,#e8610a);color:#fff;padding:25px 30px;}
.body{padding:25px 30px;line-height:1.7;color:#333;}
.btn{display:inline-block;background:#1a7a4a;color:#fff;padding:10px 24px;border-radius:5px;text-decoration:none;font-weight:bold;margin-top:15px;}
.footer{background:#f8f8f8;padding:15px 30px;font-size:.8rem;color:#888;text-align:center;}
</style></head>
<body>
<div class="container">
    <div class="header"><h2 style="margin:0;">📝 Mufti Answer Submitted</h2></div>
    <div class="body">
        <p>A Mufti has submitted an answer for review.</p>
        <p><strong>Question ID:</strong> {{ $answer->question_id ?? ($answer->question->question_id ?? '') }}</p>
        <p><strong>Mufti:</strong> {{ $answer->assignedMufti?->name ?? ($answer->mufti?->name ?? 'Mufti') }}</p>
        <p><strong>Summary:</strong> {{ $answer->title ?? ($answer->summary ?? '') }}</p>
        <a href="{{ url('/admin/questions/' . ($answer->id ?? $answer->question?->id) . '/verify') }}" class="btn">Verify & Publish →</a>
        <div style="margin-top: 25px; padding-top: 15px; border-top: 1px dashed #e2e8f0; text-align: center;">
            <a href="https://pages.razorpay.com/daruliftakauzariyya" target="_blank" style="color: #1a7a4a; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block;">
                <img src="{{ asset('assets/logo/fecher.png') }}" alt="Darul Ifta Kauzariyya" style="width: 100%; max-width: 350px; height: auto; display: block; margin: 0 auto 10px auto; border-radius: 8px;">
                🎁 send your hadiyya to darul ifta kauzariyya
            </a>
        </div>
    </div>
    <div class="footer">© {{ date('Y') }} Darul Ifta Kauzariyya.</div>
</div>
</body></html>
