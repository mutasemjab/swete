<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\ModuleController;
use App\Models\SupplierGroup;
use Illuminate\Http\Request;

/** شجرة الموردين */
class SupplierGroupController extends ModuleController
{
    protected string $module = 'accounting';

    public function index()
    {
        $groups = SupplierGroup::with('parent')->orderBy('name')->get();
        return $this->moduleView('accounting.supplier-groups.index', compact('groups'));
    }

    public function create()
    {
        $parents = SupplierGroup::orderBy('name')->get();
        return $this->moduleView('accounting.supplier-groups.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:supplier_groups,id'],
            'name'      => ['required', 'string', 'max:150'],
            'name_en'   => ['nullable', 'string', 'max:150'],
            'status'    => ['boolean'],
        ]);

        SupplierGroup::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('accounting.supplier-groups.index')
            ->with('success', __('accounting.group_added'));
    }

    public function edit(SupplierGroup $supplierGroup)
    {
        $parents = SupplierGroup::where('id', '!=', $supplierGroup->id)->orderBy('name')->get();
        return $this->moduleView('accounting.supplier-groups.edit', ['group' => $supplierGroup, 'parents' => $parents]);
    }

    public function update(Request $request, SupplierGroup $supplierGroup)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:supplier_groups,id'],
            'name'      => ['required', 'string', 'max:150'],
            'name_en'   => ['nullable', 'string', 'max:150'],
            'status'    => ['boolean'],
        ]);

        if ((int) ($validated['parent_id'] ?? 0) === $supplierGroup->id) {
            return back()->withErrors(['parent_id' => __('accounting.group_cannot_be_own_parent')])->withInput();
        }

        $supplierGroup->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('accounting.supplier-groups.index')
            ->with('success', __('accounting.group_updated'));
    }

    public function destroy(SupplierGroup $supplierGroup)
    {
        $supplierGroup->delete();

        return redirect()->route('accounting.supplier-groups.index')
            ->with('success', __('accounting.group_deleted'));
    }
}
