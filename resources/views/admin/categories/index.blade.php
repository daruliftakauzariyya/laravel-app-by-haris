@extends('layouts.admin')
@section('title', 'Categories')
@section('topbar-title', 'Category Tree Management')
@section('topbar-icon', 'bi-tags-fill')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
    <span class="text-dark fw-semibold">Categories</span>
@endsection

@push('styles')
<style>
    .tree-container { background: #ffffff; padding: 1.5rem; border-radius: 14px; }
    .tree-node { position: relative; }
    .tree-children { transition: all 0.3s ease-in-out; }
    .tree-info .arabic-text { font-family: 'Noto Naskh Arabic', serif; }
    .cursor-pointer { cursor: pointer; }
    .hover-shadow-sm:hover { box-shadow: 0 4px 12px rgba(16, 43, 67, 0.08); border-color: #cbd5e1 !important; }
    .btn-icon { width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px; }
    
    /* Tree Vertical Line Alignment */
    .tree-children { border-left: 2px dashed #E2E8F0 !important; }
    
    .rotate-n90 { transform: rotate(-90deg); }
    .collapsed-tree { display: none; }
</style>
@endpush

@section('content')
<div class="row g-4">
    {{-- Manage Category Form (Add/Edit) --}}
    <div class="col-lg-4">
        <div class="modern-card p-4 sticky-top" style="top: 85px;">
            <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                <div class="d-inline-flex p-2 rounded-3" style="background: #EFF6FF; color: #2563EB;">
                    <i class="bi bi-folder-plus fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark" id="form-title">Add New Category</h6>
                    <span class="text-muted small">Configure hierarchy & names</span>
                </div>
            </div>
            <div>
                <form id="category-form" method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Category Name (English) *</label>
                        <input type="text" name="name" id="field-name" class="modern-input" placeholder="e.g. Marriage" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Arabic Name (Arabic)</label>
                        <input type="text" name="name_ar" id="field-name-ar" class="modern-input arabic-text" dir="rtl" placeholder="النكاح">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Urdu Name (Urdu)</label>
                        <input type="text" name="name_ur" id="field-name-ur" class="modern-input" dir="rtl" placeholder="نکاح">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Parent Category</label>
                        <select name="parent_id" id="field-parent" class="modern-select">
                            <option value="">— Top Level (Root) —</option>
                            @foreach(\App\Models\Category::whereNull('parent_id')->orderBy('name')->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @foreach($c->children as $sub)
                                    <option value="{{ $sub->id }}">-- {{ $sub->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                        <div class="form-text small text-muted">Select where this category belongs in the tree.</div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="field-active" value="1" checked>
                            <label class="form-check-label small fw-bold text-dark" for="field-active">Active on Public Site</label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-modern-primary py-2.5" id="btn-submit">Save Category</button>
                        <button type="button" class="btn btn-modern-secondary py-2.5 d-none" id="btn-cancel" onclick="resetForm()">Cancel Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tree View List --}}
    <div class="col-lg-8">
        <div class="modern-card tree-container">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Hierarchical Category Tree</h6>
                    <span class="text-muted small">Nested catalog of fatwa topics</span>
                </div>
                <div class="small text-muted"><i class="bi bi-info-circle me-1 text-primary"></i>Click arrows to toggle branches</div>
            </div>
            
            <div class="tree-root">
                @forelse($categories as $category)
                    @include('admin.categories._tree_item', ['category' => $category])
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-folder2-open display-4 text-muted d-block mb-2"></i>
                        <p class="text-muted">No categories created yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Logic
        document.querySelectorAll('.toggle-trigger').forEach(trigger => {
            trigger.addEventListener('click', function() {
                const node = this.closest('.tree-node');
                const children = node.querySelector('.tree-children');
                if (children) {
                    children.classList.toggle('collapsed-tree');
                    this.classList.toggle('rotate-n90');
                }
            });
        });
    });

    function editCategory(id, name, nameAr, nameUr, isActive, parentId) {
        // Update Form for Editing
        const form = document.getElementById('category-form');
        const title = document.getElementById('form-title');
        const btnSubmit = document.getElementById('btn-submit');
        const btnCancel = document.getElementById('btn-cancel');
        const methodField = document.getElementById('form-method');

        title.innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Category';
        form.action = `/admin/categories/${id}`;
        methodField.value = 'PUT';
        btnSubmit.innerText = 'Update Category';
        btnCancel.classList.remove('d-none');

        // Fill Fields
        document.getElementById('field-name').value = name;
        document.getElementById('field-name-ar').value = nameAr;
        document.getElementById('field-name-ur').value = nameUr;
        document.getElementById('field-parent').value = parentId === null ? '' : parentId;
        document.getElementById('field-active').checked = isActive === 1;
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        const form = document.getElementById('category-form');
        const title = document.getElementById('form-title');
        const btnSubmit = document.getElementById('btn-submit');
        const btnCancel = document.getElementById('btn-cancel');
        const methodField = document.getElementById('form-method');

        form.reset();
        title.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add New Category';
        form.action = "{{ route('admin.categories.store') }}";
        methodField.value = 'POST';
        btnSubmit.innerText = 'Save Category';
        btnCancel.classList.add('d-none');
    }
</script>
@endpush
@endsection
