Question Assigned to You – {{ $question->question_id }}
==================================================

Dear {{ $question->assignedMufti?->name ?? 'Scholar' }},

A question has been assigned to you for Fatwa.

Question ID: {{ $question->question_id }}
@if(!empty($question->title))
Title: {{ $question->title }}
@endif
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
Submit Answer: {{ $portalUrl }}/mufti/questions/{{ $question->id }}

© {{ date('Y') }} Darul Ifta Kauzariyya. All rights reserved.
{{ $portalUrl }}

