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
                       placeholder="Brief one-line summary of the question" value="{{ old('summary', $question->muftiAnswer?->summary ?? $question->title) }}" required>
                @error('summary')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Language Selector --}}
            <div class="mb-4 p-3 rounded" style="background:#f8f9fa; border:1px solid #e5e7eb;">
                <label class="form-label fw-bold mb-3">
                    <i class="bi bi-translate me-2 text-success"></i>Question Language 
                    <span class="text-muted small fw-normal">(Sets font on the public page)</span>
                </label>
                <div class="row g-3 align-items-start">
                    <div class="col-md-5">
                        <select name="language" id="language-select" class="form-select">
                            <option value="English"   {{ old('language', $question->language) == 'English'   ? 'selected' : '' }}>🇬🇧 English</option>
                            <option value="Arabic"    {{ old('language', $question->language) == 'Arabic'    ? 'selected' : '' }}>🇸🇦 Arabic</option>
                            <option value="Urdu"      {{ old('language', $question->language) == 'Urdu'      ? 'selected' : '' }}>🇵🇰 Urdu</option>
                            <option value="Malayalam" {{ old('language', $question->language) == 'Malayalam' ? 'selected' : '' }}>🇮🇳 Malayalam</option>
                            <option value="Hindi"     {{ old('language', $question->language) == 'Hindi'     ? 'selected' : '' }}>🇮🇳 Hindi</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <div id="font-preview-box" class="font-preview text-muted" style="min-height:48px; font-size:1.1rem;">
                            — Font preview will appear here —
                        </div>
                    </div>
                </div>
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
            e.target.closest('.reference-row').querySelectorAll('textarea, input').forEach(el => el.value = '');
        }
    }
});

// ================================================
// Live Font Preview for Language Selector
// ================================================
const fontMap = {
    'English':   { font: "'Google Sans Eng', 'Inter', sans-serif", dir: 'ltr', sample: 'The ruling on this matter is as follows in Islamic jurisprudence...' },
    'Arabic':    { font: "'Noto Naskh Arabic', serif",             dir: 'rtl', sample: 'بسم الله الرحمن الرحيم — الجواب وبالله التوفيق والنجاح' },
    'Urdu':      { font: "'Noto Nastaliq Urdu', serif",            dir: 'rtl', sample: 'بسم اللہ الرحمٰن الرحیم — اس مسئلے کا جواب یہ ہے...' },
    'Malayalam': { font: "'Noto Serif Malayalam', serif",          dir: 'ltr', sample: 'ബിസ്മില്ലാഹ് — ഈ കാര്യത്തിൽ ഇസ്‌ലാമിക വിധി ഇപ്രകാരമാണ്...' },
    'Hindi':     { font: "'Google Sans Eng', 'Inter', sans-serif", dir: 'ltr', sample: 'इस्लामी न्यायशास्त्र के अनुसार इस विषय में यह निर्णय है...' },
};

function updateFontPreview(lang) {
    const preview = document.getElementById('font-preview-box');
    const config = fontMap[lang] || fontMap['English'];
    preview.style.fontFamily = config.font;
    preview.style.direction = config.dir;
    preview.style.textAlign = config.dir === 'rtl' ? 'right' : 'left';
    preview.style.fontSize = lang === 'Urdu' ? '1.3rem' : '1.1rem';
    preview.style.lineHeight = (lang === 'Urdu' || lang === 'Arabic') ? '2.2' : '1.6';
    preview.style.color = '#1a202c';
    preview.textContent = config.sample;
}

const langSelect = document.getElementById('language-select');
if (langSelect) {
    updateFontPreview(langSelect.value); // run on load
    langSelect.addEventListener('change', function() { updateFontPreview(this.value); });
}

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
@endpush
@endsection
