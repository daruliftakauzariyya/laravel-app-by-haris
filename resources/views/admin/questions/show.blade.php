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
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#acceptModal">Accept</button>
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

        {{-- Files --}}
        @if($question->files->count())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-2"><h6 class="mb-0 fw-bold"><i class="bi bi-paperclip me-2"></i>Attached Files</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($question->files as $file)
                    <div class="col-md-4 col-lg-3 col-6">
                        <div class="card h-100 border shadow-none overflow-hidden" style="border-radius: 12px;">
                            @if(str_starts_with($file->file_type, 'image/'))
                                <a href="{{ route('files.view', $file) }}" target="_blank" class="d-block">
                                    <div style="height: 100px; background: #eee; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        <img src="{{ route('files.view', $file) }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Attachment">
                                    </div>
                                </a>
                            @elseif($file->file_type == 'application/pdf')
                                <a href="{{ route('files.view', $file) }}" target="_blank" class="d-block text-decoration-none">
                                    <div style="height: 100px; background: #fdf2f2; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                        <i class="bi bi-file-earmark-pdf text-danger fs-2"></i>
                                        <span class="x-small text-danger fw-bold mt-1">VIEW</span>
                                    </div>
                                </a>
                            @else
                                <div style="height: 100px; background: #f8fafc; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <i class="bi bi-file-earmark-text text-secondary fs-2"></i>
                                    <span class="x-small text-muted mt-1 uppercase">{{ explode('/', $file->file_type)[1] ?? 'FILE' }}</span>
                                </div>
                            @endif
                            
                            <div class="card-footer p-2 bg-white d-flex justify-content-between align-items-center border-top-0">
                                <div class="overflow-hidden pe-1">
                                    <div style="font-size: 0.65rem;" class="fw-bold text-truncate" title="{{ $file->original_name }}">{{ $file->original_name }}</div>
                                </div>
                                <a href="{{ route('files.download', $file) }}" class="btn btn-link btn-sm p-0 text-primary" title="Download">
                                    <i class="bi bi-download" style="font-size: 0.8rem;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Mufti Answer (if exists) --}}
        @if($question->muftiAnswer)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-chat-text me-2"></i>Mufti Answer</h6>
                <div class="d-flex gap-2">
                    <span class="badge badge-status-{{ $question->muftiAnswer->status }} me-2">{{ ucfirst(str_replace('_',' ',$question->muftiAnswer->status)) }}</span>
                    @if(in_array($question->status, ['published', 'answered']))
                    <a href="{{ route('admin.questions.answer', $question) }}" class="btn btn-sm btn-outline-primary py-0">
                        <i class="bi bi-pencil me-1"></i>Edit Answer
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <p class="fw-semibold">Summary: {!! $question->muftiAnswer->summary !!}</p>
                <div class="mufti-answer-body" style="line-height:1.8; color: #333;">
                    {!! $question->muftiAnswer->full_answer !!}
                </div>

                {{-- Supporting References --}}
                @if($question->muftiAnswer->supportingReferences->count())
                <div class="mt-4 p-3 bg-light rounded border">
                    <h6 class="fw-bold small text-muted text-uppercase mb-3"><i class="bi bi-journal-text me-1"></i>Supporting References</h6>
                    @foreach($question->muftiAnswer->supportingReferences as $ref)
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

{{-- Accept Modal --}}
<div class="modal fade" id="acceptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Accept Question</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('admin.questions.accept', $question) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question Summary (Short Question) <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ $question->title }}" maxlength="200" required>
                        <small class="text-muted">A clear, brief summary (max 200 chars) for archive headings.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Language</label>
                        <select name="language" class="form-select" required>
                            <option value="Urdu" {{ $question->language == 'Urdu' || $question->user->mother_language == 'Urdu' ? 'selected' : '' }}>Urdu</option>
                            <option value="Arabic" {{ $question->language == 'Arabic' || $question->user->mother_language == 'Arabic' ? 'selected' : '' }}>Arabic</option>
                            <option value="English" {{ $question->language == 'English' || $question->user->mother_language == 'English' ? 'selected' : '' }}>English</option>
                            <option value="Hindi" {{ $question->language == 'Hindi' || $question->user->mother_language == 'Hindi' ? 'selected' : '' }}>Hindi</option>
                            <option value="Malayalam" {{ $question->language == 'Malayalam' || $question->user->mother_language == 'Malayalam' ? 'selected' : '' }}>Malayalam</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Main Category <span class="text-danger">*</span></label>
                        <select id="main_category" class="form-select" required>
                            <option value="">Select Main Category...</option>
                            @foreach($mainCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="sub_category_container" style="display:none;">
                        <label class="form-label fw-semibold">Sub Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="sub_category" class="form-select" required>
                            <option value="">Select Sub Category...</option>
                            @foreach($subCategories as $sub)
                                <option value="{{ $sub->id }}" data-parent="{{ $sub->parent_id }}">{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Accept Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Store all original subcategory options in a safe place
const allSubOptions = Array.from(document.querySelectorAll('#sub_category option[data-parent]'));

document.getElementById('main_category').addEventListener('change', function() {
    const parentId = this.value;
    const subContainer = document.getElementById('sub_category_container');
    const subSelect = document.getElementById('sub_category');

    // Create a fresh list of options starting with the placeholder
    subSelect.innerHTML = '<option value="">Select Sub Category...</option>';

    if (parentId) {
        // Find matching options from our stored list
        const matches = allSubOptions.filter(opt => opt.getAttribute('data-parent') === parentId);

        if (matches.length > 0) {
            subContainer.style.display = 'block';
            matches.forEach(opt => {
                // Re-append the matching option a clone to keep the data
                subSelect.appendChild(opt.cloneNode(true));
            });
        } else {
            // Show a "No subcategories" message if nothing matches
            const emptyOpt = document.createElement('option');
            emptyOpt.text = "No subcategories found for this section";
            emptyOpt.disabled = true;
            subSelect.appendChild(emptyOpt);
            subContainer.style.display = 'block';
        }
    } else {
        // Hide if no main category selected
        subContainer.style.display = 'none';
    }
    
    subSelect.value = ''; // Reset selection
});
</script>
@endsection
