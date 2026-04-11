@extends('layouts.admin')
@section('title', 'Madhab Management')
@section('topbar-title', 'Madhab Settings')

@section('content')
<div class="row g-4">
    {{-- Manage Madhab Form --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 85px;">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold" id="form-title"><i class="bi bi-plus-circle me-2"></i>Add New Madhab</h6>
            </div>
            <div class="card-body">
                <form id="madhab-form" method="POST" action="{{ route('admin.madhabs.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Madhab Name *</label>
                        <input type="text" name="name" id="field-name" class="form-control form-control-sm" placeholder="e.g. Hanafi" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-brand btn-sm" id="btn-submit">Save Madhab</button>
                        <button type="button" class="btn btn-light btn-sm d-none" id="btn-cancel" onclick="resetForm()">Cancel Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Madhab List --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 border-0 small text-uppercase text-muted">Madhab Name</th>
                                <th class="border-0 small text-uppercase text-muted">Stats</th>
                                <th class="text-end pe-4 border-0 small text-uppercase text-muted">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($madhabs as $madhab)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $madhab->name }}</div>
                                        <div class="small text-muted text-uppercase" style="font-size: 0.7rem;">SLUG: {{ $madhab->slug }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-3">
                                            <span class="badge bg-soft-brand text-brand small rounded-pill px-2">
                                                <i class="bi bi-question-circle me-1"></i>{{ $madhab->questions_count }} Questions
                                            </span>
                                            <span class="badge bg-soft-info text-info small rounded-pill px-2">
                                                <i class="bi bi-person-badge me-1"></i>{{ $madhab->muftis_count }} Muftis
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <button class="btn btn-soft-brand btn-icon" onclick="editMadhab({{ $madhab->id }}, '{{ $madhab->name }}')">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('admin.madhabs.destroy', $madhab) }}" method="POST" onsubmit="return confirm('Delete this Madhab? (Only possible if not assigned)')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-soft-danger btn-icon">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <p class="text-muted mb-0">No Madhabs found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($madhabs->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $madhabs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .btn-icon { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 0.9rem; }
    .bg-soft-brand { background-color: rgba(5, 41, 97, 0.08); color: #052961; }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.08); color: #0dcaf0; }
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.08); color: #dc3545; }
    .btn-soft-brand { background-color: rgba(5, 41, 97, 0.1); color: #052961; border: none; }
    .btn-soft-brand:hover { background-color: #052961; color: #fff; }
    .btn-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; border: none; }
    .btn-soft-danger:hover { background-color: #dc3545; color: #fff; }
</style>
@endpush

@push('scripts')
<script>
    function editMadhab(id, name) {
        const form = document.getElementById('madhab-form');
        const title = document.getElementById('form-title');
        const btnSubmit = document.getElementById('btn-submit');
        const btnCancel = document.getElementById('btn-cancel');
        const methodField = document.getElementById('form-method');

        title.innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Madhab';
        form.action = `/admin/madhabs/${id}`;
        methodField.value = 'PUT';
        btnSubmit.innerText = 'Update Madhab';
        btnCancel.classList.remove('d-none');

        document.getElementById('field-name').value = name;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        const form = document.getElementById('madhab-form');
        const title = document.getElementById('form-title');
        const btnSubmit = document.getElementById('btn-submit');
        const btnCancel = document.getElementById('btn-cancel');
        const methodField = document.getElementById('form-method');

        form.reset();
        title.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add New Madhab';
        form.action = "{{ route('admin.madhabs.store') }}";
        methodField.value = 'POST';
        btnSubmit.innerText = 'Save Madhab';
        btnCancel.classList.add('d-none');
    }
</script>
@endpush
@endsection
