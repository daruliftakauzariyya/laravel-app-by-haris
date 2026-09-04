@extends('layouts.admin')
@section('title', 'Import Old / Published Fatwas')
@section('topbar-title', 'Add & Import Old Published Fatwa')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
    .arabic-input {
        font-family: 'Amiri', 'Noto Naskh Arabic', serif;
        direction: rtl;
        font-size: 1.15rem;
        line-height: 1.8;
    }
    .note-editor.note-frame {
        border: 1px solid #ced4da;
        border-radius: 6px;
    }
    .note-toolbar {
        background-color: #f8fafc;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
    }
    .badge-step {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: var(--brand-blue, #1a7a4a);
        color: #fff;
        font-size: 0.85rem;
        margin-right: 8px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    {{-- Success Alert with View Link --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-between p-3 mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                <div>
                    <div>{!! session('success') !!}</div>
                    @if(session('last_view_url'))
                        <div class="mt-1">
                            <a href="{{ session('last_view_url') }}" target="_blank" class="btn btn-sm btn-outline-success fw-bold">
                                <i class="bi bi-box-arrow-up-right me-1"></i> View Published Fatwa Page
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show p-3 mb-4 shadow-sm" role="alert">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-octagon-fill me-2"></i>Please fix the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Main Form Column --}}
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.fatwas.import-old.store') }}" id="importFatwaForm">
                @csrf

                {{-- Card 1: Question Details --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0 border-bottom">
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <span class="badge-step">1</span> Question Details
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            {{-- Question / Fatwa ID --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Fatwa / Question ID <span class="text-danger">*</span></label>
                                <input type="text" name="question_id" class="form-control @error('question_id') is-invalid @enderror"
                                       value="{{ old('question_id', $suggestedQuestionId) }}" placeholder="e.g. A0001, Q-1001, F-502" required>
                                <div class="form-text small">Unique reference code</div>
                                @error('question_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Question Summary / Title --}}
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Question Summary / Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title') }}" placeholder="Brief one-line summary (e.g. Doubt regarding Wudu validity)" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Question Body (Formatted Text Box) --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Question Text (Supports all formats, paragraphs, etc.) <span class="text-danger">*</span></label>
                                <textarea name="body" id="question_body" class="form-control @error('body') is-invalid @enderror" rows="5"
                                          placeholder="Enter the full question text submitted by the questioner...">{{ old('body') }}</textarea>
                                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Fatwa Answer --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0 border-bottom">
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <span class="badge-step">2</span> Fatwa Answer / Ruling
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Fatwa Answer (Rich Text Editor) <span class="text-danger">*</span></label>
                            <textarea name="answer" id="answer_editor" class="form-control @error('answer') is-invalid @enderror" rows="10">{{ old('answer') }}</textarea>
                            @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text small mt-2 text-muted">
                                <span class="badge bg-success-subtle text-success me-1"><i class="bi bi-tag-fill me-1"></i>Tagging / Linking:</span>
                                Type <code>@{Question-ID}</code> (e.g. <code>@{A0001}</code>) anywhere in the answer box to automatically link a published fatwa as an interactive reference badge.
                                <span class="text-danger fw-semibold d-block mt-1"><i class="bi bi-exclamation-circle me-1"></i>Must contain at least <strong>30 characters</strong> before saving/publishing.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Supporting References --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <span class="badge-step">3</span> Supporting Islamic References
                            </h6>
                            <small class="text-muted">Reference text is always written in Arabic or Urdu.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success" id="add-reference-btn">
                            <i class="bi bi-plus-circle me-1"></i> Add Another Reference
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div id="references-container">
                            <div class="reference-row p-3 border rounded mb-3 bg-light position-relative">
                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <label class="form-label fw-semibold small">Arabic / Urdu Reference Text (متن المرجع)</label>
                                        <textarea name="arabic_text[]" rows="3" class="form-control arabic-input" placeholder="نص العبارة أو المرجع الفقهي بالعربية أو الأردية..."></textarea>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="mb-2">
                                            <label class="form-label fw-semibold small">Book / Source Name (اسم الكتاب والمجلد والصفحة)</label>
                                            <input type="text" name="book_name[]" class="form-control form-control-sm" placeholder="e.g. رد المحتار ۲/۱۱٥, المجموع">
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold small">Reference Language</label>
                                            <select name="ref_language[]" class="form-select form-select-sm">
                                                <option value="Arabic" selected>Arabic (العربية)</option>
                                                <option value="Urdu">Urdu (اردو)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-ref-btn">
                                        <i class="bi bi-trash me-1"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="d-flex gap-3 mb-5">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold flex-grow-1 shadow-sm">
                        <i class="bi bi-cloud-arrow-up-fill me-2"></i> Save & Publish Old Fatwa
                    </button>
                    <button type="reset" class="btn btn-light px-4 py-2 border">Reset Form</button>
                </div>

        </div>

        {{-- Sidebar Column: Metadata & Classification --}}
        <div class="col-lg-4">

            {{-- Card 4: Classification --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-tags me-2 text-primary"></i>Category & Madhab</h6>
                </div>
                <div class="card-body p-3">
                    {{-- Category --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $parent)
                                <optgroup label="{{ $parent->name }} {{ $parent->name_ar ? '('.$parent->name_ar.')' : '' }}">
                                    <option value="{{ $parent->id }}" {{ old('category_id') == $parent->id ? 'selected' : '' }}>
                                        ★ {{ $parent->name }} (Main)
                                    </option>
                                    @foreach($parent->children as $child)
                                        <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;↳ {{ $child->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Madhab --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Madhab (Fiqh School)</label>
                        <select name="madhab_id" class="form-select @error('madhab_id') is-invalid @enderror">
                            <option value="">-- Select Madhab (or General) --</option>
                            @foreach($madhabs as $madhab)
                                <option value="{{ $madhab->id }}" {{ old('madhab_id') == $madhab->id ? 'selected' : '' }}>
                                    {{ $madhab->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('madhab_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Language --}}
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Language <span class="text-danger">*</span></label>
                        <select name="language" class="form-select @error('language') is-invalid @enderror" required>
                            <option value="English" {{ old('language', 'English') == 'English' ? 'selected' : '' }}>English</option>
                            <option value="Malayalam" {{ old('language') == 'Malayalam' ? 'selected' : '' }}>Malayalam (മലയാളം)</option>
                            <option value="Urdu" {{ old('language') == 'Urdu' ? 'selected' : '' }}>Urdu (اردو)</option>
                            <option value="Arabic" {{ old('language') == 'Arabic' ? 'selected' : '' }}>Arabic (العربية)</option>
                        </select>
                        @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Card 5: People & Mufti Attribution --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-people me-2 text-primary"></i>Questioner & Mufti</h6>
                </div>
                <div class="card-body p-3">
                    {{-- Select Mufti --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Answering Mufti <span class="text-danger">*</span></label>
                        <select name="assigned_mufti_id" class="form-select @error('assigned_mufti_id') is-invalid @enderror" required>
                            <option value="">-- Select Mufti --</option>
                            @foreach($muftis as $mufti)
                                <option value="{{ $mufti->id }}" {{ old('assigned_mufti_id') == $mufti->id ? 'selected' : ($loop->first ? 'selected' : '') }}>
                                    {{ $mufti->name }} ({{ $mufti->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_mufti_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Checked / Approved by --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Checked & Approved By</label>
                        <select name="checked_approved_by" class="form-select">
                            <option value="">-- Same as Answering Mufti / Admin --</option>
                            @foreach($admins as $adm)
                                <option value="{{ $adm->id }}" {{ old('checked_approved_by') == $adm->id ? 'selected' : '' }}>
                                    {{ $adm->name }} ({{ $adm->role?->name ?? 'Staff' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Questioner User --}}
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold mb-0">Questioner Account</label>
                            <button type="button" class="btn btn-xs btn-outline-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#createQuestionerModal">
                                <i class="bi bi-person-plus-fill me-1"></i> + New Questioner
                            </button>
                        </div>
                        <select name="user_id" id="questioner_select" class="form-select">
                            <option value="{{ $defaultQuestioner?->id }}" selected>
                                👤 Questioner Demo ({{ $defaultQuestioner?->email }}) [Default]
                            </option>
                            @foreach($questioners as $q)
                                @if($q->id !== $defaultQuestioner?->id)
                                    <option value="{{ $q->id }}" {{ old('user_id') == $q->id ? 'selected' : '' }}>
                                        👤 {{ $q->name }} ({{ $q->email ?: $q->mobile }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <div id="new_questioner_alert" class="d-none mt-2 alert alert-success py-1 px-2 small mb-0">
                            <i class="bi bi-check-circle-fill me-1"></i> <span id="new_questioner_name_txt"></span> selected!
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 6: Publishing Details & Stats --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-gear me-2 text-primary"></i>Publishing & Metadata</h6>
                </div>
                <div class="card-body p-3">
                    {{-- Status --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>🟢 Published (Publicly Visible)</option>
                            <option value="answered" {{ old('status') == 'answered' ? 'selected' : '' }}>🔵 Answered (Pending Admin Verification)</option>
                            <option value="assigned" {{ old('status') == 'assigned' ? 'selected' : '' }}>🟡 Assigned to Mufti</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>⚪ Pending Review</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Published Date --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Original Published Date</label>
                        <input type="date" name="published_at" class="form-control"
                               value="{{ old('published_at', date('Y-m-d')) }}">
                        <div class="form-text small">e.g. 08 Jul 2026 or past platform date</div>
                    </div>

                    {{-- Views, Likes, Stars --}}
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Views</label>
                            <input type="number" name="views" class="form-control form-control-sm" value="{{ old('views', 0) }}" min="0">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Likes</label>
                            <input type="number" name="likes" class="form-control form-control-sm" value="{{ old('likes', 0) }}" min="0">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Stars</label>
                            <input type="number" name="stars" class="form-control form-control-sm" value="{{ old('stars', 0) }}" min="0">
                        </div>
                    </div>

                    {{-- Tags / Keywords --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tags / Keywords</label>
                        <input type="text" name="tags" class="form-control form-control-sm"
                               value="{{ old('tags') }}" placeholder="wudu, taharah, prayer (comma separated)">
                    </div>

                    {{-- Featured Image Cropper (Optional) --}}
                    <div>
                        <label class="form-label fw-semibold small">Featured Image (Optional)</label>
                        <input type="file" id="image-input" class="form-control form-control-sm" accept="image/*">
                        <input type="hidden" name="featured_image_cropped" id="featured_image_cropped">
                        <div id="image-preview-container" class="mt-2 d-none">
                            <img id="image-preview" src="" class="img-fluid rounded border" style="max-height:120px;">
                        </div>
                    </div>
                </div>
            </div>

            </form>
        </div>
    </div>

    {{-- Bottom Table: Recent Imported Fatwas --}}
    <div class="card border-0 shadow-sm mt-3 mb-5">
        <div class="card-header bg-white py-3 border-0 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history me-2"></i>Recently Added / Imported Fatwas</h6>
            <span class="badge bg-light text-dark border">{{ $recentFatwas->count() }} Records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>ID</th>
                        <th>Title / Summary</th>
                        <th>Category</th>
                        <th>Madhab</th>
                        <th>Mufti</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($recentFatwas as $f)
                        <tr>
                            <td class="fw-bold text-primary">{{ $f->question_id }}</td>
                            <td class="text-truncate" style="max-width:250px;" title="{{ $f->title }}">{{ $f->title ?? '—' }}</td>
                            <td><span class="badge bg-secondary">{{ $f->category?->name ?? 'Uncategorized' }}</span></td>
                            <td>{{ $f->madhab?->name ?? 'General' }}</td>
                            <td>{{ $f->assignedMufti?->name ?? '—' }}</td>
                            <td>{{ $f->published_at ? $f->published_at->format('d M Y') : $f->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge {{ $f->status === 'published' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ ucfirst($f->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('fatwa.show', $f->id) }}" target="_blank" class="btn btn-xs btn-outline-primary" title="View Public Page">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.questions.show', $f->id) }}" class="btn btn-xs btn-outline-secondary" title="View in Admin">
                                    <i class="bi bi-gear"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No fatwas recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal for Cropping Image --}}
<div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop Featured Image (16:9)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div style="max-height: 400px; background: #333;" class="d-flex align-items-center justify-content-center">
                    <img id="cropper-image" src="" style="max-width: 100%; max-height: 400px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="crop-save-btn">Crop & Use Image</button>
            </div>
        </div>
    </div>
</div>
{{-- Modal: Create New Questioner --}}
<div class="modal fade" id="createQuestionerModal" tabindex="-1" aria-labelledby="createQuestionerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold fs-6" id="createQuestionerModalLabel">
                    <i class="bi bi-person-plus-fill text-primary me-2"></i> Create New Questioner
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modal_q_error" class="alert alert-danger py-2 small d-none mb-3"></div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Questioner Name <span class="text-danger">*</span></label>
                    <input type="text" id="modal_q_name" class="form-control" placeholder="e.g. Brother Ahmed / Fatima Khan" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Mobile Number</label>
                    <input type="text" id="modal_q_mobile" class="form-control" placeholder="e.g. +91 98765 43210 (Optional)">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Email ID</label>
                    <input type="email" id="modal_q_email" class="form-control" placeholder="e.g. questioner@example.com (Optional)">
                    <div class="form-text small">If not provided, a portal username will be auto-generated.</div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm fw-bold" id="btn_save_questioner">
                    <i class="bi bi-check2-circle me-1"></i> Save & Select
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

<script>
$(document).ready(function() {
    // 1. Initialize Summernote for Question Body
    $('#question_body').summernote({
        placeholder: 'Paste or type question text here (supports all text formats, fonts, paragraphs, Arabic, etc.)...',
        tabsize: 2,
        height: 160,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'table']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

    // Mention Button for Summernote
    const MentionButton = function (context) {
        const ui = $.summernote.ui;
        const button = ui.button({
            contents: '<i class="bi bi-at text-success fw-bold"></i> Ref Fatwa',
            tooltip: 'Insert Published Fatwa Reference (@ID)',
            click: function () {
                window.openFatwaMentionPicker('#answer_editor', '', true);
            }
        });
        return button.render();
    };

    // 2. Initialize Summernote for Answer / Fatwa
    $('#answer_editor').summernote({
        placeholder: 'Paste or write the fatwa answer here with complete formatting, headings, lists, quotes, etc.... (Type @ to search and reference published fatwas)',
        tabsize: 2,
        height: 280,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'hr']],
            ['custom', ['mentionRef']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
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

    // 3. Dynamic References Rows
    $('#add-reference-btn').on('click', function() {
        const container = $('#references-container');
        const firstRow = container.find('.reference-row').first();
        const clone = firstRow.clone();
        clone.find('textarea, input').val('');
        clone.find('select').val('Arabic');
        container.append(clone);
    });

    $(document).on('click', '.remove-ref-btn', function() {
        const rows = $('.reference-row');
        if (rows.length > 1) {
            $(this).closest('.reference-row').remove();
        } else {
            $(this).closest('.reference-row').find('textarea, input').val('');
        }
    });

    // 4. Cropper.js for Featured Image
    let cropper = null;
    const imageInput = document.getElementById('image-input');
    const cropperImage = document.getElementById('cropper-image');
    const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
    const previewImg = document.getElementById('image-preview');
    const previewContainer = document.getElementById('image-preview-container');
    const hiddenInput = document.getElementById('featured_image_cropped');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    cropperImage.src = event.target.result;
                    cropModal.show();
                };
                reader.readAsDataURL(files[0]);
            }
        });

        document.getElementById('cropModal').addEventListener('shown.bs.modal', function() {
            cropper = new Cropper(cropperImage, {
                aspectRatio: 16 / 9,
                viewMode: 1,
                autoCropArea: 1,
            });
        });

        document.getElementById('cropModal').addEventListener('hidden.bs.modal', function() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            imageInput.value = '';
        });

        document.getElementById('crop-save-btn').addEventListener('click', function() {
            if (!cropper) return;
            const canvas = cropper.getCroppedCanvas({
                width: 800,
                height: 450,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });
            const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
            previewImg.src = dataUrl;
            hiddenInput.value = dataUrl;
            previewContainer.classList.remove('d-none');
            cropModal.hide();
        });
    }

    // 5. AJAX Create Questioner Modal Logic
    $('#btn_save_questioner').on('click', function() {
        const name = $('#modal_q_name').val().trim();
        const mobile = $('#modal_q_mobile').val().trim();
        const email = $('#modal_q_email').val().trim();
        const errorDiv = $('#modal_q_error');
        const saveBtn = $(this);

        if (!name) {
            errorDiv.text('Please enter questioner name.').removeClass('d-none');
            $('#modal_q_name').focus();
            return;
        }

        errorDiv.addClass('d-none');
        saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        fetch('{{ route("admin.fatwas.quick-create-questioner") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                mobile: mobile,
                email: email
            })
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Save & Select');
            if (data.success && data.user) {
                // Add new option and select it
                const newOption = new Option(data.user.display, data.user.id, true, true);
                $('#questioner_select').append(newOption).trigger('change');
                $('#questioner_select').val(data.user.id);

                // Show confirmation badge
                $('#new_questioner_name_txt').text(data.user.name);
                $('#new_questioner_alert').removeClass('d-none');

                // Clear modal form and close
                $('#modal_q_name').val('');
                $('#modal_q_mobile').val('');
                $('#modal_q_email').val('');
                const modalEl = document.getElementById('createQuestionerModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modalInstance.hide();
            } else {
                errorDiv.text(data.message || 'Error creating questioner.').removeClass('d-none');
            }
        })
        .catch(err => {
            saveBtn.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Save & Select');
            errorDiv.text('Server error. Please try again.').removeClass('d-none');
            console.error(err);
        });
    });
});
</script>
@include('partials.fatwa-mention-modal')
@endpush
