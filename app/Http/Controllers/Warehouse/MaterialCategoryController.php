<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\ModuleController;
use App\Models\MaterialCategory;
use Illuminate\Http\Request;

class MaterialCategoryController extends ModuleController
{
    protected string $module = 'warehouse';

    public function index()
    {
        $categories = MaterialCategory::with('parent')->orderBy('name')->get();
        return $this->moduleView('warehouse.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = MaterialCategory::orderBy('name')->get();
        return $this->moduleView('warehouse.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:material_categories,id'],
            'name'      => ['required', 'string', 'max:100'],
            'name_en'   => ['nullable', 'string', 'max:100'],
            'code'      => ['nullable', 'string', 'max:30', 'unique:material_categories,code'],
            'status'    => ['boolean'],
        ]);

        MaterialCategory::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('warehouse.categories.index')
            ->with('success', __('warehouse.category_added'));
    }

    public function edit(MaterialCategory $category)
    {
        $parents = MaterialCategory::where('id', '!=', $category->id)->orderBy('name')->get();
        return $this->moduleView('warehouse.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, MaterialCategory $category)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:material_categories,id'],
            'name'      => ['required', 'string', 'max:100'],
            'name_en'   => ['nullable', 'string', 'max:100'],
            'code'      => ['nullable', 'string', 'max:30', "unique:material_categories,code,{$category->id}"],
            'status'    => ['boolean'],
        ]);

        if ((int) ($validated['parent_id'] ?? 0) === $category->id) {
            return back()->withErrors(['parent_id' => __('warehouse.category_cannot_be_own_parent')])->withInput();
        }

        $category->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('warehouse.categories.index')
            ->with('success', __('warehouse.category_updated'));
    }

    public function destroy(MaterialCategory $category)
    {
        $category->delete();

        return redirect()->route('warehouse.categories.index')
            ->with('success', __('warehouse.category_deleted'));
    }
}
