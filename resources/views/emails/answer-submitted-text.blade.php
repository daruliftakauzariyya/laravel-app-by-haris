Mufti Answer Submitted for Review – {{ $answer->question_id ?? ($answer->question->question_id ?? '') }}
==================================================

A Mufti has submitted an answer for review.

Question ID: {{ $answer->question_id ?? ($answer->question->question_id ?? '') }}
Mufti: {{ $answer->assignedMufti?->name ?? ($answer->mufti?->name ?? 'Scholar') }}
@if(!empty($answer->title))
Title: {{ $answer->title }}
@endif

@php
    $answerContent = $answer->answer ?? ($answer->summary ?? '');
@endphp
@if(!empty($answerContent))
--------------------------------------------------
Answer Summary:
--------------------------------------------------
@emailPlainText($answerContent)

--------------------------------------------------
@endif
@php
    $livePortalUrl = 'https://daruliftakauzariyya.com';
    $appUrl = config('app.url');
    $portalUrl = (empty($appUrl) || str_contains($appUrl, '127.0.0.1') || str_contains($appUrl, 'localhost')) ? $livePortalUrl : rtrim($appUrl, '/');
@endphp
Verify & Publish: {{ $portalUrl }}/admin/questions/{{ $answer->id ?? $answer->question?->id }}/verify

Darul Ifta Kauzariyya
{{ $portalUrl }}

