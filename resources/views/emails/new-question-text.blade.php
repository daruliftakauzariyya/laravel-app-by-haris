New Question Submitted – {{ $question->question_id }}
==================================================

A new question has been submitted and requires your review.

Question ID: {{ $question->question_id }}
@if(!empty($question->title))
Title: {{ $question->title }}
@endif
Questioner: {{ $question->user?->name ?? 'Guest User' }} ({{ $question->user?->email ?? 'N/A' }})
Madhab: {{ $question->madhab?->name == 'Shafii' ? "Shafi'i" : ($question->madhab?->name ?? 'General/Other') }}

--------------------------------------------------
Question Content:
--------------------------------------------------
@emailPlainText($question->body)

--------------------------------------------------
@php
    $livePortalUrl = 'https://daruliftakauzariyya.com';
    $appUrl = config('app.url');
    $portalUrl = (empty($appUrl) || str_contains($appUrl, '127.0.0.1') || str_contains($appUrl, 'localhost')) ? $livePortalUrl : rtrim($appUrl, '/');
@endphp
View in Admin Panel: {{ $portalUrl }}/admin/questions/{{ $question->id }}

© {{ date('Y') }} Darul Ifta Kauzariyya. All rights reserved.
{{ $portalUrl }}

