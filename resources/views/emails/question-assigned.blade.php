<!DOCTYPE html>
<html><head><style>
body{font-family:Arial,sans-serif;background:#f4f6f9;margin:0;padding:20px;}
.container{max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1);}
.header{background:linear-gradient(135deg,#0d6efd,#0a58ca);color:#fff;padding:25px 30px;}
.header h2{margin:0;font-size:1.3rem;}
.body{padding:25px 30px;line-height:1.7;color:#333;}
.badge{display:inline-block;background:#c9a84c;color:#fff;padding:4px 12px;border-radius:4px;font-size:.85rem;}
.footer{background:#f8f8f8;padding:15px 30px;font-size:.8rem;color:#888;text-align:center;border-top:1px solid #eee;}
.btn{display:inline-block;background:#0d6efd;color:#fff;padding:10px 24px;border-radius:5px;text-decoration:none;font-weight:bold;margin-top:15px;}
</style></head>
<body>
<div class="container">
    <div class="header"><h2>🎓 Question Assigned to You</h2></div>
    <div class="body">
        <p>Dear <strong>{{ $question->assignedMufti->name }}</strong>,</p>
        <p>A question has been assigned to you for Fatwa.</p>
        <p><strong>Question ID:</strong> <span class="badge">{{ $question->question_id }}</span></p>
        <p><strong>Madhab:</strong> {{ $question->madhab?->name ?? 'General' }}</p>
        <p><strong>Question:</strong></p>
        <p style="background:#f8f8f8;padding:12px;border-radius:5px;border-left:3px solid #0d6efd;">{{ Str::limit($question->body, 300) }}</p>
        <a href="{{ url('/mufti/questions/' . $question->id) }}" class="btn">Submit Answer →</a>
    </div>
    <div class="footer">© {{ date('Y') }} Darul Ifta Kauzariyya.</div>
</div>
</body></html>
