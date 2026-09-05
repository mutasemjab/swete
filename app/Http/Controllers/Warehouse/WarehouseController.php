<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\ModuleController;
use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends ModuleController
{
    protected string $module = 'warehouse';

    public function index()
    {
        $warehouses = Warehouse::with('branch')->orderBy('name')->get();
        return $this->moduleView('warehouse.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        return $this->moduleView('warehouse.warehouses.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'name'      => ['required', 'string', 'max:100'],
            'name_en'   => ['nullable', 'string', 'max:100'],
            'code'      => ['required', 'string', 'max:30', 'unique:warehouses,code'],
            'is_main'   => ['boolean'],
            'status'    => ['boolean'],
        ]);

        Warehouse::create([
            ...$validated,
            'is_main' => $request->boolean('is_main'),
            'status'  => $request->boolean('status', true),
        ]);

        return redirect()->route('warehouse.warehouses.index')
            ->with('success', __('warehouse.warehouse_added'));
    }

    public function edit(Warehouse $warehouse)
    {
        $branches = Branch::orderBy('name')->get();
        return $this->moduleView('warehouse.warehouses.edit', compact('warehouse', 'branches'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'name'      => ['required', 'string', 'max:100'],
            'name_en'   => ['nullable', 'string', 'max:100'],
            'code'      => ['required', 'string', 'max:30', "unique:warehouses,code,{$warehouse->id}"],
            'is_main'   => ['boolean'],
            'status'    => ['boolean'],
        ]);

        $warehouse->update([
            ...$validated,
            'is_main' => $request->boolean('is_main'),
            'status'  => $request->boolean('status'),
        ]);

        return redirect()->route('warehouse.warehouses.index')
            ->with('success', __('warehouse.warehouse_updated'));
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()->route('warehouse.warehouses.index')
            ->with('success', __('warehouse.warehouse_deleted'));
    }
}
