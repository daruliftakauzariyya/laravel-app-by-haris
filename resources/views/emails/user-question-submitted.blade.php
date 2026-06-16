<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f9; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { border-bottom: 2px solid #1a7a4a; padding-bottom: 15px; margin-bottom: 25px; }
        .footer { margin-top: 30px; font-size: 0.85rem; color: #777; border-top: 1px solid #eee; padding-top: 15px; }
        .btn { display: inline-block; background: #1a7a4a; color: #fff; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 20px; }
        .highlight { font-weight: bold; color: #1a7a4a; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0; color: #1a7a4a;">Question Submitted Successfully</h2>
        </div>
        
        <p>Your question has been submitted successfully!</p>
        
        <p><span class="highlight">Question ID:</span> {{ $question->question_id }}</p>
        
        <p>You can track the status of your query and view the answer (once provided) by clicking the link below:</p>
        
        <a href="{{ route('user.question.show', $question) }}" class="btn">View Your Question</a>
        
        <div style="margin-top: 25px; padding-top: 15px; border-top: 1px dashed #e2e8f0; text-align: center;">
            <a href="https://pages.razorpay.com/daruliftakauzariyya" target="_blank" style="color: #1a7a4a; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block;">
                🎁 send your hadiyya to darul ifta kauzariyya
            </a>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Darul Ifta Kauzariyya. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
