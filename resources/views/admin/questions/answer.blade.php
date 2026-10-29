@extends('layouts.admin')
@section('title', 'Write Answer')
@section('topbar-title', 'Write Answer on Behalf of Mufti')
@section('topbar-icon', 'bi-pencil-square')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
    <a href="{{ route('admin.questions.index') }}">Questions</a>
    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
    <span class="text-dark fw-semibold">Draft Answer</span>
@endsection
@section('content')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush
<div class="card border-0 shadow-sm" style="max-width:860px;">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold"><i class="bi bi-pencil me-2"></i>Write Fatwa Answer — {{ $question->question_id }}</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.questions.answer.store', $question) }}">
            @csrf

            {{-- Editable Question Details --}}
            <div class="card border mb-4 bg-light shadow-none">
                <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-secondary small"><i class="bi bi-pencil-square me-1"></i>Edit Question Text & Summary (HTML Supported)</span>
                    <span class="badge bg-secondary">Original Questioner Input</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Question Summary / Title <span class="text-danger">*</span></label>
                        <input type="text" name="question_title" class="form-control @error('question_title') is-invalid @enderror" value="{{ old('question_title', $question->title) }}" placeholder="e.g. Concise summary / title of the question..." required>
                        @error('question_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label fw-semibold small">Question Body (Full Inquiry Details — Rich HTML Editor)</label>
                        <textarea name="question_body" id="summernote-question" class="form-control" placeholder="Original question text...">{{ old('question_body', $question->body) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Answering Mufti <span class="text-danger">*</span></label>
                <select name="mufti_id" class="form-select @error('mufti_id') is-invalid @enderror" required>
                    <option value="">Select Mufti...</option>
                    @forelse($muftis as $m)
                        @php
                            $isMatch = $question->madhab_id && $m->madhab_id === $question->madhab_id;
                            $madhabSuffix = $m->madhab ? ' (' . $m->madhab->name . ($isMatch ? ' — Recommended' : '') . ')' : '';
                        @endphp
                        <option value="{{ $m->id }}" {{ old('mufti_id', $question->assigned_mufti_id) == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}{{ $madhabSuffix }}
                        </option>
                    @empty
                        <option value="" disabled>No active Muftis registered</option>
                    @endforelse
                </select>
                @error('mufti_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label fw-semibold mb-0">Full Fatwa Answer (HTML Supported) <span class="text-danger">*</span></label>
                    <button type="button" class="btn btn-xs btn-outline-success rounded-pill px-3"
                            onclick="window.openFatwaMentionPicker($('#summernote'), '', true)">
                        <i class="bi bi-at me-1"></i> Reference Published Fatwa
                    </button>
                </div>
                <textarea name="full_answer" id="summernote" rows="8" class="form-control @error('full_answer') is-invalid @enderror"
                          placeholder="Write the full fatwa answer here... (Type @ to search and reference published fatwas)" required>{{ old('full_answer', $question->draft_answer ?? $question->answer) }}</textarea>
                @error('full_answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text small mt-1 text-muted">
                    <span class="badge bg-success-subtle text-success me-1"><i class="bi bi-tag-fill me-1"></i>Cross-reference Tag:</span>
                    Type <code>@{Question-ID}</code> (e.g. <code>@{A0001}</code>) or press <code>@</code> to search and link an existing published fatwa.
                    <span class="text-danger fw-semibold d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i>Must contain at least <strong>30 characters</strong> before saving/publishing.</span>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold"><i class="bi bi-book me-2"></i>Supporting Islamic References</label>
                <div id="references-container">
                    @php 
                        $refs = $question->references->isNotEmpty() ? $question->references : $question->draftReferences;
                    @endphp
                    
                    @if($refs->count() > 0)
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

            {{-- Featured Image (Archive Thumbnail) --}}
            @php $isPublished = $question->status === 'published'; @endphp
            <div class="mb-4 p-3 rounded" style="background:#f8f9fa; border:1px solid #e5e7eb;">
                <label class="form-label fw-bold mb-3"><i class="bi bi-image me-2 text-primary"></i>Archive Featured Image</label>
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <input type="file" id="image-input" class="form-control form-control-sm mb-2" accept="image/*">
                        <input type="hidden" name="featured_image_cropped" id="featured_image_cropped">
                        <p class="small text-muted mb-0">Recommended size: 800x450 (16:9). Image will be optimized for web.</p>
                    </div>
                    <div class="col-md-7">
                        <div id="image-preview-container" class="{{ $question->fatwa?->featured_image ? '' : 'd-none' }}">
                            <img id="image-preview" src="{{ $question->fatwa?->featured_image ? asset($question->fatwa->featured_image) : '' }}" 
                                 class="rounded border shadow-sm w-100" style="aspect-ratio:16/9; object-fit:cover;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-5">
                    <i class="bi bi-send me-2"></i>Save & Proceed
                </button>
                <a href="{{ route('admin.questions.show', $question) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

{{-- Cropping Modal --}}
<div class="modal fade" id="cropModal" tabindex="-1" data-bs-backdrop="static" style="z-index: 1060;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Crop Featured Image (16:9)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body overflow-hidden">
                <div class="img-container">
                    <img id="cropper-image" src="" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="crop-save-btn">Crop & Use Image</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
$(document).ready(function() {
    // Mention Button for Summernote
    const MentionButton = function (context) {
        const ui = $.summernote.ui;
        const button = ui.button({
            contents: '<i class="bi bi-at text-success fw-bold"></i> Ref Fatwa',
            tooltip: 'Insert Published Fatwa Reference (@ID)',
            click: function () {
                window.openFatwaMentionPicker($('#summernote'), '', true);
            }
        });
        return button.render();
    };

    // 1. Question Body HTML Editor
    $('#summernote-question').summernote({
        height: 200,
        placeholder: 'Original question text with rich HTML formatting...',
        dialogsInBody: true,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'hr']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        styleTags: ['p', 'h4', 'h5', 'h6', 'blockquote']
    });

    // 2. Full Fatwa Answer HTML Editor
    $('#summernote').summernote({
        height: 380,
        placeholder: 'Write the full scholarly fatwa answer here... (Type @ to search and reference published fatwas)',
        dialogsInBody: true,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'hr']],
            ['custom', ['mentionRef']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        styleTags: ['p', 'h3', 'h4', 'h5', 'h6', 'blockquote'],
        buttons: {
            mentionRef: MentionButton
        },
        hint: {
            match: /@([a-zA-Z0-9_-]*)$/,
            search: function (keyword, callback) {
                $.ajax({
                    url: '{{ route("fatwa.mention.search") }}',
                    data: { q: keyword },
                    success: function (data) {
                        callback(data);
                    }
                });
            },
            template: function (item) {
                return '<div class="d-flex justify-content-between align-items-center gap-2 p-1">' +
                       '<div><span class="badge bg-success me-1">' + item.question_id + '</span> <strong class="text-dark">' + item.title + '</strong></div>' +
                       '<small class="text-muted text-nowrap ms-2"><i class="bi bi-calendar3 me-1"></i>' + item.published_date + '</small>' +
                       '</div>';
            },
            content: function (item) {
                return '@{' + item.question_id + '} ';
            }
        }
    });

    // Sync Summernote code before form submit
    $('form').on('submit', function() {
        if ($('#summernote-question').length && $('#summernote-question').summernote) {
            $('#summernote-question').val($('#summernote-question').summernote('code'));
        }
        if ($('#summernote').length && $('#summernote').summernote) {
            $('#summernote').val($('#summernote').summernote('code'));
        }
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
            e.target.closest('.reference-row').querySelectorAll('textarea, input').forEach(el => el.value = '');
        }
    }
});

// --- Cropper Logic ---
let cropper;
const imageInput = document.getElementById('image-input');
const cropperImage = document.getElementById('cropper-image');
const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
const previewImg = document.getElementById('image-preview');
const previewContainer = document.getElementById('image-preview-container');
const hiddenInput = document.getElementById('featured_image_cropped');

if (imageInput) {
    imageInput.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const reader = new FileReader();
            reader.onload = function (event) {
                cropperImage.src = event.target.result;
                cropModal.show();
            };
            reader.readAsDataURL(files[0]);
        }
    });

    document.getElementById('cropModal').addEventListener('shown.bs.modal', function () {
        cropper = new Cropper(cropperImage, {
            aspectRatio: 16 / 9,
            viewMode: 1,
            autoCropArea: 1,
        });
    });

    document.getElementById('cropModal').addEventListener('hidden.bs.modal', function () {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        imageInput.value = ''; // Reset input to allow re-selection
    });

    document.getElementById('crop-save-btn').addEventListener('click', function () {
        if (!cropper) return;
        
        // Get optimized canvas (800x450 is good for quality)
        const canvas = cropper.getCroppedCanvas({
            width: 800,
            height: 450,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        // Convert to base64 with quality optimization (0.8)
        const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
        
        previewImg.src = dataUrl;
        hiddenInput.value = dataUrl;
        previewContainer.classList.remove('d-none');
        cropModal.hide();
    });
}
</script>
@include('partials.fatwa-mention-modal')
@endpush
@endsection
