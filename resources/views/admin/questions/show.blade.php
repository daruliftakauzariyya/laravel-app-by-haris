@extends('layouts.admin')
@section('title', 'Question ' . $question->question_id)
@section('topbar-title', 'Question Details')
@section('content')
<div class="row g-4">
    {{-- Left: Question detail --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <span class="badge bg-secondary me-2">{{ $question->question_id }}</span>
                    <span class="badge badge-status-{{ $question->status }} me-2">{{ ucfirst(str_replace('_',' ',$question->status)) }}</span>
                    @if($question->status === 'published')
                        @if($question->is_public)
                            <span class="badge bg-success me-2" title="Visible to everyone in public archive & search"><i class="bi bi-globe2 me-1"></i>Public Archive</span>
                        @else
                            <span class="badge bg-warning text-dark me-2" title="Hidden from public archive. Accessible via shared link with login."><i class="bi bi-shield-lock-fill me-1"></i>Private (Shared Link Only)</span>
                        @endif
                    @endif
                    <span class="badge bg-warning text-dark px-2 border-0"><i class="bi bi-hourglass-split me-1"></i>{{ floor($question->created_at->diffInDays(now())) }} Days Ago</span>
                </div>
                <div class="d-flex gap-2 no-print flex-wrap align-items-center">
                    {{-- Copy Public Short URL Button --}}
                    <button type="button" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1" id="copyShortUrlBtn"
                        onclick="copyAdminShortUrl('{{ route('fatwa.short', $question->id) }}')" 
                        title="Copy Short URL: {{ route('fatwa.short', $question->id) }}">
                        <i class="bi bi-link-45deg fs-6" id="copyShortUrlIcon"></i>
                        <span id="copyShortUrlText">Copy Link</span>
                    </button>

                    @if($question->status === 'published')
                    <a href="{{ route('fatwa.show', $question) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" title="Open Fatwa Page">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>View Fatwa</span>
                    </a>
                    @endif

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
        {{-- Public & Short Link Access Card --}}
        <div class="card border-0 shadow-sm mb-3" style="border-left: 4px solid {{ $question->is_public ? '#10b981' : '#f59e0b' }} !important;">
            <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold {{ $question->is_public ? 'text-success' : 'text-warning' }}">
                    <i class="bi bi-link-45deg me-1"></i>Fatwa Access & URL
                </h6>
                @if($question->status === 'published')
                    @if($question->is_public)
                        <span class="badge bg-success"><i class="bi bi-globe2 me-1"></i>Public</span>
                    @else
                        <span class="badge bg-warning text-dark"><i class="bi bi-shield-lock-fill me-1"></i>Private Link</span>
                    @endif
                @else
                    <span class="badge bg-secondary">{{ ucfirst($question->status) }}</span>
                @endif
            </div>
            <div class="card-body small">
                @if($question->status === 'published' && !$question->is_public)
                    <div class="alert alert-warning py-1.5 px-2 mb-2 border-0 small" style="font-size: 0.75rem;">
                        <i class="bi bi-info-circle me-1"></i><strong>Private Fatwa:</strong> Hidden from public archive. Anyone visiting this link must be logged in to read it.
                    </div>
                @endif

                <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.75rem;">Fatwa Direct Link:</label>
                <div class="input-group input-group-sm mb-2">
                    <input type="text" class="form-control bg-light font-monospace" style="font-size: 0.8rem;" value="{{ route('fatwa.short', $question->id) }}" id="sidebarShortUrlInput" readonly onclick="this.select()">
                    <button class="btn btn-success" type="button" onclick="copySidebarShortUrl()">
                        <i class="bi bi-clipboard me-1" id="sidebarCopyIcon"></i><span id="sidebarCopyText">Copy</span>
                    </button>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-1">
                    <a href="{{ route('fatwa.short', $question->id) }}" target="_blank" class="text-decoration-none small text-primary fw-medium">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Test Short Link
                    </a>
                    @if($question->status === 'published')
                    <a href="{{ route('fatwa.show', $question) }}" target="_blank" class="text-decoration-none small text-success fw-medium">
                        <i class="bi bi-eye me-1"></i>Open Full Page
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Visibility & Publishing Controls (For Published Fatwas) --}}
        @if($question->status === 'published')
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-2">
                <h6 class="mb-0 fw-bold"><i class="bi bi-sliders me-1"></i>Visibility & Publishing</h6>
            </div>
            <div class="card-body small">
                {{-- Toggle Public / Private --}}
                <form method="POST" action="{{ route('admin.questions.toggle-public', $question) }}" class="mb-2">
                    @csrf
                    @if($question->is_public)
                        <button type="submit" class="btn btn-sm btn-outline-warning w-100 text-start d-flex align-items-center justify-content-between"
                                onclick="return confirm('Make this fatwa PRIVATE? It will be hidden from the public archive and search, but accessible via shared link (requires login).')">
                            <span><i class="bi bi-eye-slash me-2"></i>Hide from Public Archive</span>
                            <span class="badge bg-warning text-dark">Make Private</span>
                        </button>
                    @else
                        <button type="submit" class="btn btn-sm btn-outline-success w-100 text-start d-flex align-items-center justify-content-between"
                                onclick="return confirm('Make this fatwa PUBLIC? It will appear in public archives, searches, and the home page.')">
                            <span><i class="bi bi-globe2 me-2"></i>Publish to Public Archive</span>
                            <span class="badge bg-success">Make Public</span>
                        </button>
                    @endif
                </form>

                {{-- Unpublish Entirely --}}
                <form method="POST" action="{{ route('admin.questions.unpublish', $question) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100 text-start d-flex align-items-center justify-content-between"
                            onclick="return confirm('Are you sure you want to UNPUBLISH this fatwa? Its status will revert to Answered / Draft and it will no longer be accessible as published.')">
                        <span><i class="bi bi-arrow-counterclockwise me-2"></i>Unpublish Fatwa</span>
                        <span class="badge bg-danger">Revert</span>
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Assign Mufti --}}
        @if(in_array($question->status, ['accepted','assigned']))
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-2"><h6 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i>Assign to Mufti</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.questions.assign', $question) }}">
                    @csrf
                    <select name="mufti_id" class="form-select form-select-sm mb-2" required>
                        <option value="">Select Mufti...</option>
                        @forelse($muftis as $m)
                            @php
                                $isMatch = $question->madhab_id && $m->madhab_id === $question->madhab_id;
                                $madhabSuffix = $m->madhab ? ' (' . $m->madhab->name . ($isMatch ? ' — Recommended' : '') . ')' : '';
                            @endphp
                            <option value="{{ $m->id }}" {{ $question->assigned_mufti_id == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}{{ $madhabSuffix }}
                            </option>
                        @empty
                            <option value="" disabled>No active Muftis registered</option>
                        @endforelse
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
        @php
            $displayLogs = isset($logs) && $logs->isNotEmpty() ? $logs->sortByDesc(fn($l) => $l->event_at ?? $l->created_at) : \App\Models\FatwaActivityLog::getComprehensiveTimeline($question)->sortByDesc(fn($l) => $l->event_at ?? $l->created_at);
        @endphp
        <div class="card border-0 shadow-sm overflow-hidden mb-4">
            <div class="card-header bg-white border-0 py-2.5 d-flex justify-content-between align-items-center">
                <div class="d-flex items-center gap-2">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-1 text-primary"></i>Fatwa Log History</h6>
                    <span class="badge bg-light text-primary border rounded-pill">{{ $displayLogs->count() }}</span>
                </div>
                <a href="{{ route('admin.questions.history', $question) }}" class="btn btn-sm btn-outline-primary py-0 px-2 text-decoration-none d-inline-flex align-items-center" style="font-size: 0.75rem;">
                    <i class="bi bi-diagram-3 me-1"></i>Full Timeline
                </a>
            </div>
            <div class="card-body p-0">
                <div class="timeline-container p-3" style="max-height: 520px; overflow-y: auto;">
                    <ul class="list-unstyled mb-0">
                        @forelse($displayLogs as $log)
                        <li class="position-relative pb-3 ps-4 border-bottom border-light mb-2">
                            @if(!$loop->last)
                            <div class="position-absolute h-100 border-start border-2 border-light" style="left: 0.45rem; top: 1.1rem;"></div>
                            @endif
                            <div class="position-absolute rounded-circle bg-{{ $log->color }} d-flex align-items-center justify-content-center shadow-sm"
                                 style="left: 0; width: 1.1rem; height: 1.1rem; top: 0.15rem; z-index: 2;">
                                <i class="bi {{ $log->icon }}" style="font-size: 0.55rem; color: #fff;"></i>
                            </div>
                            <div class="small">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                                    <div class="fw-bold text-dark d-flex align-items-center gap-1.5">
                                        <span class="badge bg-light text-secondary border px-1.5 py-0.5 rounded text-[10px] fw-bold">#{{ $log->reverse_step_number ?? $loop->iteration }}</span>
                                        <span>{{ $log->event_label }}</span>
                                    </div>
                                    <span class="text-muted x-small ms-auto font-monospace">{{ $log->event_at?->format('M d, H:i') }}</span>
                                </div>
                                <p class="text-muted mb-1 x-small line-height-sm mt-1 bg-light p-1.5 rounded border border-light">
                                    {{ $log->notes ? Str::limit($log->notes, 160) : 'Process event recorded.' }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center x-small text-secondary opacity-75">
                                    <div>
                                        <i class="bi bi-person me-1"></i>{{ $log->actor->name ?? ($question->user?->name ?? 'System') }}
                                    </div>
                                    <div>
                                        {{ $log->event_at?->diffForHumans() }}
                                    </div>
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

@push('scripts')
<script>
    function copyAdminShortUrl(url) {
        navigator.clipboard.writeText(url).then(() => {
            const btnText = document.getElementById('copyShortUrlText');
            const btnIcon = document.getElementById('copyShortUrlIcon');
            const btn = document.getElementById('copyShortUrlBtn');
            if (btnText && btnIcon) {
                btnText.innerText = 'Copied!';
                btnIcon.className = 'bi bi-check2-circle';
                btn.classList.remove('btn-outline-success');
                btn.classList.add('btn-success', 'text-white');
                setTimeout(() => {
                    btnText.innerText = 'Copy Short URL';
                    btnIcon.className = 'bi bi-link-45deg fs-6';
                    btn.classList.add('btn-outline-success');
                    btn.classList.remove('btn-success', 'text-white');
                }, 2000);
            }
        }).catch(() => {
            prompt('Public Short URL:', url);
        });
    }

    function copySidebarShortUrl() {
        const input = document.getElementById('sidebarShortUrlInput');
        if (!input) return;
        navigator.clipboard.writeText(input.value).then(() => {
            const text = document.getElementById('sidebarCopyText');
            const icon = document.getElementById('sidebarCopyIcon');
            if (text && icon) {
                text.innerText = 'Copied!';
                icon.className = 'bi bi-check2-circle';
                setTimeout(() => {
                    text.innerText = 'Copy';
                    icon.className = 'bi bi-clipboard me-1';
                }, 2000);
            }
        }).catch(() => {
            input.select();
            document.execCommand('copy');
        });
    }
</script>
@endpush

@endsection
