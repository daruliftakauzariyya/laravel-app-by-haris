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
                <small class="text-muted">By: {{ $question->user->name }} | Madhab: {{ $question->madhab == 'Shafii' ? "Shafi'i" : $question->madhab }}</small>
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
                <form method="POST" action="{{ route('admin.questions.approve', $question) }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Verified by:</label>
                        <select name="verified_by" class="form-select form-select-sm">
                            <option value="">-- Select Verifier --</option>
                            @foreach($verifierMuftis as $mufti)
                                <option value="{{ $mufti->id }}">{{ $mufti->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Checked & Approved by:</label>
                        <select name="checked_approved_by" class="form-select form-select-sm">
                            <option value="">-- Select Approver --</option>
                            @foreach($approverMuftis as $mufti)
                                <option value="{{ $mufti->id }}">{{ $mufti->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button class="btn btn-success w-100" onclick="return confirm('Publish this fatwa?')">
                        <i class="bi bi-patch-check me-2"></i>Approve & Publish
                    </button>
                </form>
            </div>
        </div>

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
