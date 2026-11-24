<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\ModuleController;
use App\Models\EquipmentCategory;
use App\Models\EquipmentManufacturer;
use App\Models\EquipmentModel;
use App\Models\EquipmentSubtype;
use Illuminate\Http\Request;

class EquipmentModelController extends ModuleController
{
    protected string $module = 'equipment';

    public function index(Request $request)
    {
        $query = EquipmentModel::with(['manufacturer', 'category', 'subtype']);

        if ($request->filled('equipment_manufacturer_id')) {
            $query->where('equipment_manufacturer_id', $request->input('equipment_manufacturer_id'));
        }

        $models = $query->orderBy('model_number')->paginate(20)->withQueryString();
        $manufacturers = EquipmentManufacturer::where('status', true)->orderBy('name')->get();

        return $this->moduleView('equipment.models.index', compact('models', 'manufacturers'));
    }

    public function create()
    {
        return $this->moduleView('equipment.models.create', [
            'model' => null,
        ] + $this->pickers());
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        EquipmentModel::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('equipment-models.index')
            ->with('success', __('equipment.model_added'));
    }

    public function edit(EquipmentModel $equipmentModel)
    {
        return $this->moduleView('equipment.models.edit', [
            'model' => $equipmentModel,
        ] + $this->pickers());
    }

    public function update(Request $request, EquipmentModel $equipmentModel)
    {
        $validated = $this->validated($request);

        $equipmentModel->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('equipment-models.index')
            ->with('success', __('equipment.model_updated'));
    }

    public function destroy(EquipmentModel $equipmentModel)
    {
        if ($equipmentModel->units()->exists()) {
            return back()->with('error', __('equipment.model_in_use'));
        }

        $equipmentModel->delete();

        return redirect()->route('equipment-models.index')
            ->with('success', __('equipment.model_deleted'));
    }

    private function pickers(): array
    {
        return [
            'manufacturers' => EquipmentManufacturer::where('status', true)->orderBy('name')->get(),
            'categories'    => EquipmentCategory::where('status', true)->orderBy('name')->get(),
            'subtypes'      => EquipmentSubtype::where('status', true)->orderBy('name')->get(['id', 'name', 'name_en', 'equipment_category_id']),
        ];
    }

    private function validated(Request $request): array
    {
        $modelId = $request->route('equipment_model')?->id;

        return $request->validate([
            'equipment_manufacturer_id' => ['required', 'exists:equipment_manufacturers,id'],
            'equipment_category_id'     => ['required', 'exists:equipment_categories,id'],
            'equipment_subtype_id'      => ['nullable', 'exists:equipment_subtypes,id'],
            'model_number'              => [
                'required', 'string', 'max:150',
                "unique:equipment_models,model_number,{$modelId},id,equipment_manufacturer_id,{$request->input('equipment_manufacturer_id')}",
            ],
        ]);
    }
}
