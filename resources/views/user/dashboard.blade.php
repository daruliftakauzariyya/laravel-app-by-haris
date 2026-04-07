@extends('layouts.public')
@section('title', 'My Dashboard')
@section('content')
<div class="container py-5" style="min-height: 60vh;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="bi bi-speedometer2 text-green"></i> My Query Dashboard</h4>
        <a href="{{ route('question.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Ask New Fatwa</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4">Query ID</th>
                            <th class="py-3">Subject / Title</th>
                            <th class="py-3">Date Submitted</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                        <tr>
                            <td class="align-middle fw-semibold px-4">{{ $question->question_id }}</td>
                            <td class="align-middle">{{ $question->title ?: 'No Title Provided' }}</td>
                            <td class="align-middle text-muted">{{ $question->created_at->format('d M, Y') }}</td>
                            <td class="align-middle">
                                @if($question->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($question->status == 'published')
                                    <span class="badge bg-success">Published</span>
                                @elseif($question->status == 'answered')
                                    <span class="badge bg-info">Answered</span>
                                @elseif($question->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $question->status)) }}</span>
                                @endif
                            </td>
                            <td class="align-middle text-end px-4">
                                <a href="{{ route('user.question.show', $question->id) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox d-block mb-3" style="font-size: 2rem;"></i>
                                You have not submitted any queries yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-4">
        {{ $questions->links() }}
    </div>
</div>
@endsection
