<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends ModuleController
{
    protected string $module = 'settings';

    public function index()
    {
        $branches = Branch::orderByDesc('is_main')->orderBy('name')->get();
        return $this->moduleView('settings.branches.index', compact('branches'));
    }

    public function create()
    {
        return $this->moduleView('settings.branches.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        if ($request->boolean('is_main')) {
            Branch::where('is_main', true)->update(['is_main' => false]);
        }

        Branch::create([
            ...$validated,
            'is_main' => $request->boolean('is_main'),
            'status'  => $request->boolean('status', true),
        ]);

        return redirect()->route('settings.branches.index')
            ->with('success', __('settings.branch_added'));
    }

    public function edit(Branch $branch)
    {
        return $this->moduleView('settings.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $this->validated($request);

        if ($request->boolean('is_main') && ! $branch->is_main) {
            Branch::where('is_main', true)->update(['is_main' => false]);
        }

        $branch->update([
            ...$validated,
            'is_main' => $request->boolean('is_main'),
            'status'  => $request->boolean('status'),
        ]);

        return redirect()->route('settings.branches.index')
            ->with('success', __('settings.branch_updated'));
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->route('settings.branches.index')
            ->with('success', __('settings.branch_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'              => ['required', 'string', 'max:100'],
            'name_en'           => ['nullable', 'string', 'max:100'],
            'phone'             => ['nullable', 'string', 'max:30'],
            'fax'               => ['nullable', 'string', 'max:30'],
            'address_line1'     => ['nullable', 'string', 'max:255'],
            'address_line1_en'  => ['nullable', 'string', 'max:255'],
            'po_box'            => ['nullable', 'string', 'max:30'],
            'postal_code'       => ['nullable', 'string', 'max:30'],
            'city'              => ['nullable', 'string', 'max:100'],
            'city_en'           => ['nullable', 'string', 'max:100'],
            'country'           => ['nullable', 'string', 'max:100'],
            'country_en'        => ['nullable', 'string', 'max:100'],
            'is_main'           => ['boolean'],
            'status'            => ['boolean'],
        ]);
    }
}
