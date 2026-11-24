<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\ModuleController;
use App\Models\EquipmentManufacturer;
use Illuminate\Http\Request;

class EquipmentManufacturerController extends ModuleController
{
    protected string $module = 'equipment';

    public function index()
    {
        $manufacturers = EquipmentManufacturer::orderBy('name')->get();

        return $this->moduleView('equipment.manufacturers.index', compact('manufacturers'));
    }

    public function create()
    {
        return $this->moduleView('equipment.manufacturers.create', ['manufacturer' => null]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        EquipmentManufacturer::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('equipment-manufacturers.index')
            ->with('success', __('equipment.manufacturer_added'));
    }

    public function edit(EquipmentManufacturer $equipmentManufacturer)
    {
        return $this->moduleView('equipment.manufacturers.edit', ['manufacturer' => $equipmentManufacturer]);
    }

    public function update(Request $request, EquipmentManufacturer $equipmentManufacturer)
    {
        $validated = $this->validated($request);

        $equipmentManufacturer->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('equipment-manufacturers.index')
            ->with('success', __('equipment.manufacturer_updated'));
    }

    public function destroy(EquipmentManufacturer $equipmentManufacturer)
    {
        if ($equipmentManufacturer->models()->exists()) {
            return back()->with('error', __('equipment.manufacturer_in_use'));
        }

        $equipmentManufacturer->delete();

        return redirect()->route('equipment-manufacturers.index')
            ->with('success', __('equipment.manufacturer_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'    => ['required', 'string', 'max:150', 'unique:equipment_manufacturers,name,'.$request->route('equipment_manufacturer')?->id],
            'name_en' => ['nullable', 'string', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
        ]);
    }
}
