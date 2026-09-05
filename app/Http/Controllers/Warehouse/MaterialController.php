<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\ModuleController;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Unit;
use Illuminate\Http\Request;

class MaterialController extends ModuleController
{
    protected string $module = 'warehouse';

    public function index(Request $request)
    {
        $query = Material::with(['category', 'unit']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%"));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $materials  = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = MaterialCategory::orderBy('name')->get();

        return $this->moduleView('warehouse.materials.index', compact('materials', 'categories'));
    }

    public function create()
    {
        $categories = MaterialCategory::orderBy('name')->get();
        $units      = Unit::where('status', true)->orderBy('name')->get();
        return $this->moduleView('warehouse.materials.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        Material::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('warehouse.materials.index')
            ->with('success', __('warehouse.material_added'));
    }

    public function show(Material $material)
    {
        $material->load(['category', 'unit', 'stocks.warehouse']);
        return $this->moduleView('warehouse.materials.show', compact('material'));
    }

    public function edit(Material $material)
    {
        $categories = MaterialCategory::orderBy('name')->get();
        $units      = Unit::orderBy('name')->get();
        return $this->moduleView('warehouse.materials.edit', compact('material', 'categories', 'units'));
    }

    public function update(Request $request, Material $material)
    {
        $validated = $this->validated($request, $material->id);

        $material->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('warehouse.materials.index')
            ->with('success', __('warehouse.material_updated'));
    }

    public function destroy(Material $material)
    {
        $material->delete();

        return redirect()->route('warehouse.materials.index')
            ->with('success', __('warehouse.material_deleted'));
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'category_id'      => ['required', 'exists:material_categories,id'],
            'unit_id'          => ['required', 'exists:units,id'],
            'code'             => ['required', 'string', 'max:50', 'unique:materials,code' . ($ignoreId ? ",{$ignoreId}" : '')],
            'name'             => ['required', 'string', 'max:150'],
            'name_en'          => ['nullable', 'string', 'max:150'],
            'description'      => ['nullable', 'string'],
            'min_stock_level'  => ['nullable', 'numeric', 'min:0'],
            'status'           => ['boolean'],
        ]);
    }
}
