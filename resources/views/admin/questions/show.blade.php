@extends('layouts.admin')
@section('title', 'Question ' . $question->question_id)
@section('topbar-title', 'Question Details')
@section('content')
<div class="row g-4">
    {{-- Left: Question detail --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-secondary me-2">{{ $question->question_id }}</span>
                    <span class="badge badge-status-{{ $question->status }} me-2">{{ ucfirst(str_replace('_',' ',$question->status)) }}</span>
                    <span class="badge bg-warning text-dark px-2 border-0"><i class="bi bi-hourglass-split me-1"></i>{{ floor($question->created_at->diffInDays(now())) }} Days Ago</span>
                </div>
                <div class="d-flex gap-2 no-print">
                    <a href="{{ route('admin.questions.history', $question) }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-clock-history me-1"></i>History
                    </a>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-dark"><i class="bi bi-printer me-1"></i>Print</button>
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
                    @if(in_array($question->status, ['pending']))
                    <a href="{{ route('admin.questions.accept.form', $question) }}" class="btn btn-sm btn-primary">Accept</a>
                    @endif
                </div>
            </div>
            @php
                $langClass = '';
                if($question->language == 'Urdu') $langClass = 'gulzar-font';
                elseif($question->language == 'Arabic') $langClass = 'alyamama-font';
                elseif($question->language == 'Malayalam') $langClass = 'noto-serif-malayalam-font';
                else $langClass = '';
            @endphp
            <div class="card-body">
                <div><small class="text-muted fw-bold">Question Summary (Short Question):</small></div>
                @if($question->title)<h5 class="fw-bold {{ $langClass }}">{{ $question->title }}</h5>@endif
                <div class="mb-3"><span class="badge" style="background:#1a7a4a;">{{ $question->madhab?->name ?? '—' }}</span></div>
                <div class="{{ $langClass }}" style="line-height:1.8;">{!! $question->body !!}</div>
                <hr>
                <div class="row g-2 small text-muted">
                    <div class="col-md-4"><i class="bi bi-person me-1"></i>{{ $question->user->name }}</div>
                    <div class="col-md-4"><i class="bi bi-envelope me-1"></i>{{ $question->user->email }}</div>
                    <div class="col-md-4"><i class="bi bi-calendar me-1"></i>{{ $question->created_at->format('d M Y H:i') }}</div>
                </div>
            </div>
        </div>

        {{-- Mufti Answer (if exists) --}}
        @if($question->answer || $question->draft_answer)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-chat-text me-2"></i>Mufti Answer</h6>
                <div class="d-flex gap-2">
                    <span class="badge badge-status-{{ $question->status }} me-2">{{ ucfirst(str_replace('_',' ',$question->status)) }}</span>
                    @if(in_array($question->status, ['published', 'answered']))
                    <a href="{{ route('admin.questions.answer', $question) }}" class="btn btn-sm btn-outline-primary py-0">
                        <i class="bi bi-pencil me-1"></i>Edit Answer
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <p class="fw-semibold">Summary / Title: {!! $question->title !!}</p>
                <div class="mufti-answer-body" style="line-height:1.8; color: #333;">
                    {!! $question->draft_answer ?? $question->answer !!}
                </div>

                {{-- Supporting References --}}
                @php
                    $refs = $question->references->isNotEmpty() ? $question->references : $question->draftReferences;
                @endphp
                @if($refs->count())
                <div class="mt-4 p-3 bg-light rounded border">
                    <h6 class="fw-bold small text-muted text-uppercase mb-3"><i class="bi bi-journal-text me-1"></i>Supporting References</h6>
                    @foreach($refs as $ref)
                        <div class="mb-4 last-child-mb-0">
                            <div class="arabic-text mb-2 text-dark {{ $ref->language == 'Urdu' ? 'gulzar-font' : 'alyamama-font' }}" style="font-size: 1.15rem; line-height: 1.6;">
                                {{ $ref->arabic_text }}
                            </div>
                            <div class="small text-muted d-flex align-items-center">
                                <i class="bi bi-book me-2"></i>
                                <span class="fw-semibold">{{ $ref->book_name }}</span>
                                <span class="mx-2 opacity-50">|</span>
                                <span>{{ $ref->language }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
                @if(in_array($question->status, ['answered', 'for_review']))
                <div class="mt-3">
                    <a href="{{ route('admin.questions.verify', $question) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-shield-check me-1"></i>Verify & Publish
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Right: Actions --}}
    <div class="col-lg-4 no-print">
        {{-- Assign Mufti --}}
        @if(in_array($question->status, ['accepted','assigned']))
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-2"><h6 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i>Assign to Mufti</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.questions.assign', $question) }}">
                    @csrf
                    <select name="mufti_id" class="form-select form-select-sm mb-2" required>
                        <option value="">Select Mufti...</option>
                        @foreach($muftis as $m)
                            <option value="{{ $m->id }}" {{ $question->assigned_mufti_id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary btn-sm w-100">Assign</button>
                </form>
            </div>
        </div>
        @endif

        {{-- Admin write answer --}}
        @if(in_array($question->status, ['accepted','assigned']))
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body text-center">
                <p class="small text-muted mb-2">Or write the answer yourself (on behalf of a Mufti)</p>
                <a href="{{ route('admin.questions.answer', $question) }}" class="btn btn-outline-success btn-sm w-100">
                    <i class="bi bi-pencil me-1"></i>Write Answer
                </a>
            </div>
        </div>
        @endif

        {{-- Questioner Profile Sidebar Card --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-person-circle me-2 text-green"></i>Questioner Details
                </h6>
                <a href="{{ route('admin.questioners.show', $question->user) }}" class="btn btn-link btn-sm p-0 text-decoration-none small" style="color: #1a7a4a;">View Profile</a>
            </div>
            <div class="card-body small">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $question->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($question->user->name).'&background=1a7a4a&color=fff&size=80' }}"
                         class="rounded-circle border me-2"
                         width="40" height="40">
                    <div>
                        <div class="fw-bold text-dark">{{ $question->user->name }}</div>
                        <span class="badge {{ $question->user->profile_completed ? 'bg-success' : 'bg-warning text-dark' }} py-1" style="font-size: 0.65rem;">
                            {{ $question->user->profile_completed ? 'Profile Completed' : 'Profile Incomplete' }}
                        </span>
                    </div>
                </div>
                
                <p class="mb-1"><strong>Email:</strong> <a href="mailto:{{ $question->user->email }}" class="text-decoration-none">{{ $question->user->email }}</a></p>
                <p class="mb-1"><strong>Mobile:</strong> <a href="tel:{{ $question->user->mobile }}" class="text-decoration-none">{{ $question->user->country_code }}{{ $question->user->mobile }}</a></p>
                <p class="mb-1"><strong>Gender:</strong> {{ $question->user->gender ?? 'Not set' }}</p>
                <p class="mb-1"><strong>Madhab Pref:</strong> {{ $question->user->madhab?->name ?? 'None' }}</p>
                <hr class="my-2">
                <p class="mb-1"><strong>Address:</strong></p>
                <p class="text-muted mb-0 lh-sm">
                    @if($question->user->address_line_1)
                        {{ $question->user->address_line_1 }}<br>
                        @if($question->user->address_line_2) {{ $question->user->address_line_2 }}<br> @endif
                        {{ $question->user->district ?? '' }}{{ $question->user->state ? ', ' . $question->user->state : '' }}<br>
                        {{ $question->user->country ?? '' }}
                    @else
                        <em>Address details skipped.</em>
                    @endif
                </p>
            </div>
        </div>

        {{-- Question detail info --}}
        {{-- Question detail info --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body small">
                <p class="mb-1"><strong>Language:</strong> {{ $question->language ?? 'Not set' }}</p>
                <p class="mb-1"><strong>Category:</strong> {{ $question->category?->name ?? '—' }}</p>
                <p class="mb-0"><strong>Accepted:</strong> {{ $question->accepted_at?->format('d M Y') ?? '—' }}</p>
            </div>
        </div>

        {{-- Likes & Engagement Info --}}
        @if($question->fatwa)
        <div class="card border-0 shadow-sm mb-3" style="border-left: 4px solid #dc3545 !important; border-left-style: solid !important;">
            <div class="card-header bg-white border-0 py-2">
                <h6 class="mb-0 fw-bold"><i class="bi bi-heart-fill text-danger me-2"></i>Likes & Engagement</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-center flex-fill">
                        <div class="h4 fw-bold text-danger mb-0">{{ number_format($question->fatwa->likes) }}</div>
                        <div class="x-small text-muted text-uppercase fw-bold">Total Likes</div>
                    </div>
                    <div class="vr"></div>
                    <div class="text-center flex-fill">
                        <div class="h4 fw-bold text-primary mb-0">{{ number_format($question->fatwa->views) }}</div>
                        <div class="x-small text-muted text-uppercase fw-bold">Total Views</div>
                    </div>
                </div>
                <div class="border-top pt-2">
                    <div class="x-small text-muted mb-1 fw-bold">Recent Likes by IP:</div>
                    @php
                        $recentLikes = $question->fatwa->fatwaLikes()->latest()->take(5)->get();
                    @endphp
                    @forelse($recentLikes as $like)
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                        <span class="x-small text-monospace"><i class="bi bi-globe2 me-1 text-muted"></i>{{ $like->ip_address }}</span>
                        <span class="x-small text-muted">{{ $like->created_at->diffForHumans() }}</span>
                    </div>
                    @empty
                    <p class="x-small text-muted mb-0">No likes recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- Fatwa Log History --}}
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Fatwa Log History</h6>
                <span class="badge bg-light text-primary rounded-pill">{{ $question->activityLogs->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="timeline-container p-3" style="max-height: 500px; overflow-y: auto;">
                    <ul class="list-unstyled mb-0">
                        @forelse($question->activityLogs as $log)
                        <li class="position-relative pb-4 ps-4">
                            @if(!$loop->last)
                            <div class="position-absolute h-100 border-start border-2 border-light" style="left: 0.45rem; top: 1rem;"></div>
                            @endif
                            <div class="position-absolute rounded-circle bg-{{ $log->color }} d-flex align-items-center justify-content-center shadow-sm"
                                 style="left: 0; width: 1rem; height: 1rem; top: 0.15rem; z-index: 2;">
                                <i class="bi {{ $log->icon }} text-white" style="font-size: 0.5rem;"></i>
                            </div>
                            <div class="small">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">{{ $log->event_label }}</span>
                                    <span class="text-muted x-small ms-2">{{ $log->event_at?->format('M d, H:i') }}</span>
                                </div>
                                <p class="text-muted mb-1 x-small line-height-sm mt-1">
                                    {{ $log->notes ? Str::limit($log->notes, 120) : 'Process event recorded.' }}
                                </p>
                                <div class="x-small text-secondary opacity-75">
                                    <i class="bi bi-person me-1"></i>{{ $log->actor->name ?? 'System' }}
                                </div>
                            </div>
                        </li>
                        @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-journal-x d-block fs-3 mb-2 opacity-25"></i>
                            No activity logs found.
                        </div>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
