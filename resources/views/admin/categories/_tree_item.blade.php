<div class="tree-node" data-id="{{ $category->id }}">
    <div class="tree-item d-flex align-items-center justify-content-between p-2 rounded hover-shadow-sm mb-1" style="border: 1px solid #eef2f7; background: #fff;">
        <div class="d-flex align-items-center flex-grow-1">
            <div class="tree-toggle me-2" style="width: 20px;">
                @if($category->children->count() > 0)
                    <i class="bi bi-chevron-down text-brand cursor-pointer toggle-trigger"></i>
                @else
                    <i class="bi bi-dot text-muted"></i>
                @endif
            </div>
            
            <div class="tree-info d-flex align-items-center flex-grow-1 gap-2">
                <span class="fw-bold text-dark">{{ $category->name }}</span>
                <span class="text-muted small d-none d-md-inline">—</span>
                <span class="arabic-text small text-brand" dir="rtl">{{ $category->name_ar }}</span>
                
                @if(!$category->is_active)
                    <span class="badge bg-secondary-subtle text-secondary small ms-2">Inactive</span>
                @endif
            </div>
        </div>

        <div class="tree-actions d-flex gap-1">
            <button class="btn btn-sm btn-icon btn-light text-brand" title="Edit" 
                    onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->name_ar) }}', '{{ addslashes($category->name_ur) }}', {{ $category->is_active ? 1 : 0 }}, {{ $category->parent_id ?? 'null' }})">
                <i class="bi bi-pencil-square"></i>
            </button>
            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('Delete this category and all its subcategories?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-icon btn-light text-danger" title="Delete">
                    <i class="bi bi-trash3"></i>
                </button>
            </form>
        </div>
    </div>

    @if($category->children->count() > 0)
        <div class="tree-children ps-4 ms-2 border-start" style="border-color: #dee2e6 !important;">
            @foreach($category->children as $child)
                @include('admin.categories._tree_item', ['category' => $child])
            @endforeach
        </div>
    @endif
</div>
