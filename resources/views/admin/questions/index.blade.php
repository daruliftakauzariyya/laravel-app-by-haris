@extends('layouts.admin')
@section('title', 'Questions')
@section('topbar-title', 'Question Management')
@section('content')

{{-- Filters --}}
<form method="GET" class="card border-0 shadow-sm p-3 mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search ID or text..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                @foreach(['pending','accepted','assigned','answered','under_review','published','rejected'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="madhab" class="form-select form-select-sm">
                <option value="">All Madhabs</option>
                @foreach(['Hanafi','Shafii','Maliki','Hanbali','Other'] as $m)
                    <option value="{{ $m }}" {{ request('madhab') == $m ? 'selected' : '' }}>{{ $m == 'Shafii' ? "Shafi'i" : $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
            <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-secondary btn-sm">×</a>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th>Question ID</th>
                    <th>Questioner</th>
                    <th>Question</th>
                    <th>Madhab</th>
                    <th>Assigned To</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $q)
                <tr>
                    <td><span class="badge bg-secondary">{{ $q->question_id }}</span></td>
                    <td>{{ $q->user->name }}<br><small class="text-muted">{{ $q->user->email }}</small></td>
                    <td>{{ Str::limit(strip_tags($q->body), 70) }}</td>
                    <td>{{ $q->madhab == 'Shafii' ? "Shafi'i" : $q->madhab }}</td>
                    <td>{{ $q->assignedMufti?->name ?? '—' }}</td>
                    <td><small class="text-muted">{{ $q->created_at->format('d M Y') }}</small></td>
                    <td><span class="badge badge-status-{{ $q->status }}">{{ ucfirst(str_replace('_',' ',$q->status)) }}</span></td>
                    <td>
                        <a href="{{ route('admin.questions.show', $q) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No questions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $questions->links() }}</div>
</div>
@endsection
