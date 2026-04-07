@extends('layouts.admin')
@section('title', 'Write Answer')
@section('topbar-title', 'Write Answer on Behalf of Mufti')
@section('content')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush
<div class="card border-0 shadow-sm" style="max-width:860px;">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold"><i class="bi bi-pencil me-2"></i>Write Fatwa Answer — {{ $question->question_id }}</h6>
    </div>
    <div class="card-body">
        {{-- Question Summary --}}
        <div class="alert alert-light border mb-4">
            <strong>Question:</strong><br>{{ Str::limit(strip_tags($question->body), 300) }}
        </div>

        <form method="POST" action="{{ route('admin.questions.answer.store', $question) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Answering Mufti <span class="text-danger">*</span></label>
                <select name="mufti_id" class="form-select @error('mufti_id') is-invalid @enderror" required>
                    <option value="">Select Mufti...</option>
                    @foreach($muftis as $m)
                        <option value="{{ $m->id }}" {{ old('mufti_id', $question->muftiAnswer?->mufti_id) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
                @error('mufti_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Question Summary <span class="text-danger">*</span></label>
                <input type="text" name="summary" class="form-control @error('summary') is-invalid @enderror"
                       placeholder="Brief one-line summary of the question" value="{{ old('summary', $question->muftiAnswer?->summary) }}" required>
                @error('summary')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Full Answer <span class="text-danger">*</span></label>
                <textarea name="full_answer" rows="8" class="form-control @error('full_answer') is-invalid @enderror"
                          placeholder="Write the full fatwa answer here..." required>{{ old('full_answer', $question->muftiAnswer?->full_answer) }}</textarea>
                @error('full_answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold"><i class="bi bi-book me-2"></i>Supporting Islamic References</label>
                <div id="references-container">
                    @php 
                        $refs = $question->muftiAnswer?->supportingReferences ?? [];
                    @endphp
                    
                    @if(count($refs) > 0)
                        @foreach($refs as $ref)
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
                        {{-- Fresh row if no references exist --}}
                        <div class="reference-row card border-0 bg-light p-3 mb-3">
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Arabic Text</label>
                                <textarea name="arabic_text[]" rows="3" class="form-control arabic-text"
                                          placeholder="اكتب النص العربي هنا..."></textarea>
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
                <button type="button" id="add-reference" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Add Another Reference
                </button>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-5">
                    <i class="bi bi-send me-2"></i>Save & Proceed to Verify
                </button>
                <a href="{{ route('admin.questions.show', $question) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
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
    const firstRow = container.querySelector('.reference-row');
    const template = firstRow.cloneNode(true);
    template.querySelectorAll('textarea, input').forEach(el => el.value = '');
    container.appendChild(template);
});
// Remove reference row
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-ref')) {
        const rows = document.querySelectorAll('.reference-row');
        if (rows.length > 1) {
            e.target.closest('.reference-row').remove();
        } else {
            // If only one row, just clear it
            e.target.closest('.reference-row').querySelectorAll('textarea, input').forEach(el => el.value = '');
        }
    }
});
</script>
@endpush
@endsection
