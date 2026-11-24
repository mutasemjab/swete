<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\ModuleController;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;

class EquipmentCategoryController extends ModuleController
{
    protected string $module = 'equipment';

    public function index()
    {
        $categories = EquipmentCategory::orderBy('name')->get();

        return $this->moduleView('equipment.categories.index', compact('categories'));
    }

    public function create()
    {
        return $this->moduleView('equipment.categories.create', ['category' => null]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        EquipmentCategory::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('equipment-categories.index')
            ->with('success', __('equipment.category_added'));
    }

    public function edit(EquipmentCategory $equipmentCategory)
    {
        return $this->moduleView('equipment.categories.edit', ['category' => $equipmentCategory]);
    }

    public function update(Request $request, EquipmentCategory $equipmentCategory)
    {
        $validated = $this->validated($request);

        $equipmentCategory->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('equipment-categories.index')
            ->with('success', __('equipment.category_updated'));
    }

    public function destroy(EquipmentCategory $equipmentCategory)
    {
        if ($equipmentCategory->subtypes()->exists() || $equipmentCategory->models()->exists() || $equipmentCategory->units()->exists()) {
            return back()->with('error', __('equipment.category_in_use'));
        }

        $equipmentCategory->delete();

        return redirect()->route('equipment-categories.index')
            ->with('success', __('equipment.category_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'    => ['required', 'string', 'max:150', 'unique:equipment_categories,name,'.$request->route('equipment_category')?->id],
            'name_en' => ['nullable', 'string', 'max:150'],
        ]);
    }
}
