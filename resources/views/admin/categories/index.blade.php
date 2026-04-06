@extends('layouts.admin')
@section('title', 'Categories')
@section('topbar-title', 'Category Management')
@section('content')
<div class="row g-4">
    {{-- Add Category --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-2"><h6 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Add Category</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Name (English) *</label>
                        <input type="text" name="name" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Name (Arabic)</label>
                        <input type="text" name="name_ar" class="form-control form-control-sm" dir="rtl">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Name (Urdu)</label>
                        <input type="text" name="name_ur" class="form-control form-control-sm" dir="rtl">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Parent (Subcategory of)</label>
                        <select name="parent_id" class="form-select form-select-sm">
                            <option value="">— Top Level —</option>
                            @foreach($categories->whereNull('parent_id') as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Add Category</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Category List --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr><th>Name</th><th>Arabic</th><th>Urdu</th><th>Parent</th><th>Active</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                        <tr>
                            <td><strong>{{ $cat->name }}</strong></td>
                            <td dir="rtl">{{ $cat->name_ar }}</td>
                            <td dir="rtl">{{ $cat->name_ur }}</td>
                            <td>{{ $cat->parent?->name ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $cat->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="d-inline"
                                      onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No categories yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
