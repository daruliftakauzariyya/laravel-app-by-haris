@extends('layouts.public')
@section('title', 'Query Details')
@section('content')
<div class="container py-5" style="max-width: 900px; min-height: 60vh;">
    
    <div class="mb-4">
        <a href="{{ route('user.dashboard') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-green">Query #{{ $question->question_id }}</h5>
            
            @if($question->status == 'pending')
                <span class="badge bg-warning text-dark px-3 py-2">Pending Review</span>
            @elseif($question->status == 'published')
                <span class="badge bg-success px-3 py-2">Published Fatwa</span>
            @elseif($question->status == 'answered')
                <span class="badge bg-info px-3 py-2">Answered</span>
            @elseif($question->status == 'rejected')
                <span class="badge bg-danger px-3 py-2">Rejected</span>
            @else
                <span class="badge bg-secondary px-3 py-2">{{ ucfirst(str_replace('_', ' ', $question->status)) }}</span>
            @endif
        </div>
        
        <div class="card-body p-4">
            <div class="row mb-3 text-muted small">
                <div class="col-md-4 mb-2">
                    <i class="bi bi-calendar3"></i> Submitted: <strong>{{ $question->created_at->format('d M, Y') }}</strong>
                </div>
                <div class="col-md-4 mb-2">
                    <i class="bi bi-diagram-2"></i> Category: <strong>{{ $question->category ? $question->category->name : 'Uncategorized' }}</strong>
                </div>
                <div class="col-md-4 mb-2">
                    <i class="bi bi-book"></i> Madhab: <strong>{{ $question->madhab }}</strong>
                </div>
            </div>
            
            <hr>
            
            <h5 class="fw-bold mt-4">{{ $question->title ?: 'No Title Provided' }}</h5>
            <div class="p-3 bg-light rounded mt-3" style="font-size: 1.1rem; line-height: 1.6;">
                {!! nl2br(e($question->body)) !!}
            </div>
        </div>
    </div>

    @if($question->muftiAnswer && in_array($question->status, ['answered', 'approved', 'published']))
    <div class="card shadow-sm border-0 border-top border-success border-3">
        <div class="card-body p-4">
            <h5 class="fw-bold text-green mb-3"><i class="bi bi-check-circle-fill"></i> Mufti's Answer</h5>
            
            <div class="p-3 rounded mt-3" style="font-size: 1.1rem; line-height: 1.6; background-color: #f8FAF8;">
                {!! nl2br(e($question->muftiAnswer->full_answer)) !!}
            </div>

            @if($question->status == 'published')
            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted small"><i class="bi bi-globe"></i> This answer has been officially published to the public archive.</span>
                <a href="{{ route('fatwa.archive') }}" class="btn btn-outline-success btn-sm">View Archive</a>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection
