<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Fatwa Reminder</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f4f8; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(41, 90, 117, 0.1); border: 1px solid #e2e8f0; }
        .header { background: #295A75; padding: 40px 30px; text-align: center; border-bottom: 4px solid #A8883A; }
        .header h1 { color: #fff; font-size: 1.6rem; margin: 0 0 10px; letter-spacing: 1px; }
        .header p { color: rgba(255,255,255,0.85); margin: 0; font-size: 0.95rem; font-weight: 300; }
        .arabic { font-family: 'Amiri', serif; color: #A8883A; font-size: 1.3rem; margin-bottom: 12px; direction: rtl; }
        .body { padding: 40px 35px; }
        .greeting { font-size: 1.1rem; color: #1a202c; margin-bottom: 25px; }
        .greeting b { color: #295A75; }
        .alert-box { background: #fdfaf3; border-left: 4px solid #A8883A; padding: 18px 22px; border-radius: 8px; margin-bottom: 30px; font-size: 0.95rem; color: #433100; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 35px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); border-radius: 8px; overflow: hidden; }
        th { background: #f8fafc; text-align: left; padding: 15px; font-size: 0.75rem; text-transform: uppercase; color: #4a5568; letter-spacing: 1px; border-bottom: 2px solid #edf2f7; }
        td { padding: 15px; border-bottom: 1px solid #edf2f7; font-size: 0.9rem; color: #2d3748; vertical-align: middle; }
        .qu-id { font-family: 'Courier New', Courier, monospace; background: #ebf4f9; padding: 3px 8px; border-radius: 4px; font-size: 0.85rem; color: #295A75; font-weight: bold; }
        .days-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; display: inline-block; }
        .days-low  { background: #e6fffa; color: #2c7a7b; }
        .days-mid  { background: #fffaf0; color: #9c4221; }
        .days-high { background: #fff5f5; color: #c53030; }
        .cta { text-align: center; margin-bottom: 40px; }
        .cta a { background: #295A75; color: #fff; text-decoration: none; padding: 16px 36px; border-radius: 8px; font-weight: 700; display: inline-block; box-shadow: 0 4px 12px rgba(41, 90, 117, 0.2); transition: 0.2s; }
        .footer { background: #f8fafc; padding: 25px 35px; text-align: center; border-top: 1px solid #edf2f7; }
        .footer p { margin: 0; font-size: 0.8rem; color: #718096; line-height: 1.6; }
        .footer a { color: #295A75; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="arabic">بسم الله الرحمن الرحيم</div>
        <h1>⏳ Pending Fatwa Reminder</h1>
        <p>Darul Ifta Kauzariyya — Official Notification</p>
    </div>

    <div class="body">
        <p class="greeting">Assalamu Alaikum, <b>{{ $mufti->name }}</b></p>

        <div class="alert-box">
            You have <strong>{{ $pendingQuestions->count() }} question(s)</strong> that are currently assigned to you and are awaiting your scholarly guidance. Please review and respond at your earliest convenience.
        </div>

        <table>
            <thead>
                <tr>
                    <th>QU-ID</th>
                    <th>Question Summary</th>
                    <th>Days Waiting</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingQuestions as $question)
                @php
                    $daysWaiting = $question->assigned_at ? (int) $question->assigned_at->diffInDays(now()) : 0;
                    $badgeClass = $daysWaiting <= 3 ? 'days-low' : ($daysWaiting <= 7 ? 'days-mid' : 'days-high');
                @endphp
                <tr>
                    <td><span class="qu-id">{{ $question->question_id }}</span></td>
                    <td style="font-style: italic;">"{{ Str::limit(strip_tags($question->title ?: $question->body), 50) }}"</td>
                    <td><span class="days-badge {{ $badgeClass }}">{{ $daysWaiting }} days</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cta">
            <a href="{{ config('app.url') }}/mufti/questions">Go to My Scholar Dashboard →</a>
        </div>

        <p style="font-size:0.85rem; color:#718096; text-align: center; line-height: 1.6;">
            Jazakallah Khairan for your selfless service to the Ummah.<br>
            <span style="font-size: 0.75rem; opacity: 0.7;">This is an automated administrative reminder.</span>
        </p>
        <div style="margin-top: 25px; padding-top: 15px; border-top: 1px dashed #e2e8f0; text-align: center;">
            <a href="https://pages.razorpay.com/daruliftakauzariyya" target="_blank" style="color: #1a7a4a; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block;">
                🎁 send your hadiyya to darul ifta kauzariyya
            </a>
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} <a href="{{ config('app.url') }}">Darul Ifta Kauzariyya</a> — Institutional Repository</p>
        <p style="margin-top:5px; font-size: 0.7rem; color: #a0aec0;">Strictly Confidential. Intended for named recipient only.</p>
    </div>
</div>
</body>
</html>
