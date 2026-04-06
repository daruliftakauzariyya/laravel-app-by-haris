@extends('layouts.mufti')
@section('title', 'My Questions')
@section('topbar-title', 'Assigned Questions')
@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr><th>ID</th><th>Questioner</th><th>Question</th><th>Madhab</th><th>Date</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse($questions as $q)
                <tr>
                    <td><span class="badge bg-secondary">{{ $q->question_id }}</span></td>
                    <td>{{ $q->user->name }}</td>
                    <td>{{ Str::limit($q->body, 70) }}</td>
                    <td>{{ $q->madhab == 'Shafii' ? "Shafi'i" : $q->madhab }}</td>
                    <td><small class="text-muted">{{ $q->created_at->format('d M Y') }}</small></td>
                    <td><span class="badge bg-{{ $q->status == 'answered' ? 'success' : 'primary' }}">{{ ucfirst($q->status) }}</span></td>
                    <td><a href="{{ route('mufti.questions.show', $q) }}" class="btn btn-sm btn-outline-primary">
                        {{ $q->muftiAnswer ? 'Edit' : 'Answer' }}
                    </a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No questions assigned.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $questions->links() }}</div>
</div>
@endsection
