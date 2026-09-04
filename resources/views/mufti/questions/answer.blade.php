@extends('layouts.mufti')
@section('title', 'Draft Fatwa Answer')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
<style>
    .ref-node {
        background: #ffffff;
        border-left: 4px solid #745a26;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e7e1de;
        border-left-width: 4px;
        border-left-color: #745a26;
    }
    .ref-node .arabic-area {
        font-family: 'Amiri', serif;
        font-size: 1.25rem;
        line-height: 1.9;
        direction: rtl;
        resize: vertical;
    }
    .fatwa-timeline { list-style: none; padding: 0; position: relative; }
    .fatwa-timeline::before {
        content: ''; position: absolute; left: 14px; top: 0; bottom: 0;
        width: 2px; background: #e7e1de;
    }
    .timeline-item { position: relative; padding-left: 40px; margin-bottom: 20px; }
    .t-icon {
        position: absolute; left: 0; top: 0;
        width: 30px; height: 30px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        z-index: 2; font-size: 0.8rem;
    }
</style>
@endpush

@section('content')
<!-- Page Header Sub-bar -->
<section class="border-b border-outline-variant/60 pb-3 flex flex-wrap items-baseline justify-between gap-2">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="font-mono text-[13px] font-bold text-primary bg-surface-container px-2.5 py-0.5 rounded border border-outline-variant">{{ $question->question_id }}</span>
            <span class="text-label-md text-outline">/</span>
            <span class="text-[13px] font-semibold text-secondary">{{ $question->madhab?->name ?? 'General Madhab' }}</span>
        </div>
        <h1 class="font-headline-lg text-[24px] text-primary tracking-tight font-medium">Scholarly Workspace & Fatwa Drafting</h1>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('mufti.questions') }}" class="px-3 py-1.5 rounded border border-outline-variant text-on-surface-variant hover:text-primary text-[13px] font-medium inline-flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Back to Inquiries</span>
        </a>
    </div>
</section>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
    <!-- Main Drafting Column (8 cols) -->
    <div class="xl:col-span-8">
        <form method="POST" action="{{ route('mufti.questions.answer', $question) }}" id="fatwaForm" class="flex flex-col gap-6">
            @csrf

            {{-- Inquiry Overview Card --}}
            <div class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-6 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-surface-variant/60 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-secondary">help_outline</span>
                        <h4 class="font-title-md text-[16px] font-semibold text-primary m-0">Question Details</h4>
                    </div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-outline">Inquiry Text</span>
                </div>

                <div class="mb-4">
                    <label class="block font-label-md text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1">Inquiry Title / Summary</label>
                    <input type="text" name="question_title" class="w-full px-3.5 py-2 rounded border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary bg-white text-[14px] text-on-surface font-semibold" value="{{ old('question_title', $question->title) }}" placeholder="Original Inquiry Title...">
                </div>

                <div>
                    <label class="block font-label-md text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1">Inquiry Body</label>
                    <textarea name="question_body" id="summernote-question" class="w-full">{{ old('question_body', $question->body) }}</textarea>
                </div>
            </div>

            {{-- Full Answer Editor Card --}}
            <div class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-6 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-surface-variant/60 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-primary">edit_document</span>
                        <h4 class="font-headline-sm text-[17px] font-semibold text-primary m-0">Authoritative Fatwa Answer (Full Response)</h4>
                    </div>
                    <span class="text-[11px] text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded font-semibold">Live Editor</span>
                </div>

                <div class="mb-3">
                    <textarea name="full_answer" id="summernote" required>{{ old('full_answer', $question->draft_answer ?? $question->answer) }}</textarea>
                </div>

                <div class="bg-surface-container-low border border-surface-variant rounded p-3 text-[12px] flex flex-col gap-1">
                    <div class="text-on-surface-variant flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-emerald-700 text-[16px]">bookmark_add</span>
                        <span><strong>Cross-reference:</strong> Type <code class="bg-white px-1.5 py-0.5 rounded border text-emerald-800 font-mono">@{Question-ID}</code> (e.g. <code>@{A0001}</code>) or click <strong>Ref Fatwa</strong> to tag published references at caret position.</span>
                    </div>
                    <div class="text-red-700 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px]">info</span>
                        <span>The answer must contain at least <strong>30 characters</strong> before submission.</span>
                    </div>
                </div>
            </div>

            {{-- Classical References & Evidences --}}
            <div class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-6 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-surface-variant/60 mb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-secondary">menu_book</span>
                            <h4 class="font-title-md text-[16px] font-semibold text-primary m-0">Classical References & Evidences</h4>
                        </div>
                        <p class="font-body-sm text-[12px] text-on-surface-variant mt-0.5 mb-0">Arabic or Urdu classical legal texts from authenticated fiqh works.</p>
                    </div>
                    <button type="button" id="add-reference" class="px-3.5 py-1.5 rounded bg-primary text-on-primary text-[12px] font-medium hover:bg-primary/90 transition-all inline-flex items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined text-[15px]">add</span>
                        <span>Add Evidence</span>
                    </button>
                </div>

                <div id="references-container">
                    @php 
                        $refs = $question->references->isNotEmpty() 
                            ? $question->references 
                            : ($question->draftReferences->isNotEmpty() 
                                ? $question->draftReferences 
                                : collect([new \App\Models\Reference()])); 
                    @endphp
                    @foreach($refs as $ref)
                    <div class="ref-node">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-outline uppercase tracking-wider bg-surface-container px-2 py-0.5 rounded border border-outline-variant">Ref #{{ $loop->iteration }}</span>
                            <button type="button" class="btn-close remove-ref" style="font-size: 0.65rem;"></button>
                        </div>
                        <textarea name="arabic_text[]" rows="2" class="w-full arabic-area p-2.5 rounded border border-outline-variant bg-white text-on-surface mb-3" placeholder="أدخل النص العربي أو الأردي للمرجع... e.g. قال الله تعالى..." required>{{ $ref->arabic_text }}</textarea>
                        
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                            <div class="md:col-span-8">
                                <input type="text" name="book_name[]" class="w-full px-3 py-1.5 rounded border border-outline-variant text-[13px] bg-white text-on-surface" placeholder="Source Book / Volume / Page (e.g. Radd al-Muhtar 2/145)" value="{{ $ref->book_name }}">
                            </div>
                            <div class="md:col-span-4">
                                <select name="ref_language[]" class="w-full px-3 py-1.5 rounded border border-outline-variant text-[13px] bg-white text-on-surface">
                                    <option value="Arabic" {{ $ref->language == 'Arabic' ? 'selected' : '' }}>العربية (Arabic)</option>
                                    <option value="Urdu" {{ $ref->language == 'Urdu' ? 'selected' : '' }}>اردو (Urdu)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Floating Action Bar --}}
            <div class="sticky bottom-4 z-20 bg-surface-container-lowest/95 backdrop-blur-md border border-outline-variant/80 rounded-xl p-4 shadow-lg flex flex-wrap items-center justify-between gap-3">
                <div class="text-[13px] font-medium text-on-surface-variant flex items-center gap-2">
                    <span class="text-outline">Draft Status:</span>
                    @if($question->draft_answer)
                        <span class="text-emerald-700 font-semibold" id="draft-status-text">Saved as Draft ({{ $question->updated_at->format('H:i') }})</span>
                    @else
                        <span class="text-secondary font-semibold" id="draft-status-text">Unsaved Draft</span>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" id="save-draft-btn" class="px-4 py-2 rounded bg-surface-container hover:bg-surface-container-high border border-outline-variant text-[13px] font-semibold text-on-surface inline-flex items-center gap-1.5 transition-all">
                        <span class="material-symbols-outlined text-[17px]">save</span>
                        <span>Save Draft</span>
                    </button>
                    <button type="submit" class="px-6 py-2 rounded bg-primary text-on-primary text-[13px] font-semibold hover:bg-primary/90 inline-flex items-center gap-1.5 shadow-sm transition-all">
                        <span class="material-symbols-outlined text-[17px]">send</span>
                        <span>Submit for Verification</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Right Context Column (4 cols) -->
    <div class="xl:col-span-4 flex flex-col gap-6">
        <!-- Questioner Context -->
        <div class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-5 shadow-sm">
            <div class="flex items-center gap-2 pb-3 border-b border-surface-variant/60 mb-3">
                <span class="material-symbols-outlined text-[18px] text-secondary">person</span>
                <h4 class="font-title-md text-[15px] font-semibold text-primary m-0">Questioner Context</h4>
            </div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-sm">
                    {{ substr($question->user->name, 0, 1) }}
                </div>
                <div>
                    <h5 class="font-semibold text-on-surface text-[14px] m-0">{{ $question->user->name }}</h5>
                    <p class="text-[12px] text-on-surface-variant m-0">{{ $question->user->country ?? 'Unknown' }} · {{ $question->language ?? 'Default' }}</p>
                </div>
            </div>
            <div class="flex flex-col gap-2 text-[12px] border-t border-surface-variant/60 pt-3">
                <div class="flex justify-between">
                    <span class="text-outline">Submitted:</span>
                    <span class="font-medium text-on-surface">{{ $question->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-outline">Waiting Time:</span>
                    <span class="font-semibold text-amber-800">{{ $question->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-5 shadow-sm">
            <div class="flex items-center gap-2 pb-3 border-b border-surface-variant/60 mb-4">
                <span class="material-symbols-outlined text-[18px] text-secondary">timeline</span>
                <h4 class="font-title-md text-[15px] font-semibold text-primary m-0">Fatwa Lifecycle</h4>
            </div>
            <div class="fatwa-timeline">
                @forelse($question->activityLogs as $log)
                <div class="timeline-item">
                    <div class="t-icon bg-primary text-on-primary">
                        <i class="bi {{ $log->icon ?: 'bi-record-circle' }}"></i>
                    </div>
                    <div class="bg-surface-container-low p-2.5 rounded border border-surface-variant text-[12px]">
                        <span class="font-semibold text-primary block">{{ $log->event_label }}</span>
                        <p class="text-on-surface-variant mb-1 mt-0.5">{{ $log->notes ?: 'Process milestone reached.' }}</p>
                        <div class="flex justify-between text-[10px] text-outline border-t border-surface-variant/60 pt-1 mt-1">
                            <span>{{ $log->actor->name ?? 'System' }}</span>
                            <span>{{ $log->event_at ? $log->event_at->format('M d, H:i') : '' }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-[12px] text-outline text-center py-4">No milestone logs recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
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
                window.openFatwaMentionPicker('#summernote', '', true);
            }
        });
        return button.render();
    };

    $('#summernote').summernote({
        height: 380,
        placeholder: 'Begin your scholarly response here... (Type @ to search and reference published fatwas)',
        dialogsInBody: true,
        toolbar: [
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['custom', ['mentionRef']],
            ['view', ['fullscreen', 'codeview']]
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
        },
        styleTags: ['p', 'h3', 'h4', 'h5', 'h6', 'blockquote'],
    });

    $('#summernote-question').summernote({
        height: 180,
        placeholder: 'Original question text...',
        dialogsInBody: true,
        toolbar: [
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

    // Reference Engine Logic
    let refCount = $('#references-container .ref-node').length;
    
    $('#add-reference').on('click', function() {
        refCount++;
        const newNode = `
            <div class="ref-node" style="opacity: 0; transform: translateY(10px)">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-outline uppercase tracking-wider bg-surface-container px-2 py-0.5 rounded border border-outline-variant">Ref #${refCount}</span>
                    <button type="button" class="btn-close remove-ref" style="font-size: 0.65rem;"></button>
                </div>
                <textarea name="arabic_text[]" rows="2" class="w-full arabic-area p-2.5 rounded border border-outline-variant bg-white text-on-surface mb-3" placeholder="أدخل النص العربي أو الأردي للمرجع... e.g. قال الله تعالى..." required></textarea>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-8">
                        <input type="text" name="book_name[]" class="w-full px-3 py-1.5 rounded border border-outline-variant text-[13px] bg-white text-on-surface" placeholder="Source Book / Volume / Page">
                    </div>
                    <div class="md:col-span-4">
                        <select name="ref_language[]" class="w-full px-3 py-1.5 rounded border border-outline-variant text-[13px] bg-white text-on-surface">
                            <option value="Arabic">العربية (Arabic)</option>
                            <option value="Urdu">اردو (Urdu)</option>
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

    $('#save-draft-btn').on('click', function(e) {
        e.preventDefault();
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...');
        
        $('#summernote').val($('#summernote').summernote('code'));
        if ($('#summernote-question').length) {
            $('#summernote-question').val($('#summernote-question').summernote('code'));
        }
        
        const formData = $('#fatwaForm').serialize();
        
        $.ajax({
            url: "{{ route('mufti.questions.draft', $question) }}",
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    $('#draft-status-text').removeClass('text-secondary text-red-700').addClass('text-emerald-700').html('Saved as Draft (' + response.time + ')');
                } else {
                    $('#draft-status-text').removeClass('text-secondary text-emerald-700').addClass('text-red-700').text('Save Failed');
                }
            },
            error: function() {
                $('#draft-status-text').removeClass('text-secondary text-emerald-700').addClass('text-red-700').text('Save Failed');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<span class="material-symbols-outlined text-[17px]">save</span><span>Save Draft</span>');
            }
        });
    });

    function refreshCounts() {
        $('#references-container .ref-node').each(function(index) {
            $(this).find('.text-outline').text('Ref #' + (index + 1));
        });
        refCount = $('#references-container .ref-node').length;
    }
});
</script>
@include('partials.fatwa-mention-modal')
@endpush
@endsection
