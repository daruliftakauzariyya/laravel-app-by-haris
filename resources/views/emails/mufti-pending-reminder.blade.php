<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Fatwa Reminder</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f9; margin: 0; padding: 0; }
        .wrapper { max-width: 620px; margin: 30px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
        .header { background: linear-gradient(135deg, #1a7a4a, #145e38); padding: 30px 35px; text-align: center; }
        .header h1 { color: #fff; font-size: 1.4rem; margin: 0 0 5px; }
        .header p { color: rgba(255,255,255,0.8); margin: 0; font-size: 0.9rem; }
        .arabic { font-family: 'Arial', serif; color: #c9a84c; font-size: 1.1rem; margin-bottom: 8px; direction: rtl; }
        .body { padding: 30px 35px; }
        .greeting { font-size: 1rem; color: #2d3748; margin-bottom: 20px; }
        .greeting b { color: #1a7a4a; }
        .alert-box { background: #fff8e1; border-left: 4px solid #f59e0b; padding: 14px 18px; border-radius: 6px; margin-bottom: 25px; font-size: 0.9rem; color: #5a4400; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        th { background: #f1f5f9; text-align: left; padding: 10px 14px; font-size: 0.8rem; text-transform: uppercase; color: #718096; letter-spacing: 0.5px; }
        td { padding: 12px 14px; border-bottom: 1px solid #edf2f7; font-size: 0.88rem; color: #4a5568; vertical-align: middle; }
        .qu-id { font-family: monospace; background: #f7fafc; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; }
        .days-badge { padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; display: inline-block; }
        .days-low  { background: #dcfce7; color: #166534; }
        .days-mid  { background: #fef9c3; color: #854d0e; }
        .days-high { background: #fee2e2; color: #991b1b; }
        .cta { text-align: center; margin-bottom: 30px; }
        .cta a { background: #1a7a4a; color: #fff; text-decoration: none; padding: 13px 32px; border-radius: 8px; font-weight: 700; display: inline-block; }
        .footer { background: #f8fafc; padding: 18px 35px; text-align: center; }
        .footer p { margin: 0; font-size: 0.78rem; color: #a0aec0; }
        .footer a { color: #1a7a4a; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="arabic">بسم الله الرحمن الرحيم</div>
        <h1>⏳ Pending Fatwa Reminder</h1>
        <p>Darul Ifta Kauzariyya — Automated Notification</p>
    </div>

    <div class="body">
        <p class="greeting">Assalamu Alaikum, <b>{{ $mufti->name }}</b></p>

        <div class="alert-box">
            You have <strong>{{ $pendingQuestions->count() }} question(s)</strong> that are assigned to you and are waiting for your answer. Please review and respond as soon as possible.
        </div>

        <table>
            <thead>
                <tr>
                    <th>QU-ID</th>
                    <th>Question Summary</th>
                    <th>Assigned On</th>
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
                    <td>{{ Str::limit(strip_tags($question->title ?: $question->body), 60) }}</td>
                    <td>{{ $question->assigned_at?->format('d M Y') ?? '—' }}</td>
                    <td><span class="days-badge {{ $badgeClass }}">{{ $daysWaiting }} days</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cta">
            <a href="{{ config('app.url') }}/mufti/questions">Go to My Questions →</a>
        </div>

        <p style="font-size:0.85rem; color:#718096; text-align: center;">
            Jazakallah Khairan for your service to the Ummah.<br>
            This reminder is sent every 3 days for pending questions.
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} <a href="{{ config('app.url') }}">Darul Ifta Kauzariyya</a> — All Rights Reserved</p>
        <p style="margin-top:5px;">This is an automated email. Please do not reply.</p>
    </div>
</div>
</body>
</html>
