<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->orderBy('parent_id')->orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'name_ar'   => ['nullable', 'string', 'max:100'],
            'name_ur'   => ['nullable', 'string', 'max:100'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        Category::create([
            'name'      => $request->name,
            'name_ar'   => $request->name_ar,
            'name_ur'   => $request->name_ur,
            'parent_id' => $request->parent_id,
            'slug'      => Str::slug($request->name) . '-' . uniqid(),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'name_ar' => ['nullable', 'string'],
            'name_ur' => ['nullable', 'string'],
        ]);

        $category->update($request->only('name', 'name_ar', 'name_ur', 'is_active'));
        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }
}
