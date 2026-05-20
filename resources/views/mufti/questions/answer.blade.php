@extends('layouts.mufti')
@section('title', 'Submit Answer')
@section('topbar-title', 'Submit Fatwa Answer')
@section('content')
@section('topbar-title', 'Scholarly Workspace')
@section('content')

<style>
    /* ── Workspace Aesthetics ── */
    .workspace-wrapper { background: #f8fafc; min-height: 100vh; margin: -1.5rem; padding: 1.5rem; }
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    }
    
    .q-id-pill {
        background: rgba(41, 90, 117, 0.08);
        color: var(--brand-blue);
        font-weight: 700;
        font-size: 0.75rem;
        padding: 5px 14px;
        border-radius: 50px;
        letter-spacing: 0.5px;
    }

    /* ── References Section ── */
    .ref-node {
        background: #fff;
        border-left: 4px solid var(--brand-gold);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
    }
    .ref-node:hover { transform: translateX(5px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
    .ref-node .arabic-area {
        font-family: 'Amiri', serif;
        font-size: 1.35rem;
        line-height: 2;
        color: #1a202c;
        direction: rtl;
        border: none;
        background: #fdfdfd;
        resize: none;
    }
    .ref-node .arabic-area:focus { box-shadow: none; background: #fff; }

    /* ── Form Styling ── */
    .premium-input {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 18px;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    .premium-input:focus { border-color: var(--brand-blue); box-shadow: 0 0 0 4px rgba(41,90,117,0.1); }
    
    .floating-save-bar {
        position: sticky;
        bottom: 20px;
        z-index: 100;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 50px;
        padding: 12px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        margin-top: 40px;
    }

    /* ── Timeline ── */
    .fatwa-timeline { list-style: none; padding: 0; position: relative; }
    .fatwa-timeline::before {
        content: ''; position: absolute; left: 15px; top: 0; bottom: 0;
        width: 2px; background: rgba(0,0,0,0.05);
    }
    .timeline-item { position: relative; padding-left: 45px; margin-bottom: 25px; }
    .t-icon {
        position: absolute; left: 0; top: 0;
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        z-index: 2; font-size: 0.85rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
</style>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
@endpush

<div class="workspace-wrapper">
    <div class="row g-4">
        <div class="col-xl-8">
            <form method="POST" action="{{ route('mufti.questions.answer', $question) }}" id="fatwaForm">
                @csrf

                {{-- Editable Question Details --}}
                <div class="glass-card mb-4 overflow-hidden border-0 bg-white">
                    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <span class="q-id-pill mb-2 d-inline-block">{{ $question->question_id }}</span>
                            <span class="fw-800 text-secondary small d-block"><i class="bi bi-pencil-square me-1"></i>Review / Edit Original Question (Optional)</span>
                        </div>
                        <span class="badge px-3 py-2 rounded-pill" style="background: var(--brand-green);">{{ $question->madhab?->name ?? 'Common Ground' }}</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Questioner Name / Heading</label>
                            <input type="text" name="question_title" class="form-control premium-input border-0 bg-light fw-bold" value="{{ old('question_title', $question->title) }}" placeholder="Original Inquiry Title...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Question Body</label>
                            <textarea name="question_body" rows="6" class="form-control premium-input border-0 bg-light" style="line-height: 1.8;" placeholder="Original question text...">{{ old('question_body', $question->body) }}</textarea>
                        </div>
                        
                        @if($question->files->count())
                        <div class="attachment-reel d-flex gap-3 overflow-auto pt-3 border-top mt-3">
                            @foreach($question->files as $file)
                            <div class="flex-shrink-0" style="width: 140px;">
                                <a href="{{ route('files.view', $file) }}" target="_blank" class="text-decoration-none">
                                    <div class="rounded-3 border overflow-hidden bg-white shadow-sm hover-shadow transition-all">
                                        @if(str_starts_with($file->file_type, 'image/'))
                                            <img src="{{ route('files.view', $file) }}" class="w-100" style="height: 100px; object-fit: cover;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center bg-light" style="height: 100px;">
                                                <i class="bi bi-file-earmark-{{ $file->file_type == 'application/pdf' ? 'pdf text-danger' : 'text text-muted' }} fs-2"></i>
                                            </div>
                                        @endif
                                        <div class="p-2 x-small fw-bold text-dark text-truncate">{{ $file->original_name }}</div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                
                {{-- Global Summary --}}
                <div class="glass-card p-4 mb-4">
                    <h5 class="fw-800 mb-3"><i class="bi bi-chat-left-dots-fill me-2 text-gold"></i>Scholarly Summary</h5>
                    <input type="text" name="summary" class="form-control premium-input border-0 bg-light fw-bold" 
                           value="{{ old('summary', $question->muftiAnswer?->summary) }}"
                           placeholder="Enter a concise title or essence of this fatwa..." required>
                </div>

                {{-- Full Answer --}}
                <div class="glass-card mb-4 border-0">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <h5 class="fw-800 mb-0">Detailed Fatwa Breakdown</h5>
                    </div>
                    <div class="card-body p-0">
                        <textarea name="full_answer" id="summernote" required>{{ old('full_answer', $question->muftiAnswer?->full_answer) }}</textarea>
                    </div>
                </div>

                {{-- References Engine --}}
                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-800 mb-0"><i class="bi bi-book-half me-2 text-gold"></i>Classical References & Evidences</h5>
                        <button type="button" id="add-reference" class="btn btn-dark btn-sm rounded-pill px-4 shadow-sm hover-shadow">
                            <i class="bi bi-plus-lg me-2"></i>Link Evidence
                        </button>
                    </div>

                    <div id="references-container">
                        @php $refs = $question->muftiAnswer ? $question->muftiAnswer->supportingReferences : collect([new \App\Models\SupportingReference()]); @endphp
                        @foreach($refs as $ref)
                        <div class="ref-node">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-light text-muted x-small uppercase tracking-wider border">Ref #{{ $loop->iteration }}</span>
                                <button type="button" class="btn-close remove-ref" style="font-size: 0.7rem;"></button>
                            </div>
                            <textarea name="arabic_text[]" rows="2" class="form-control arabic-area mb-3" placeholder="أدخل النص العربي للمرجع... e.g. قال الله تعالى..." required>{{ $ref->arabic_text }}</textarea>
                            
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-journal-bookmark"></i></span>
                                        <input type="text" name="book_name[]" class="form-control premium-input border-start-0 ps-0" placeholder="Source Book / Author" value="{{ $ref->book_name }}">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <select name="ref_language[]" class="form-select form-select-sm premium-input">
                                        <option value="Arabic" {{ $ref->language == 'Arabic' ? 'selected' : '' }}>العربية (Arabic)</option>
                                        <option value="Urdu" {{ $ref->language == 'Urdu' ? 'selected' : '' }}>اردو (Urdu)</option>
                                        <option value="Malayalam" {{ $ref->language == 'Malayalam' ? 'selected' : '' }}>മലയാളം (Malayalam)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="floating-save-bar">
                    <div class="small fw-bold text-muted">Draft Status: <span class="text-brand-gold">Unsaved</span></div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">
                            <i class="bi bi-shield-check me-2"></i>Finalize & Submit Review
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-xl-4">
            {{-- Questioner Info --}}
            <div class="glass-card p-4 mb-4 border-0">
                <h6 class="fw-800 text-muted uppercase tracking-wider small mb-3">Sender Context</h6>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-brand-blue text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height:50px;">
                        <i class="bi bi-person-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-800 mb-0">{{ $question->user->name }}</h6>
                        <div class="text-muted x-small">{{ $question->user->country }} · {{ $question->language ?? 'Default' }}</div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="d-flex justify-content-between p-2 rounded-3 bg-light mb-2">
                        <span class="x-small text-muted">Submitted</span>
                        <span class="x-small fw-bold">{{ $question->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between p-2 rounded-3 mb-2" style="background: #fff8e1;">
                        <span class="x-small text-muted">Efficiency Queue</span>
                        <span class="x-small fw-800 text-gold">{{ $question->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            {{-- Dynamic Activity Log --}}
            <div class="glass-card border-0">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="fw-800 mb-0">Fatwa Lifecycle</h6>
                </div>
                <div class="card-body p-4">
                    <div class="fatwa-timeline">
                        @foreach($question->activityLogs as $log)
                        <div class="timeline-item">
                            <div class="t-icon bg-{{ $log->color }} text-white shadow-{{ $log->color }}">
                                <i class="bi {{ $log->icon }}"></i>
                            </div>
                            <div class="bg-white p-3 rounded-4 shadow-sm border">
                                <span class="x-small fw-800 text-{{ $log->color }} uppercase tracking-wider">{{ $log->event_label }}</span>
                                <p class="x-small text-dark mb-1 mt-1 font-semibold">{{ $log->notes ?: 'Process milestone reached.' }}</p>
                                <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                    <span class="x-small opacity-50"><i class="bi bi-person me-1"></i>{{ $log->actor->name ?? 'System' }}</span>
                                    <span class="x-small opacity-50">{{ $log->event_at->format('M d, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
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
    $('#summernote').summernote({
        height: 400,
        placeholder: 'Begin your scholarly response here...',
        dialogsInBody: true,
        toolbar: [
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['view', ['fullscreen', 'codeview']]
        ],
        styleTags: ['p', 'h3', 'h4', 'h5', 'h6', 'blockquote'],
    });

    // Reference Engine Logic
    let refCount = $('#references-container .ref-node').length;
    
    $('#add-reference').on('click', function() {
        refCount++;
        const newNode = `
            <div class="ref-node" style="opacity: 0; transform: translateY(10px)">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-light text-muted x-small uppercase tracking-wider border">Ref #${refCount}</span>
                    <button type="button" class="btn-close remove-ref" style="font-size: 0.7rem;"></button>
                </div>
                <textarea name="arabic_text[]" rows="2" class="form-control arabic-area mb-3" placeholder="أدخل النص العربي للمرجع... e.g. قال الله تعالى..." required></textarea>
                <div class="row g-3">
                    <div class="col-md-7">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-journal-bookmark"></i></span>
                            <input type="text" name="book_name[]" class="form-control premium-input border-start-0 ps-0" placeholder="Source Book / Author">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select name="ref_language[]" class="form-select form-select-sm premium-input">
                            <option value="Arabic">العربية (Arabic)</option>
                            <option value="Urdu">اردו (Urdu)</option>
                            <option value="Malayalam">മലയാളം (Malayalam)</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
        const $nodes = $(newNode).appendTo('#references-container');
        $nodes.animate({ opacity: 1 }, 300);
    });

    $(document).on('click', '.remove-ref', function() {
        if($('#references-container .ref-node').length > 1) {
            $(this).closest('.ref-node').slideUp(200, function() { $(this).remove(); refreshCounts(); });
        }
    });

    function refreshCounts() {
        $('#references-container .ref-node').each(function(index) {
            $(this).find('.badge').text('Ref #' + (index + 1));
        });
        refCount = $('#references-container .ref-node').length;
    }
});
</script>
@endpush
@endsection
@endsection
