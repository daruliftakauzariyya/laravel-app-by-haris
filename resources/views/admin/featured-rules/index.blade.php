@extends('layouts.admin')
@section('title', 'Featured Fatwa Rules')
@section('topbar-title', 'Featured Fatwa Rules')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Smart Featured Fatwa Rules</h5>
        <p class="text-muted small mb-0">Rules matched by Hijri date, visitor location, or both — shown on the home page.</p>
    </div>
    <a href="{{ route('admin.featured-rules.create') }}" class="btn btn-success px-4 rounded-pill">
        <i class="bi bi-plus-circle me-2"></i>New Rule
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0 shadow-sm" style="border-radius:14px; overflow:hidden;">
    <div class="card-header bg-white border-0 py-3 px-4">
        <span class="small text-muted">Rules are evaluated in priority order (highest first). The first matching rule wins.</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-4" style="width:40px;">#</th>
                    <th>Rule Name</th>
                    <th>Conditions</th>
                    <th>Section Title</th>
                    <th>Limit</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rules as $rule)
                <tr>
                    <td class="ps-4 text-muted">{{ $rule->id }}</td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $rule->name }}</div>
                        @if($rule->category)
                            <span class="badge bg-light text-dark border x-small mt-1">
                                <i class="bi bi-folder2 me-1"></i>{{ $rule->category->name }}
                            </span>
                        @endif
                        @if($rule->madhab)
                            <span class="badge bg-light text-dark border x-small mt-1">
                                <i class="bi bi-bookmark me-1"></i>{{ $rule->madhab->name }}
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="text-muted small">{{ $rule->conditionSummary() }}</div>
                    </td>
                    <td class="text-muted">{{ Str::limit($rule->section_label, 40) }}</td>
                    <td><span class="badge bg-secondary">{{ $rule->limit }}</span></td>
                    <td>
                        <span class="fw-bold" style="color: #295a75;">{{ $rule->priority }}</span>
                    </td>
                    <td>
                        <form action="{{ route('admin.featured-rules.toggle', $rule) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm rounded-pill px-3 fw-semibold
                                {{ $rule->is_active ? 'btn-success' : 'btn-outline-secondary' }}">
                                {{ $rule->is_active ? '✓ Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.featured-rules.edit', $rule) }}"
                           class="btn btn-sm btn-outline-primary px-3 me-1">Edit</a>
                        <form action="{{ route('admin.featured-rules.destroy', $rule) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this rule?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger px-3">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-stars d-block fs-2 mb-2 opacity-25"></i>
                        No rules yet. Create your first smart featured rule.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4 p-4" style="border-radius:14px; background:linear-gradient(135deg,#f0f9ff,#e0f2fe);">
    <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb me-2 text-warning"></i>How Rules Work</h6>
    <div class="row g-3 small text-dark">
        <div class="col-md-4">
            <div class="d-flex gap-2">
                <span class="fs-4">📅</span>
                <div><strong>Hijri Date</strong><br>Match by Islamic month (e.g., Ramadan = month 9) with optional day range.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex gap-2">
                <span class="fs-4">🌍</span>
                <div><strong>Location</strong><br>Match by visitor's country (Saudi Arabia, India, Pakistan, etc.).</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex gap-2">
                <span class="fs-4">🔗</span>
                <div><strong>Combined</strong><br>Use 2 or 3 conditions together. All must match for the rule to apply.</div>
            </div>
        </div>
    </div>
</div>

@endsection
