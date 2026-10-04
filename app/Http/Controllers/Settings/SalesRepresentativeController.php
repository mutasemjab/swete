<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\SalesRepresentative;
use Illuminate\Http\Request;

class SalesRepresentativeController extends ModuleController
{
    protected string $module = 'settings';

    public function index()
    {
        $salesRepresentatives = SalesRepresentative::orderBy('name')->get();

        return $this->moduleView('settings.sales-representatives.index', compact('salesRepresentatives'));
    }

    public function create()
    {
        return $this->moduleView('settings.sales-representatives.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        SalesRepresentative::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('settings.sales-representatives.index')
            ->with('success', __('settings.sales_rep_added'));
    }

    public function edit(SalesRepresentative $salesRepresentative)
    {
        return $this->moduleView('settings.sales-representatives.edit', ['salesRep' => $salesRepresentative]);
    }

    public function update(Request $request, SalesRepresentative $salesRepresentative)
    {
        $validated = $this->validated($request);

        $salesRepresentative->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('settings.sales-representatives.index')
            ->with('success', __('settings.sales_rep_updated'));
    }

    public function destroy(SalesRepresentative $salesRepresentative)
    {
        if ($salesRepresentative->tenders()->exists()) {
            return back()->with('error', __('settings.sales_rep_delete_blocked'));
        }

        $salesRepresentative->delete();

        return redirect()->route('settings.sales-representatives.index')
            ->with('success', __('settings.sales_rep_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'               => ['required', 'string', 'max:150'],
            'name_en'            => ['nullable', 'string', 'max:150'],
            'phone'              => ['nullable', 'string', 'max:30'],
            'commission_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'status'             => ['boolean'],
        ]);
    }
}
