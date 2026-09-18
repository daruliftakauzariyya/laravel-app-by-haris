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
                @foreach([
                    'pending' => 'Pending',
                    'accepted' => 'Accepted',
                    'assigned' => 'Assigned',
                    'drafted' => 'Mufti Drafted',
                    'answered' => 'Answered',
                    'under_review' => 'Under Review',
                    'published' => 'Published',
                    'rejected' => 'Rejected'
                ] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="madhab_id" class="form-select form-select-sm">
                <option value="">All Madhabs</option>
                @foreach(\App\Models\Madhab::all() as $m)
                    <option value="{{ $m->id }}" {{ request('madhab_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
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
                    <th>Question Summary</th>
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
                    <td>
                        @php
                            $textSample = ($q->title ?? '') . ' ' . strip_tags($q->body ?? '');
                            $isMalayalam = (bool) preg_match('/[\x{0D00}-\x{0D7F}]/u', $textSample) || strtolower($q->language ?? '') === 'malayalam';
                            $isArabic = (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $textSample) || in_array(strtolower($q->language ?? ''), ['arabic', 'ar']);
                            $isUrdu = strtolower($q->language ?? '') === 'urdu';

                            $fontClass = $isMalayalam ? 'font-malayalam' : ($isUrdu ? 'font-urdu' : ($isArabic ? 'font-arabic' : 'font-eng'));
                        @endphp
                        <div class="fw-bold text-dark {{ $fontClass }}" style="{{ $isMalayalam ? 'line-height: 1.55; font-size: 0.95rem;' : ($isArabic || $isUrdu ? 'line-height: 1.7; font-size: 1.05rem;' : 'font-size: 0.92rem;') }}">
                            {{ $q->title ?: 'No Summary' }}
                        </div>
                        @if($q->body)
                            <small class="text-muted text-truncate d-inline-block {{ $fontClass }}" style="max-width: 320px; {{ $isMalayalam ? 'line-height: 1.45; font-size: 0.82rem;' : '' }}">
                                {{ Str::limit(strip_tags($q->body), 60) }}
                            </small>
                        @endif
                    </td>
                    <td>{{ $q->madhab?->name ?? '—' }}</td>
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
