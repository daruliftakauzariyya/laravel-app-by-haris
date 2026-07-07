@extends('layouts.admin')
@section('title', 'Accept Question ' . $question->question_id)
@section('topbar-title', 'Accept Question')
@section('content')
<div class="row g-4">
    {{-- Left: Original Question Review --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <div>
                    <span class="badge bg-secondary me-2">{{ $question->question_id }}</span>
                    <span class="badge badge-status-{{ $question->status }}">{{ ucfirst($question->status) }}</span>
                </div>
                <a href="{{ route('admin.questions.show', $question) }}" class="btn btn-sm btn-outline-secondary">← Back to Details</a>
            </div>
            @php
                $langClass = '';
                if($question->language == 'Urdu') $langClass = 'gulzar-font';
                elseif($question->language == 'Arabic') $langClass = 'alyamama-font';
                elseif($question->language == 'Malayalam') $langClass = 'noto-serif-malayalam-font';
            @endphp
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted fw-bold d-block mb-1">User Selected Madhab:</small>
                    <span class="badge px-3 py-2 text-white" style="background:#1a7a4a; font-size: 0.85rem; border-radius: 30px;">
                        <i class="bi bi-book-half me-1"></i>{{ $question->madhab?->name ?? '—' }}
                    </span>
                </div>
                
                <div class="mb-4">
                    <small class="text-muted fw-bold d-block mb-2">Original Question Text:</small>
                    <div class="p-3 bg-light rounded-3 border {{ $langClass }}" style="line-height:1.8; font-size: 1.05rem; max-height: 400px; overflow-y: auto;">
                        {!! $question->body !!}
                    </div>
                </div>

                <div class="row g-3 small text-muted pt-2 border-top">
                    <div class="col-sm-6"><i class="bi bi-person-fill me-2 text-primary"></i><strong>Submitted by:</strong> {{ $question->user->name }}</div>
                    <div class="col-sm-6"><i class="bi bi-envelope-fill me-2 text-primary"></i><strong>Email:</strong> {{ $question->user->email }}</div>
                    <div class="col-sm-6"><i class="bi bi-telephone-fill me-2 text-primary"></i><strong>Mobile:</strong> {{ $question->user->mobile }}</div>
                    <div class="col-sm-6"><i class="bi bi-calendar-check-fill me-2 text-primary"></i><strong>Submitted:</strong> {{ $question->created_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Right: Accept Form --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; border-top: 4px solid #0d6efd !important;">
            <div class="card-header bg-white border-0 py-3" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-check2-square me-2"></i>Question Categorization</h5>
            </div>
            <form method="POST" action="{{ route('admin.questions.accept', $question) }}">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Question Summary (Short Question) <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg border-2" value="{{ old('title', $question->title) }}" maxlength="200" required placeholder="Enter a concise summary...">
                        <small class="text-muted d-block mt-1">A clear, brief summary (max 200 chars) for archive headings.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Question Language <span class="text-danger">*</span></label>
                        <select name="language" class="form-select form-select-lg border-2" required>
                            <option value="Urdu" {{ old('language', $question->language) == 'Urdu' || $question->user->mother_language == 'Urdu' ? 'selected' : '' }}>Urdu</option>
                            <option value="Arabic" {{ old('language', $question->language) == 'Arabic' || $question->user->mother_language == 'Arabic' ? 'selected' : '' }}>Arabic</option>
                            <option value="English" {{ old('language', $question->language) == 'English' || $question->user->mother_language == 'English' ? 'selected' : '' }}>English</option>
                            <option value="Hindi" {{ old('language', $question->language) == 'Hindi' || $question->user->mother_language == 'Hindi' ? 'selected' : '' }}>Hindi</option>
                            <option value="Malayalam" {{ old('language', $question->language) == 'Malayalam' || $question->user->mother_language == 'Malayalam' ? 'selected' : '' }}>Malayalam</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Main Category <span class="text-danger">*</span></label>
                        <select id="main_category" class="form-select form-select-lg border-2" required>
                            <option value="">Select Main Category...</option>
                            @foreach($mainCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}{{ $cat->name_ar ? ' (' . $cat->name_ar . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4" id="sub_category_container" style="display:none;">
                        <label class="form-label fw-bold text-dark">Sub Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="sub_category" class="form-select form-select-lg border-2" required>
                            <option value="">Select Sub Category...</option>
                            @foreach($subCategories as $sub)
                                <option value="{{ $sub->id }}" data-parent="{{ $sub->parent_id }}">{{ $sub->name }}{{ $sub->name_ar ? ' (' . $sub->name_ar . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <a href="{{ route('admin.questions.show', $question) }}" class="btn btn-lg btn-outline-secondary w-50" style="border-radius: 12px;">Cancel</a>
                        <button type="submit" class="btn btn-lg btn-primary w-50" style="border-radius: 12px;">
                            <i class="bi bi-check2-circle me-1"></i>Accept & Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Store all original subcategory options in a safe place
const allSubOptions = Array.from(document.querySelectorAll('#sub_category option[data-parent]'));

document.getElementById('main_category').addEventListener('change', function() {
    const parentId = this.value;
    const subContainer = document.getElementById('sub_category_container');
    const subSelect = document.getElementById('sub_category');

    // Create a fresh list of options starting with the placeholder
    subSelect.innerHTML = '<option value="">Select Sub Category...</option>';

    if (parentId) {
        // Find matching options from our stored list
        const matches = allSubOptions.filter(opt => opt.getAttribute('data-parent') === parentId);

        if (matches.length > 0) {
            subContainer.style.display = 'block';
            matches.forEach(opt => {
                // Re-append the matching option a clone to keep the data
                subSelect.appendChild(opt.cloneNode(true));
            });
        } else {
            // Show a "No subcategories" message if nothing matches
            const emptyOpt = document.createElement('option');
            emptyOpt.text = "No subcategories found for this section";
            emptyOpt.disabled = true;
            subSelect.appendChild(emptyOpt);
            subContainer.style.display = 'block';
        }
    } else {
        // Hide if no main category selected
        subContainer.style.display = 'none';
    }
    
    subSelect.value = ''; // Reset selection
});
</script>
@endsection
