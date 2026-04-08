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
                <span class="badge" style="background:#1a7a4a;">{{ $question->madhab == 'Shafii' ? "Shafi'i" : $question->madhab }}</span>
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
                        <div class="row g-2">
                            @foreach($question->files as $file)
                            <div class="col-md-6">
                                <a href="{{ route('files.download', $file) }}" class="d-flex align-items-center p-2 bg-white rounded border text-decoration-none text-dark hover-shadow-sm transition-all">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                        <i class="bi bi-file-earmark-arrow-down text-primary fs-5"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="small fw-bold text-truncate">{{ $file->original_name }}</div>
                                        <div class="x-small text-muted">{{ number_format($file->file_size / 1024, 1) }} KB</div>
                                    </div>
                                </a>
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
        <div class="card border-0 shadow-sm">
            <div class="card-body small">
                <p class="fw-semibold mb-1">Submitted by:</p>
                <p class="mb-2">{{ $question->user->name }}<br><span class="text-muted">{{ $question->user->country }}</span></p>
                <p class="fw-semibold mb-1">Date:</p>
                <p class="mb-2">{{ $question->created_at->format('d M Y') }}</p>
                <p class="fw-semibold mb-1">Language:</p>
                <p class="mb-0">{{ $question->language ?? 'Not specified' }}</p>
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
