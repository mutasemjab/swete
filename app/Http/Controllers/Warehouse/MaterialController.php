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
        $query = Material::with(['category', 'unit'])->confirmed();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%"));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $materials   = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories  = MaterialCategory::orderBy('name')->get();
        $draftsCount = Material::where('is_draft', true)->count();

        return $this->moduleView('warehouse.materials.index', compact('materials', 'categories', 'draftsCount'));
    }

    /** The "السلع المؤقتة" cleanup screen: every not-yet-promoted draft material, pick some/all and delete. */
    public function drafts()
    {
        $materials = Material::with(['category', 'unit'])->where('is_draft', true)->orderByDesc('created_at')->get();

        return $this->moduleView('warehouse.materials.drafts', compact('materials'));
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'material_ids'   => ['required', 'array', 'min:1'],
            'material_ids.*' => ['exists:materials,id'],
        ]);

        $deleted = 0;
        $blocked = 0;

        foreach (Material::where('is_draft', true)->whereIn('id', $validated['material_ids'])->get() as $material) {
            try {
                $material->delete();
                $deleted++;
            } catch (\Illuminate\Database\QueryException $e) {
                // Still referenced by a saved price quote/analysis line — leave it, don't fail the whole batch.
                $blocked++;
            }
        }

        $message = __('warehouse.drafts_deleted', ['count' => $deleted]);

        if ($blocked > 0) {
            $message .= ' ' . __('warehouse.drafts_delete_blocked', ['count' => $blocked]);
        }

        return back()->with('success', $message);
    }

    /**
     * AJAX quick-add shared by the Price Quote, Split Unit, and CIAT Discount screens.
     * Price Quote/Split Unit callers (the default) get a DRAFT — not yet a real catalog material,
     * since the tender it's priced for may never actually be won.
     * The CIAT Discount screen passes `is_ciat: true` instead: that material IS the real thing
     * being added to the warehouse (just for use in a CIAT discount), so it's created as a real,
     * non-draft material flagged `is_ciat`, never dropped into "السلع المؤقتة" (draft cleanup).
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:material_categories,id'],
            'unit_id'     => ['required', 'exists:units,id'],
            'is_ciat'     => ['boolean'],
        ]);

        $isCiat = $request->boolean('is_ciat');

        $material = Material::create([
            'name'        => $validated['name'],
            'category_id' => $validated['category_id'],
            'unit_id'     => $validated['unit_id'],
            'code'        => $isCiat ? Material::nextCiatCode() : Material::nextDraftCode(),
            'status'      => true,
            'is_draft'    => ! $isCiat,
            'is_ciat'     => $isCiat,
        ]);

        return response()->json(['id' => $material->id, 'name' => $material->localized_name, 'code' => $material->code]);
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

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = 'assets/uploads/materials/' . uploadImage('assets/uploads/materials', $request->file('photo'));
        }

        Material::create([
            ...$validated,
            'status'   => $request->boolean('status', true),
            'is_draft' => false,
            'is_ciat'  => $request->boolean('is_ciat'),
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

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = 'assets/uploads/materials/' . uploadImage('assets/uploads/materials', $request->file('photo'));
        }

        $material->update([
            ...$validated,
            'status'  => $request->boolean('status'),
            'is_ciat' => $request->boolean('is_ciat'),
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
            'photo'            => ['nullable', 'image', 'max:5120'],
            'min_stock_level'  => ['nullable', 'numeric', 'min:0'],
            'status'           => ['boolean'],
            'is_ciat'          => ['boolean'],
        ]);
    }
}
