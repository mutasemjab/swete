<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\ModuleController;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;

/** شجرة العملاء */
class CustomerGroupController extends ModuleController
{
    protected string $module = 'accounting';

    public function index()
    {
        $groups = CustomerGroup::with('parent')->orderBy('name')->get();
        return $this->moduleView('accounting.customer-groups.index', compact('groups'));
    }

    public function create()
    {
        $parents = CustomerGroup::orderBy('name')->get();
        return $this->moduleView('accounting.customer-groups.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:customer_groups,id'],
            'name'      => ['required', 'string', 'max:150'],
            'name_en'   => ['nullable', 'string', 'max:150'],
            'status'    => ['boolean'],
        ]);

        CustomerGroup::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('accounting.customer-groups.index')
            ->with('success', __('accounting.group_added'));
    }

    public function edit(CustomerGroup $customerGroup)
    {
        $parents = CustomerGroup::where('id', '!=', $customerGroup->id)->orderBy('name')->get();
        return $this->moduleView('accounting.customer-groups.edit', ['group' => $customerGroup, 'parents' => $parents]);
    }

    public function update(Request $request, CustomerGroup $customerGroup)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:customer_groups,id'],
            'name'      => ['required', 'string', 'max:150'],
            'name_en'   => ['nullable', 'string', 'max:150'],
            'status'    => ['boolean'],
        ]);

        if ((int) ($validated['parent_id'] ?? 0) === $customerGroup->id) {
            return back()->withErrors(['parent_id' => __('accounting.group_cannot_be_own_parent')])->withInput();
        }

        $customerGroup->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('accounting.customer-groups.index')
            ->with('success', __('accounting.group_updated'));
    }

    public function destroy(CustomerGroup $customerGroup)
    {
        $customerGroup->delete();

        return redirect()->route('accounting.customer-groups.index')
            ->with('success', __('accounting.group_deleted'));
    }
}
