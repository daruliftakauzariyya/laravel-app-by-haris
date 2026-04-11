@extends('layouts.mufti')
@section('title', 'Submit Answer')
@section('topbar-title', 'Submit Fatwa Answer')
@section('content')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush
<div class="row g-4">
    <div class="col-lg-8">
        {{-- Question Preview --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-2 d-flex justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-question-circle me-2"></i>Question — {{ $question->question_id }}</h6>
                <span class="badge" style="background:#1a7a4a;">{{ $question->madhab?->name ?? 'General' }}</span>
            </div>
            <div class="card-body">
                @if($question->title)<h6 class="fw-bold mb-3">{{ strip_tags($question->title) }}</h6>@endif
                <div class="mb-4" style="line-height:1.8;">{!! $question->body !!}</div>
                
                {{-- Attachments Section for Mufti --}}
                @if($question->files->count())
                    <div class="mt-4 p-3 bg-light rounded-3 border">
                        <h6 class="fw-bold mb-3 small text-muted text-uppercase tracking-wider">
                            <i class="bi bi-paperclip me-1"></i>User Provided Attachments
                        </h6>
                        <div class="row g-3">
                            @foreach($question->files as $file)
                            <div class="col-md-4 col-lg-3 col-6">
                                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                    @if(str_starts_with($file->file_type, 'image/'))
                                        <a href="{{ route('files.view', $file) }}" target="_blank" class="d-block">
                                            <div style="height: 120px; background: #eee; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                <img src="{{ route('files.view', $file) }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Attachment">
                                            </div>
                                        </a>
                                    @elseif($file->file_type == 'application/pdf')
                                        <a href="{{ route('files.view', $file) }}" target="_blank" class="d-block text-decoration-none">
                                            <div style="height: 120px; background: #fdf2f2; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                                <i class="bi bi-file-earmark-pdf text-danger fs-1"></i>
                                                <span class="x-small text-danger fw-bold mt-1">VIEW PDF</span>
                                            </div>
                                        </a>
                                    @else
                                        <div style="height: 120px; background: #f8fafc; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                            <i class="bi bi-file-earmark-text text-secondary fs-1"></i>
                                            <span class="x-small text-muted mt-1 uppercase">{{ explode('/', $file->file_type)[1] ?? 'FILE' }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="card-footer p-2 bg-white d-flex justify-content-between align-items-center">
                                        <div class="overflow-hidden pe-2">
                                            <div class="x-small fw-bold text-truncate" title="{{ $file->original_name }}">{{ $file->original_name }}</div>
                                            <div class="x-small text-muted">{{ number_format($file->file_size / 1024, 1) }} KB</div>
                                        </div>
                                        <a href="{{ route('files.download', $file) }}" class="btn btn-link btn-sm p-0 text-primary" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Answer Form --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-2">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pencil me-2"></i>Write Your Fatwa Answer</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('mufti.questions.answer', $question) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Short Question Summary <span class="text-danger">*</span></label>
                        <input type="text" name="summary" class="form-control @error('summary') is-invalid @enderror"
                               value="{{ old('summary', $question->muftiAnswer?->summary) }}"
                               placeholder="One-line summary of the question" required>
                        @error('summary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Full Answer <span class="text-danger">*</span></label>
                        <textarea name="full_answer" rows="10" class="form-control @error('full_answer') is-invalid @enderror"
                                  placeholder="Write your complete fatwa answer here..." required>{{ old('full_answer', $question->muftiAnswer?->full_answer) }}</textarea>
                        @error('full_answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Supporting References --}}
                    <h6 class="fw-bold mb-3"><i class="bi bi-book me-2"></i>Supporting Islamic References</h6>
                    <div id="references-container">
                        @if($question->muftiAnswer && $question->muftiAnswer->supportingReferences->count())
                            @foreach($question->muftiAnswer->supportingReferences as $ref)
                            <div class="reference-row card border-0 bg-light p-3 mb-3">
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold">Arabic Text</label>
                                    <textarea name="arabic_text[]" rows="3" class="form-control arabic-text">{{ $ref->arabic_text }}</textarea>
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Book Reference</label>
                                        <input type="text" name="book_name[]" class="form-control" value="{{ $ref->book_name }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Language</label>
                                        <select name="ref_language[]" class="form-select">
                                            <option value="Arabic" {{ $ref->language == 'Arabic' ? 'selected' : '' }}>Arabic</option>
                                            <option value="Urdu" {{ $ref->language == 'Urdu' ? 'selected' : '' }}>Urdu</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-ref w-100">✕</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <div class="reference-row card border-0 bg-light p-3 mb-3">
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Arabic Text</label>
                                <textarea name="arabic_text[]" rows="3" class="form-control arabic-text" placeholder="اكتب النص العربي هنا..."></textarea>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Book Reference</label>
                                    <input type="text" name="book_name[]" class="form-control" placeholder="e.g. Sahih al-Bukhari">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Language</label>
                                    <select name="ref_language[]" class="form-select">
                                        <option value="Arabic">Arabic</option>
                                        <option value="Urdu">Urdu</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-ref w-100">✕</button>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <button type="button" id="add-reference" class="btn btn-outline-secondary btn-sm mb-4">
                        <i class="bi bi-plus-circle me-1"></i>Add Reference
                    </button>
                    <br>
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="bi bi-send me-2"></i>Submit for Admin Review
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-2">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Question Info</h6>
            </div>
            <div class="card-body small">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Submitted by:</span>
                    <span class="fw-bold">{{ $question->user->name }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Country:</span>
                    <span class="fw-bold">{{ $question->user->country }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Date:</span>
                    <span class="fw-bold">{{ $question->created_at->format('d M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Age:</span>
                    <span class="badge bg-warning text-dark px-2">{{ floor($question->created_at->diffInDays(now())) }} Days Ago</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Language:</span>
                    <span class="fw-bold text-success">{{ $question->language ?? 'Not specified' }}</span>
                </div>
            </div>
        </div>

        {{-- Fatwa Log History --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Fatwa Log History</h6>
                <span class="badge bg-light text-primary rounded-pill">{{ $question->activityLogs->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="timeline-container p-3" style="max-height: 450px; overflow-y: auto;">
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
                                    <span class="text-muted x-small">{{ $log->event_at->format('M d, H:i') }}</span>
                                </div>
                                <p class="text-muted mb-1 x-small line-height-sm">
                                    {{ $log->notes ? Str::limit($log->notes, 80) : 'Process event recorded.' }}
                                </p>
                                <div class="x-small text-secondary opacity-75">
                                    <i class="bi bi-person me-1"></i>{{ $log->actor->name ?? 'System' }}
                                </div>
                            </div>
                        </li>
                        @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-journal-x d-block fs-3 mb-2 opacity-25"></i>
                            No activity logs found for this query.
                        </div>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
$(document).ready(function() {
    $('textarea[name="full_answer"]').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
});
document.getElementById('add-reference').addEventListener('click', function() {
    const container = document.getElementById('references-container');
    const first = container.querySelector('.reference-row');
    const clone = first.cloneNode(true);
    clone.querySelectorAll('textarea, input').forEach(el => el.value = '');
    container.appendChild(clone);
});
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-ref')) {
        const rows = document.querySelectorAll('.reference-row');
        if (rows.length > 1) e.target.closest('.reference-row').remove();
    }
});
</script>
@endpush
@endsection
