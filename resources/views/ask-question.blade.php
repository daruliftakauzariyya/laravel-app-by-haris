@extends('layouts.public')
@section('title', 'Ask a Question')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { font-size: 1rem; line-height: 1.6; min-height: 200px; }
</style>
@endpush

@section('content')
<div class="container py-5" style="max-width:720px;">
    <div class="mb-4">
        <h2 class="fw-bold text-green"><i class="bi bi-pencil-square me-2"></i>Ask a Question</h2>
        <p class="text-muted">Submit your Islamic question and receive a verified Fatwa from a qualified Mufti.</p>
    </div>
    <div class="card border-0 shadow p-4">
        <form method="POST" action="{{ route('question.store') }}" enctype="multipart/form-data" id="askForm">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Question Title / Summary <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                       placeholder="A brief summary of your question (max 200 chars)" maxlength="200" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Your Full Question <span class="text-danger">*</span></label>
                <div id="editor-container" style="background: #fff; min-height: 200px;">{!! old('body') !!}</div>
                <input type="hidden" name="body" id="body-input" value="{{ old('body') }}">
                @error('body')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Madhab <span class="text-danger">*</span></label>
                    <select name="madhab" class="form-select @error('madhab') is-invalid @enderror" required>
                        <option value="">Select Madhab...</option>
                        @foreach(['Hanafi','Shafii','Maliki','Hanbali','Other'] as $m)
                            <option value="{{ $m }}" {{ old('madhab') == $m ? 'selected' : '' }}>
                                {{ $m == 'Shafii' ? "Shafi'i" : $m }}
                            </option>
                        @endforeach
                    </select>
                    @error('madhab')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Supporting Files <small class="text-muted">(Optional, PDF/DOC/Images, max 10MB each)</small></label>
                <input type="file" name="files[]" class="form-control @error('files.*') is-invalid @enderror"
                       multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                @error('files.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text"><i class="bi bi-info-circle me-1"></i>Accepted: PDF, DOC, DOCX, JPG, PNG — Max 10MB per file</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                    <i class="bi bi-send me-2"></i>Submit Question
                </button>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 text-decoration-none">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold'], 
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean']
            ]
        },
        placeholder: 'Write your full question in detail here...'
    });

    document.getElementById('askForm').onsubmit = function() {
        var body = document.querySelector('input[name=body]');
        body.value = quill.root.innerHTML;
        if(quill.getText().trim().length === 0) {
            alert('Please enter your question.');
            return false;
        }
    };
</script>
@endpush
@endsection
