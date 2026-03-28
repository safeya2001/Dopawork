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
        $categories = Category::with('children')->whereNull('parent_id')->orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::active()->whereNull('parent_id')->orderBy('sort_order')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'name_ar'   => 'required|string|max:100',
            'icon'      => 'nullable|string|max:10',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order'=> 'nullable|integer',
        ]);

        Category::create([
            'name'       => $request->name,
            'name_ar'    => $request->name_ar,
            'slug'       => Str::slug($request->name) . '-' . Str::random(4),
            'icon'       => $request->icon,
            'parent_id'  => $request->parent_id,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => true,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        $parents = Category::active()->whereNull('parent_id')->where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'name_ar'   => 'required|string|max:100',
            'icon'      => 'nullable|string|max:10',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order'=> 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name'       => $request->name,
            'name_ar'    => $request->name_ar,
            'icon'       => $request->icon,
            'parent_id'  => $request->parent_id,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
