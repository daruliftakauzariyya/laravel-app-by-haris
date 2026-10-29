Your Question Has Been Answered – {{ $fatwa->question_id }}
==================================================

Assalamu Alaikum {{ $fatwa->user?->name ?? 'Brother/Sister' }},

The answer to your question (ID: {{ $fatwa->question_id }}) has been verified and published.

@if(!empty($fatwa->title))
Title: {{ $fatwa->title }}
@endif

@php
    $livePortalUrl = 'https://daruliftakauzariyya.com';
    $appUrl = config('app.url');
    $portalUrl = (empty($appUrl) || str_contains($appUrl, '127.0.0.1') || str_contains($appUrl, 'localhost')) ? $livePortalUrl : rtrim($appUrl, '/');
@endphp
View Full Fatwa: {{ $portalUrl }}/fatwa-archive/{{ $fatwa->id }}

Jazakumullahu Khayran for trusting Darul Ifta Kauzariyya.

© {{ date('Y') }} Darul Ifta Kauzariyya. All rights reserved.
{{ $portalUrl }}

