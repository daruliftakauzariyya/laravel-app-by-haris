@extends('layouts.public')
@section('title', 'Contact Us')
@section('content')
<div class="container py-5" style="max-width:700px;">
    <h2 class="fw-bold text-green mb-2"><i class="bi bi-envelope me-2"></i>Contact Us</h2>
    <p class="text-muted mb-4">For enquiries about the portal, please reach us via the details below.</p>
    <div class="card border-0 shadow p-4">
        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="fw-bold text-green"><i class="bi bi-envelope-at me-2"></i>Email</h6>
                <p class="mb-0"><a href="mailto:darulifta.kauzariyya@gmail.com">darulifta.kauzariyya@gmail.com</a></p>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold text-green"><i class="bi bi-globe me-2"></i>Portal</h6>
                <p class="mb-0">Darul Ifta Kauzariyya</p>
            </div>
        </div>
        <hr class="my-4">
        <p class="text-muted small mb-0">
            <i class="bi bi-info-circle me-2"></i>
            For urgent Islamic questions, please use the <a href="{{ route('question.create') }}">Ask a Question</a> portal.
        </p>
    </div>
</div>
@endsection
