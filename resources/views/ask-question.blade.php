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
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
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
                    <select name="madhab_id" class="form-select @error('madhab_id') is-invalid @enderror" required>
                        <option value="">Select Madhab...</option>
                        @foreach($madhabs as $m)
                            <option value="{{ $m->id }}" {{ old('madhab_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('madhab_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Supporting Files <small class="text-muted">(Optional, PDF/DOC/Images, max 10MB each)</small></label>
                <input type="file" id="file-uploader" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                <div id="upload-status-list" class="mt-2"></div>
                <div id="hidden-uploads-container"></div>
                <div class="form-text mt-1"><i class="bi bi-info-circle me-1"></i>Accepted: PDF, DOC, DOCX, JPG, PNG — Max 10MB per file</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" id="submit-btn" class="btn btn-primary px-5 py-2 fw-bold">
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
    // --- Quill Editor Setup ---
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: { toolbar: [['bold'], [{ 'list': 'ordered'}, { 'list': 'bullet' }], ['clean']] },
        placeholder: 'Write your full question in detail here...'
    });

    // --- Async File Upload Logic ---
    const fileInput = document.getElementById('file-uploader');
    const statusList = document.getElementById('upload-status-list');
    const hiddenContainer = document.getElementById('hidden-uploads-container');
    const submitBtn = document.getElementById('submit-btn');
    let activeUploads = 0;

    fileInput.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files.length === 0) return;

        Array.from(files).forEach(file => {
            uploadFile(file);
        });
        
        // Reset file input so same file can be re-selected if removed
        fileInput.value = '';
    });

    function uploadFile(file) {
        activeUploads++;
        updateSubmitButton();

        // Create UI for this file
        const fileId = 'file-' + Math.random().toString(36).substr(2, 9);
        const item = document.createElement('div');
        item.id = fileId;
        item.className = 'd-flex justify-content-between align-items-center p-2 mb-1 border rounded bg-light small';
        item.innerHTML = `
            <span><i class="bi bi-file-earmark me-2"></i>${file.name}</span>
            <span class="upload-status text-primary"><span class="spinner-border spinner-border-sm me-1"></span>Uploading...</span>
        `;
        statusList.appendChild(item);

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("question.files.upload") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            activeUploads--;
            if (data.success) {
                // Success UI
                item.querySelector('.upload-status').innerHTML = `
                    <span class="text-success me-2"><i class="bi bi-check-circle-fill"></i> Uploaded</span>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 remove-btn" data-path="${data.path}">✕</button>
                `;
                // Add hidden input
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'uploaded_files[]';
                hidden.value = JSON.stringify({
                    path: data.path,
                    name: data.original_name,
                    type: data.file_type,
                    size: data.file_size
                });
                item.setAttribute('data-hidden-id', fileId + '-hidden');
                hidden.id = fileId + '-hidden';
                hiddenContainer.appendChild(hidden);
            } else {
                item.querySelector('.upload-status').innerHTML = `<span class="text-danger">Upload failed</span>`;
            }
            updateSubmitButton();
        })
        .catch(error => {
            activeUploads--;
            item.querySelector('.upload-status').innerHTML = `<span class="text-danger">Error</span>`;
            updateSubmitButton();
        });
    }

    function updateSubmitButton() {
        if (activeUploads > 0) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Uploading files...`;
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<i class="bi bi-send me-2"></i>Submit Question`;
        }
    }

    // Handle removal
    statusList.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-btn')) {
            const btn = e.target;
            const path = btn.getAttribute('data-path');
            const item = btn.closest('div');
            const hiddenId = item.getAttribute('data-hidden-id');

            // Optional: Tell server to delete the temp file
            fetch('{{ route("question.files.delete") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ path: path })
            });

            // Remove UI and hidden input
            item.remove();
            const hidden = document.getElementById(hiddenId);
            if (hidden) hidden.remove();
        }
    });

    document.getElementById('askForm').onsubmit = function() {
        var body = document.querySelector('input[name=body]');
        body.value = quill.root.innerHTML;
        if(quill.getText().trim().length === 0) {
            alert('Please enter your question.');
            return false;
        }
        
        // Disable button to prevent double-click
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Posting Question...`;
        
        return true;
    };
</script>
@endpush
@endsection
