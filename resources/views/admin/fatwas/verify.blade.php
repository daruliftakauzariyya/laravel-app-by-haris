@extends('layouts.admin')
@section('title', 'Verify Fatwa')
@section('topbar-title', 'Verify & Publish Fatwa')
@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        {{-- Question --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-2">
                <h6 class="mb-0 fw-bold"><i class="bi bi-question-circle me-2"></i>Question — {{ $question->question_id }}</h6>
            </div>
            <div class="card-body">
                @if($question->title)<h5 class="fw-bold">{{ $question->title }}</h5>@endif
                <p style="line-height:1.8;">{!! $question->body !!}</p>
                <small class="text-muted">By: {{ $question->user->name }} | Madhab: {{ $question->madhab?->name == 'Shafii' ? "Shafi'i" : ($question->madhab?->name ?? 'General/Other') }}</small>
            </div>
        </div>

        {{-- Answer --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-2 d-flex justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-chat-text me-2"></i>Mufti's Answer</h6>
                <span class="small text-muted">by {{ $question->muftiAnswer->mufti->name }}</span>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-0 py-2 mb-3">
                    <strong>Summary:</strong> {{ $question->muftiAnswer->summary }}
                </div>
                <p style="line-height:1.9;">{!! $question->muftiAnswer->full_answer !!}</p>
            </div>
        </div>

        {{-- References --}}
        @if($question->muftiAnswer->supportingReferences->count())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-2"><h6 class="mb-0 fw-bold"><i class="bi bi-book me-2"></i>Supporting References</h6></div>
            <div class="card-body">
                @foreach($question->muftiAnswer->supportingReferences as $ref)
                <div class="p-3 mb-3 rounded" style="background:#fafafa; border:1px solid #e5e7eb;">
                    <div class="arabic-text text-success mb-2">{{ $ref->arabic_text }}</div>
                    <p class="small mb-0"><i class="bi bi-journal me-1"></i><strong>{{ $ref->book_name }}</strong>
                        <span class="badge bg-secondary ms-2">{{ $ref->language }}</span>
                    </p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="col-lg-4">
        {{-- Approve --}}
        <div class="card border-0 shadow-sm mb-3 border-success">
            <div class="card-body">
                <h6 class="fw-bold text-success mb-3"><i class="bi bi-check-circle me-2"></i>Approve & Publish</h6>
                <p class="small text-muted mb-3">This will publish the fatwa publicly and notify the questioner by email.</p>
                <form method="POST" action="{{ route('admin.questions.approve', $question) }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Featured Image <span class="text-muted">(16:9 Aspect Ratio)</span>:</label>
                        <input type="file" id="image-input" class="form-control form-control-sm" accept="image/*">
                        <input type="hidden" name="featured_image_cropped" id="featured_image_cropped">
                        
                        <div id="image-preview-container" class="mt-2 {{ $question->fatwa?->featured_image ? '' : 'd-none' }}">
                            <img id="image-preview" src="{{ $question->fatwa?->featured_image ? asset($question->fatwa->featured_image) : '' }}" 
                                 alt="Preview" class="rounded border shadow-sm w-100" style="aspect-ratio: 16/9; object-fit: cover;">
                            <div class="text-center mt-1">
                                <span class="badge bg-success small">Ready to Publish</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold d-block">Verified by <span class="text-muted">(Multiple allowed)</span>:</label>
                        <div id="verified_by_container" class="border rounded p-2 bg-light" style="max-height: 160px; overflow-y: auto;">
                            @foreach($verifierMuftis as $mufti)
                                <div class="form-check my-1">
                                    <input class="form-check-input verifier-checkbox" type="checkbox" name="verified_by[]" value="{{ $mufti->id }}" id="verifier_{{ $mufti->id }}"
                                        {{ $question->fatwa?->verifiers->contains($mufti->id) ? 'checked' : '' }}>
                                    <label class="form-check-label small cursor-pointer" for="verifier_{{ $mufti->id }}">
                                        {{ $mufti->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Checked & Approved by:</label>
                        <select name="checked_approved_by" id="checked_approved_by" class="form-select form-select-sm" required>
                            <option value="">-- Select Approver --</option>
                            @foreach($approverMuftis as $mufti)
                                <option value="{{ $mufti->id }}" {{ $question->fatwa?->checked_approved_by == $mufti->id ? 'selected' : '' }}>{{ $mufti->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-success w-100" id="publish-btn">
                        <i class="bi bi-patch-check me-2"></i>Approve & Publish
                    </button>
                </form>
            </div>
        </div>

        {{-- Cropping Modal --}}
        <div class="modal fade" id="cropModal" tabindex="-1" data-bs-backdrop="static">
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
        <script>
            // --- Mufti Filtering Logic ---
            const writtenById = "{{ $question->muftiAnswer->mufti_id }}";
            const verifierCheckboxes = document.querySelectorAll('.verifier-checkbox');
            const approverSelect = document.getElementById('checked_approved_by');

            function filterMuftis() {
                // Get all selected verifier IDs as an array of trimmed strings
                const selectedVerifierIds = [];
                verifierCheckboxes.forEach(cb => {
                    if (cb.checked) {
                        selectedVerifierIds.push(String(cb.value).trim());
                    }
                });
                
                const selectedApproverId = approverSelect.value ? String(approverSelect.value).trim() : "";
                const authorId = writtenById ? String(writtenById).trim() : "";

                // Disable options/checkboxes in the verifiers list if they are the author or the currently selected approver
                verifierCheckboxes.forEach(cb => {
                    const val = String(cb.value).trim();
                    const container = cb.closest('.form-check');
                    if (val === authorId) {
                        cb.disabled = true;
                        cb.checked = false;
                        if (container) container.style.opacity = '0.5';
                    } else if (selectedApproverId && val === selectedApproverId) {
                        cb.disabled = true;
                        cb.checked = false; // Deselect if checked
                        if (container) container.style.opacity = '0.5';
                    } else {
                        cb.disabled = false;
                        if (container) container.style.opacity = '1';
                    }
                });

                // Disable options in approverSelect if they are selected as a verifier or is the author
                for (let i = 0; i < approverSelect.options.length; i++) {
                    const opt = approverSelect.options[i];
                    if (!opt.value) continue; // Skip placeholder option
                    const val = String(opt.value).trim();
                    
                    if (val === authorId || selectedVerifierIds.indexOf(val) !== -1) {
                        opt.disabled = true;
                        if (opt.selected) {
                            approverSelect.value = ""; // Reset if it was selected
                        }
                    } else {
                        opt.disabled = false;
                    }
                }
            }

            verifierCheckboxes.forEach(cb => {
                cb.addEventListener('change', filterMuftis);
            });
            approverSelect.addEventListener('change', filterMuftis);
            window.addEventListener('load', filterMuftis);

            // Client-side required validation for checkboxes
            document.querySelector('#publish-btn').closest('form').addEventListener('submit', function(e) {
                const checkedCount = Array.from(verifierCheckboxes).filter(cb => cb.checked).length;
                if (checkedCount === 0) {
                    e.preventDefault();
                    alert('Please select at least one Mufti in "Verified by" list.');
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
                
                // Get optimized canvas (400px width is plenty for cards)
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

            document.getElementById('publish-btn').addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to approve and publish this fatwa?')) {
                    e.preventDefault();
                }
            });
        </script>
        @endpush

        {{-- Request Correction --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h6 class="fw-bold text-warning mb-3"><i class="bi bi-pencil-square me-2"></i>Request Correction</h6>
                <form method="POST" action="{{ route('admin.questions.correction', $question) }}">
                    @csrf
                    <textarea name="admin_notes" class="form-control form-control-sm mb-2" rows="3"
                              placeholder="What needs to be corrected?" required></textarea>
                    <button class="btn btn-warning btn-sm w-100">Send Back for Correction</button>
                </form>
            </div>
        </div>

        {{-- Reject --}}
        <div class="card border-0 shadow-sm border-danger">
            <div class="card-body">
                <h6 class="fw-bold text-danger mb-3"><i class="bi bi-x-circle me-2"></i>Reject Answer</h6>
                <form method="POST" action="{{ route('admin.questions.reject', $question) }}">
                    @csrf
                    <textarea name="admin_notes" class="form-control form-control-sm mb-2" rows="3"
                              placeholder="Reason for rejection..." required></textarea>
                    <button class="btn btn-danger btn-sm w-100">Reject</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
