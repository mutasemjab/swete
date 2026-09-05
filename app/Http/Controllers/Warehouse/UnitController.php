<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\ModuleController;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends ModuleController
{
    protected string $module = 'warehouse';

    public function index()
    {
        $units = Unit::orderBy('name')->get();
        return $this->moduleView('warehouse.units.index', compact('units'));
    }

    public function create()
    {
        return $this->moduleView('warehouse.units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'name_en' => ['nullable', 'string', 'max:100'],
            'symbol'  => ['nullable', 'string', 'max:20'],
            'status'  => ['boolean'],
        ]);

        Unit::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('warehouse.units.index')
            ->with('success', __('warehouse.unit_added'));
    }

    public function edit(Unit $unit)
    {
        return $this->moduleView('warehouse.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'name_en' => ['nullable', 'string', 'max:100'],
            'symbol'  => ['nullable', 'string', 'max:20'],
            'status'  => ['boolean'],
        ]);

        $unit->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('warehouse.units.index')
            ->with('success', __('warehouse.unit_updated'));
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('warehouse.units.index')
            ->with('success', __('warehouse.unit_deleted'));
    }
}
