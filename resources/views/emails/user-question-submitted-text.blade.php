Question Submitted Successfully – {{ $question->question_id }}
==================================================

Assalamu Alaikum,

Your question has been submitted successfully to Darul Ifta Kauzariyya!

Question ID: {{ $question->question_id }}
@if(!empty($question->title))
Title: {{ $question->title }}
@endif

You can track the status of your query and view the answer (once provided) by visiting the link below:

@php
    $livePortalUrl = 'https://daruliftakauzariyya.com';
    $appUrl = config('app.url');
    $portalUrl = (empty($appUrl) || str_contains($appUrl, '127.0.0.1') || str_contains($appUrl, 'localhost')) ? $livePortalUrl : rtrim($appUrl, '/');
@endphp
{{ $portalUrl }}/user/questions/{{ $question->id }}

© {{ date('Y') }} Darul Ifta Kauzariyya. All rights reserved.
{{ $portalUrl }}

