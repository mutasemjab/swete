<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\ModuleController;
use App\Models\EquipmentCategory;
use App\Models\EquipmentSubtype;
use Illuminate\Http\Request;

class EquipmentSubtypeController extends ModuleController
{
    protected string $module = 'equipment';

    public function index()
    {
        $subtypes = EquipmentSubtype::with('category')->orderBy('name')->get();

        return $this->moduleView('equipment.subtypes.index', compact('subtypes'));
    }

    public function create()
    {
        return $this->moduleView('equipment.subtypes.create', [
            'subtype'    => null,
            'categories' => EquipmentCategory::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        EquipmentSubtype::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('equipment-subtypes.index')
            ->with('success', __('equipment.subtype_added'));
    }

    public function edit(EquipmentSubtype $equipmentSubtype)
    {
        return $this->moduleView('equipment.subtypes.edit', [
            'subtype'    => $equipmentSubtype,
            'categories' => EquipmentCategory::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, EquipmentSubtype $equipmentSubtype)
    {
        $validated = $this->validated($request);

        $equipmentSubtype->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('equipment-subtypes.index')
            ->with('success', __('equipment.subtype_updated'));
    }

    public function destroy(EquipmentSubtype $equipmentSubtype)
    {
        if ($equipmentSubtype->models()->exists() || $equipmentSubtype->units()->exists()) {
            return back()->with('error', __('equipment.subtype_in_use'));
        }

        $equipmentSubtype->delete();

        return redirect()->route('equipment-subtypes.index')
            ->with('success', __('equipment.subtype_deleted'));
    }

    private function validated(Request $request): array
    {
        $subtypeId = $request->route('equipment_subtype')?->id;

        return $request->validate([
            'equipment_category_id' => ['required', 'exists:equipment_categories,id'],
            'name'                  => [
                'required', 'string', 'max:150',
                "unique:equipment_subtypes,name,{$subtypeId},id,equipment_category_id,{$request->input('equipment_category_id')}",
            ],
            'name_en'                => ['nullable', 'string', 'max:150'],
        ]);
    }
}
