<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\ModuleController;
use App\Models\PartyGroup;
use Illuminate\Http\Request;

/** Shared controller for both the supplier tree (شجرة الموردين) and the customer tree (شجرة العملاء). */
class PartyGroupController extends ModuleController
{
    protected string $module = 'accounting';

    public function index(string $type)
    {
        $groups = PartyGroup::ofType($type)->with('parent')->orderBy('name')->get();
        return $this->moduleView('accounting.party-groups.index', compact('groups', 'type'));
    }

    public function create(string $type)
    {
        $parents = PartyGroup::ofType($type)->orderBy('name')->get();
        return $this->moduleView('accounting.party-groups.create', compact('parents', 'type'));
    }

    public function store(Request $request, string $type)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:party_groups,id'],
            'name'      => ['required', 'string', 'max:150'],
            'name_en'   => ['nullable', 'string', 'max:150'],
            'status'    => ['boolean'],
        ]);

        PartyGroup::create([
            ...$validated,
            'type'   => $type,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route("accounting.{$type}-groups.index")
            ->with('success', __('accounting.group_added'));
    }

    public function edit(string $type, PartyGroup $group)
    {
        $parents = PartyGroup::ofType($type)->where('id', '!=', $group->id)->orderBy('name')->get();
        return $this->moduleView('accounting.party-groups.edit', compact('group', 'parents', 'type'));
    }

    public function update(Request $request, string $type, PartyGroup $group)
    {
        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:party_groups,id'],
            'name'      => ['required', 'string', 'max:150'],
            'name_en'   => ['nullable', 'string', 'max:150'],
            'status'    => ['boolean'],
        ]);

        if ((int) ($validated['parent_id'] ?? 0) === $group->id) {
            return back()->withErrors(['parent_id' => __('accounting.group_cannot_be_own_parent')])->withInput();
        }

        $group->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route("accounting.{$type}-groups.index")
            ->with('success', __('accounting.group_updated'));
    }

    public function destroy(string $type, PartyGroup $group)
    {
        $group->delete();

        return redirect()->route("accounting.{$type}-groups.index")
            ->with('success', __('accounting.group_deleted'));
    }
}
